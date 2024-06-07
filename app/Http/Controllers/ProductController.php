<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\ProductSupplier;
use App\Supplier;
use App\Tax;
use App\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $products = Product::all();
        $additional = ProductSupplier::all();
        return view('product.index', compact('products', 'additional'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $categories = Category::all();
        $taxes = Tax::all();
        $units = Unit::all();
        return view('product.create', compact('categories', 'taxes', 'units', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|unique:products',
            'serial_number' => 'required',
            'model' => '',
            'category_id' => 'required',
            'sales_price' => '',
            'unit_id' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tax_id' => '',
            'stock_count' => 'required|integer|min:0',
            'purchase_price' => '',
            'purchase_date' => '',
            'description' => '',
        ]);

        $product = new Product($request->all());
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/product/'), $imageName);
            $product->image = $imageName;
        }

        $product->save();

        if ($request->has('supplier_id') && is_array($request->supplier_id)) {
            foreach ($request->supplier_id as $key => $supplier_id) {
                if (isset($request->supplier_price[$key])) {
                    $supplier = new ProductSupplier();
                    $supplier->product_id = $product->id;
                    $supplier->supplier_id = $supplier_id;
                    $supplier->price = $request->supplier_price[$key];
                    $supplier->save();
                }
            }
        }
        return redirect()->back()->with('message', 'New product has been added successfully');
    }

    public function edit($id)
    {
        $productId = $id;
        $additional = ProductSupplier::where('product_id', $productId)->first();

        $product = Product::findOrFail($id);
        $suppliers = Supplier::all();
        $categories = Category::all();
        $taxes = Tax::all();
        $units = Unit::all();
        return view('product.edit', compact('additional', 'suppliers', 'categories', 'taxes', 'units', 'product'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:3|unique:products,name,' . $id,
            'serial_number' => 'required',
            'model' => '',
            'category_id' => 'required',
            'sales_price' => '',
            'unit_id' => 'required',
            'stock_count' => 'required|integer|min:0',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tax_id' => '',
            'supplier_id.*' => 'required|exists:suppliers,id',
            'supplier_price.*' => 'numeric',
            'purchase_price' => '',
            'purchase_date' => '',
            'description' => '',
        ]);

        $product = Product::find($id);
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }

        $product->fill($request->all());

        if ($request->hasFile('image')) {
            $existingImagePath = public_path("images/product/{$product->image}");
            if (file_exists($existingImagePath) && is_file($existingImagePath)) {
                unlink($existingImagePath);
            }
            $imageName = time() . '_' . uniqid() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('images/product/'), $imageName);
            $product->image = $imageName;
        }

        $product->save();

        if ($request->has('supplier_id') && is_array($request->supplier_id)) {
            foreach ($request->supplier_id as $key => $supplier_id) {
                $supplier = ProductSupplier::where('product_id', $product->id)
                    ->where('supplier_id', $supplier_id)
                    ->first();

                if (!$supplier) {
                    $supplier = new ProductSupplier();
                    $supplier->product_id = $product->id;
                    $supplier->supplier_id = $supplier_id;
                }

                if (isset($request->supplier_price[$key])) {
                    $supplier->price = $request->supplier_price[$key];
                }
                
                $supplier->save();
            }
        }

        return redirect()->back()->with('message', 'Product has been updated successfully');
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
        }
        return redirect()->back();
    }
}
