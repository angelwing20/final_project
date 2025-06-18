<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductAdminController extends Controller
{
    public function index()
    {
        return view('admin.product.index'); // 记得view文件也跟着放到 resources/views/admin/product/index.blade.php
    }

    public function store(Request $request)
    {
        // 验证数据
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // 处理图片上传
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('product_images', 'public');
            $validated['image_path'] = $path;
        }

        // 目前我们先测试用 dd 查看接收到的数据
        dd($validated);
    }

    public function show($id)
    {
        // 暂不处理
    }

    public function edit($id)
    {
        // 暂不处理
    }

    public function update(Request $request, $id)
    {
        // 暂不处理
    }

    public function destroy($id)
    {
        // 暂不处理
    }
}
