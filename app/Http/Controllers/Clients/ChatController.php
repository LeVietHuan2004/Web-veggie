<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Str;

class ChatController extends Controller
{
    // Get history (user or guest)
    public function fetchMessages(Request $request)
    {
        if (Auth::check()) {
            $msgs = ChatMessage::where('user_id', Auth::id())->orderBy('created_at')->get();
        } else {
            $token = $request->cookie('chat_token');
            $msgs = $token ? ChatMessage::where('guest_token', $token)->orderBy('created_at')->get() : collect();
        }
        return response()->json($msgs);
    }

    // Send message (save user message, call AI, save bot reply)
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userId = Auth::id();

        // --- Handler guest token (cookie) ---
        $guestToken = null;
        if (!$userId) {
            $guestToken = $request->cookie('chat_token');
            if (!$guestToken) {
                $guestToken = 'guest_' . Str::random(32);
                // queue cookie to return to client (180 days)
                cookie()->queue(cookie('chat_token', $guestToken, 60 * 24 * 180));
            }
        }

        // 1) Save message user to DB
        $userMsg = ChatMessage::create([
            'user_id' => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'sender' => 'user',
            'message' => $request->message,
        ]);

        // 2) Prepare prompt
        $products = Product::where('stock','>', 0)->get(['name', 'price', 'unit', 'description'])->map(function ($p) {
            return "{$p->name} - {$p->price} / {$p->unit}";
        })->toArray();
        $productList = implode("\n", $products);

        $prompt = "Bạn là trợ lý bán hàng cho website rau củ. Dưới đây là danh sách một số sản phẩm hiện có:\n$productList\n
                    Hãy trả lời ngắn gọn, trung thực, chỉ dùng thông tin trong danh sách sản phẩm nếu cần.";

        // Get history lasted (Exp: 6 messages ~ 3 turns user-bot)
        $history = ChatMessage::query()
            ->where(function ($q) use ($userId, $guestToken) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('guest_token', $guestToken);
                }
            })
            ->latest()
            ->limit(6)
            ->orderBy('created_at', 'asc')
            ->get();

        // Change history to suit with format Gemini
        $contents = [];
        foreach ($history as $msg) {
            $contents[] = [
                "role" => $msg->sender === 'user' ? "user" : "model",
                "parts" => [["text" => $msg->message]]
            ];
        }

        // Append new message of user
        $contents[] = [
            "role" => "user",
            "parts" => [["text" => $request->message]]
        ];

        
        // 3) Call AI (Gemini) — if haven't GOOGLE_GEMINI_API_KEY return fallback text
        $aiReplyText = "Xin lỗi, hiện tại AI chưa được cấu hình.";
        
        if (env('GOOGLE_GEMINI_API_KEY')) {
            try {
                $url_apikey = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
                $payload = [
                    "systemInstruction" => [
                        "parts" => [
                            ["text" => $prompt]
                        ]
                    ],
                    "contents" => $contents
                ];

                // Call API Gemini
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Goog-Api-Key' => env('GOOGLE_GEMINI_API_KEY'),
                ])->post($url_apikey, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $aiReplyText = $data['candidates'][0]['content']['parts'][0]['text']
                        ?? "Xin lỗi, tôi chưa hiểu câu hỏi.";
                } else {
                    $aiReplyText = "Xin lỗi, AI không thể xử lý lúc này.";
                    \Log::error('AI API error', ['response' => $response->json()]);
                }

            } catch (\Throwable $e) {
                \Log::error('AI call error: ' . $e->getMessage());
                $aiReplyText = "Xin lỗi, hiện tại không thể kết nối AI.";
            }
        }

        // 4) Save bot reply
        $botMsg = ChatMessage::create([
            'user_id' => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'sender' => 'bot',
            'message' => $aiReplyText,
        ]);

        // 5) Return 2 message created (client append)
        return response()->json([
            'user' => $userMsg,
            'bot' => $botMsg,
        ]);
    }
}
