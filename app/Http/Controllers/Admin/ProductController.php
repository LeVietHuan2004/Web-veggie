<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Str;

class ProductController extends Controller
{
    public function showFormAddProduct()
    {
        $categories = Category::all();
        return view('admin.pages.product-add', compact('categories'));
    }

    public function addProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'images.*' => 'image',
        ]);

        $slug = Str::slug($request->name) . '-' . time();

        //Create product

        $product = Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock ?? 0,
            'unit' => $request->unit ?? 'kg',
            'status' => 'in_stock',
        ]);

        // Handle images upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = "uploads/products/" . $imageName;

                $resizedImage = Image::make($image)
                    ->resize(600, 600)
                    ->encode();


                Storage::disk('public')->put($path, $resizedImage);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                ]);
            }
        }

        return redirect()->route('admin.product.add')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function index()
    {
        $products = Product::with('category', 'images')->get();
        $categories = Category::all();
        return view('admin.pages.products', compact('products', 'categories'));
    }

    public function updateProduct(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'images.*' => 'image',
        ]);

        $product = Product::findOrFail($request->id);
        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock ?? 0,
            'unit' => $request->unit ?? 'kg',
        ]);

        // Handle images upload
        if ($request->hasFile('images')) {
            // Delete old images
            $oldImage = ProductImage::where('product_id', $product->id)->get();

            foreach ($oldImage as $image) {
                Storage::disk('public')->delete( $image->image);
            }
            // Delete old image records
            ProductImage::where('product_id', $product->id)->delete();

            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = "uploads/products/" . $imageName;

                $resizedImage = Image::make($image)
                    ->resize(600, 600)
                    ->encode();

                Storage::disk('public')->put($path, $resizedImage);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật sản phẩm thành công!',
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'category_name' => $product->category->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'unit' => $product->unit,
                'status' => $product->status == 'in_stock' ? 'Còn hàng' : 'Hết hàng' ,
                'images' => $product->images->map(fn($img) => asset('storage/' . $img->image)),
            ],
        ]);

    }

    public function deleteProduct(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->id);

        // Delete product images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete( $image->image);
        }

        // Delete product
        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa sản phẩm thành công!',
        ]);
    }
}
