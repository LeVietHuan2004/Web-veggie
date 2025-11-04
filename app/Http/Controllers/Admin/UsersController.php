<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $usersQuery = User::with('role')->latest();

        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        }

        $users = $usersQuery->paginate(9)->appends($request->query());

        return view('admin.pages.users', compact('users', 'search'));
    }

    public function upgrade(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role' => ['required', 'string', 'in:staff,delivery_staff'],
        ]);

        $user = User::find($data['user_id']);
        $targetRole = Role::where('name', $data['role'])->first();

        if (!$targetRole) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy vai trò phù hợp.'
            ]);
        }

        $user->role_id = $targetRole->id;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Đã cập nhật vai trò người dùng thành công.'
        ]);
    }

    public function updateStatus(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'status' => ['required', 'string', 'in:active,pending,banned,deleted'],
        ]);

        $user = User::find($data['user_id']);
        $user->status = $data['status'];
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Trạng thái người dùng đã được cập nhật.'
        ]);
    }
}
