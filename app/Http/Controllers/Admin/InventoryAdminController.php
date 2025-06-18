<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryAdminController extends Controller
{
    // 显示库存列表
    public function index()
    {
        // 暂时用假数据代替数据库
        $inventories = [
            [
                'id' => 1,
                'product_name' => 'Example Product A',
                'quantity' => 50,
                'location' => 'Warehouse 1',
                'last_updated' => '2025-06-17 10:00:00'
            ],
            [
                'id' => 2,
                'product_name' => 'Example Product B',
                'quantity' => 20,
                'location' => 'Warehouse 2',
                'last_updated' => '2025-06-16 15:30:00'
            ],
        ];

        return view('admin.inventory.index', compact('inventories'));
    }

    // 新增库存
    public function store(Request $request)
    {
        // 这里只做模拟，实际没有存储动作
        return redirect()->route('admin.inventory.index')->with('success', '模拟：库存已新增');
    }

    // 编辑库存（显示编辑页面）
    public function edit($id)
    {
        // 模拟编辑数据
        $inventory = [
            'id' => $id,
            'product_name' => 'Example Product A',
            'quantity' => 50,
            'location' => 'Warehouse 1',
            'last_updated' => '2025-06-17 10:00:00'
        ];

        return view('admin.inventory.edit', compact('inventory'));
    }

    // 更新库存
    public function update(Request $request, $id)
    {
        // 模拟更新，无实际动作
        return redirect()->route('admin.inventory.index')->with('success', '模拟：库存已更新');
    }

    // 删除库存
    public function destroy($id)
    {
        // 模拟删除，无实际动作
        return redirect()->route('admin.inventory.index')->with('success', '模拟：库存已删除');
    }
}
