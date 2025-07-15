<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DailySalesImport implements ToCollection, WithHeadingRow
{
    public $rows; // 让 Service 能拿到资料

    /**
     * 从 Excel 读取的数据
     */
    public function collection(Collection $collection)
    {
        $this->rows = $collection;
    }
}
