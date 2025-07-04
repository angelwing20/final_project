@extends('admin/layout/layout')

@section('page_title', 'Dashboard')

@section('content')

    <div class="container-fluid">
        <h1 class="h3 mb-4">Dashboard</h1>
        @if ($lowStockIngredients->isNotEmpty())
            <div class="alert alert-warning">
                <h4>Low Stock Warning</h4>
                <ul>
                    @foreach ($lowStockIngredients as $ingredient)
                        <li>{{ $ingredient->name }} - Current: {{ $ingredient->weight }}kg, Alarm: {{ $ingredient->alarm_weight }}kg</li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="alert alert-success">No low stock items.</div>
        @endif
        <!-- 其他仪表盘内容 -->
    </div>

@endsection

@section('script')
    <script>
        $(function() {

        })
    </script>
@endsection
