<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\ProductAdminService;
use Illuminate\Support\Facades\Validator;

class ProductAdminController extends Controller
{
    private $_productAdminService;

    public function __construct(ProductAdminService $productAdminService)
    {
        $this->_productAdminService = $productAdminService;
    }

    public function index()
    {
        return view('admin.product.index');
    }

    public function store(Request $request)
{
    $data = $request->only([
        'product_category_id',
        'name',
        'price',
        'description',
    ]);

    // Validate input
    $validator = Validator::make($data + ['image' => $request->file('image')], [
        'product_category_id' => 'required|exists:product_categories,id',
        'name' => 'required|string|max:255',
        'price' => 'required|string',
        'description' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    // Handle image if uploaded
    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('product_images', 'public');
    }

    // Create product using service
    $product = $this->_productAdminService->createProduct($data);

    if (!$product) {
        $error = implode("<br>", $this->_productAdminService->_errorMessage);
        return back()->with('error', $error)->withInput();
    }

    return redirect()->route('admin.product.index')->with('success', 'Product created successfully!');
}


    public function show($id)
    {
        $product = $this->_productAdminService->getById($id);

        if (!$product) {
            $error = implode("<br>", $this->_productAdminService->_errorMessage);
            return back()->with('error', $error);
        }

        return view('admin.product.show', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('product_images', 'public');
        }

        $updated = $this->_productAdminService->update($id, $validated);

        if (!$updated) {
            $error = implode("<br>", $this->_productAdminService->_errorMessage);
            return back()->with('error', $error)->withInput();
        }

        return back()->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $deleted = $this->_productAdminService->deleteById($id);

        if (!$deleted) {
            $error = implode("<br>", $this->_productAdminService->_errorMessage);
            return back()->with('error', $error);
        }

        return redirect()->route('admin.product.index')->with('success', 'Product deleted successfully!');
    }
}
