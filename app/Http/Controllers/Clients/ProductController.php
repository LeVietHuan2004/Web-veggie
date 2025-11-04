<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::with('products')->get();
        $products = Product::with('firstImage')->where('status', 'in_stock')->where('stock', '>', 0)->paginate(9);
        $productsHighRate = Product::withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating')->limit(2)->get();

        return view('clients.pages.products', compact('categories', 'products', 'productsHighRate'));
    }

    public function filter(Request $request)
    {
        $query = Product::query();

        //Filter Category if exist
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        //Filter Price if exist
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        //Filter SortBy if exist
        if ($request->has('sort_by')) {
            switch ($request->sort_by) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'latest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('id', 'desc');
                    break;
            }
        }

        $products = $query->paginate(9);

        return response()->json([
            'products' => view('clients.components.products_grid', compact('products'))->render(),
            'pagination' => $products->links('clients.components.pagination.pagination_custom')->render()
        ]);
    }

    public function detail($slug)
    {
        $product = Product::with(['category', 'images', 'reviews.user'])->where('slug', $slug)->firstOrFail();

        //Get products in the same category
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(6)
            ->get();

        // Call API on Python to get related Products
        try {
            $apiUrl = 'http://127.0.0.1:5555/api/product-recommendation';
            $response = Http::get($apiUrl, [
                'product_id' => $product->id
            ]);

            if ($response->successful()) {
                $listId = $response->json('related_products');
                
                $relatedProducts = Product::whereIn('id', $listId)->get();
            }
        } catch (\Exception $e) {
            \Log::error('Error when call API: ' . $e->getMessage());
        }

        //Calculate average rating, ensure no null
        $averageRating = round($product->reviews()->avg('rating') ?? 0, 1);

        $hasPurchased = false;
        $hasReviewed = false;

        if (Auth::check()) {
            $user = Auth::user();

            $hasPurchased = OrderItem::whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('status', 'completed');
            })->where('product_id', $product->id)->exists();

            $hasReviewed = Review::where('user_id', $user->id)->where('product_id', $product->id)->exists();

        }

        return view('clients.pages.product-detail', compact('product', 'relatedProducts', 'hasPurchased', 'hasReviewed', 'averageRating'));
    }
}
