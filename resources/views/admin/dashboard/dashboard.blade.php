@extends('admin.layout.layout')

@section('page_title', 'Dashboard')

@section('content')

    @if (session('successFiles') || session('failedFiles'))
        <div class="mb-3">
            @if (session('successFiles') && count(session('successFiles')) > 0)
                <div class="alert alert-success">
                    <strong>✅ Successfully Uploaded:</strong>
                    <hr class="my-2">
                    <ul class="mb-0">
                        @foreach (session('successFiles') as $file)
                            <li>{{ $file }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('failedFiles') && count(session('failedFiles')) > 0)
                <div class="alert alert-danger">
                    <strong>❌ Failed to Upload:</strong>
                    <hr class="my-2">
                    @foreach (session('failedFiles') as $fail)
                        <div>
                            <strong>{{ $fail['file'] }}</strong>
                            <ul class="mb-0">
                                @foreach ($fail['errors'] as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <div class="row mb-3">
        <div class="col">
            <h2 class="fw-bold">Dashboard</h2>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 align-items-center float-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importDailySalesModal">
                    <i class="fa-solid fa-upload"></i> Import Daily Sales
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importDailySalesModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Import Daily Sales (.xlsx / .csv)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="import-form" action="{{ route('admin.import_daily_sales') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div id="dropZone" class="border border-2 rounded p-4 text-center mb-3"
                            style="border-style: dashed; background:#f8f9fa; cursor:pointer;">
                            <p class="mb-2"><i class="fa-solid fa-cloud-arrow-up fs-1 text-primary"></i></p>
                            <p class="fw-bold mb-1">Drag & Drop your file here</p>
                            <p class="text-muted small mb-0">or click to select .xlsx / .csv file</p>
                            <input type="file" name="excel_file[]" class="d-none" id="fileInput" accept=".xlsx,.csv"
                                multiple required>
                        </div>
                        <p id="fileName" class="text-center text-muted mb-3" style="display:none;"></p>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa-solid fa-upload me-1"></i> Upload File
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0" style="background-color:#DDFFE7;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">This Month's Revenue</p>
                        <h2 class="fw-bold mb-0 text-success" id="kpi-revenue">RM 0.00</h2>
                    </div>
                    <div class="rounded-circle bg-white p-3 shadow-sm">
                        <i class="fa-solid fa-coins text-success fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0" style="background-color:#FFF3E0;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">This Month's Total Refill Amount</p>
                        <h2 class="fw-bold mb-0 text-warning" id="kpi-refill">RM 0.00</h2>
                    </div>
                    <div class="rounded-circle bg-white p-3 shadow-sm">
                        <i class="fa-solid fa-coins text-warning fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0" style="background-color:#FFE7E7;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Low Stock Items</p>
                        <h2 class="fw-bold mb-0 text-danger" id="kpi-low-stock">0</h2>
                    </div>
                    <div class="rounded-circle bg-white p-3 shadow-sm">
                        <i class="fa-solid fa-exclamation-triangle text-danger fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0" style="background-color:#F5F5F5;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Last Daily Sales Upload</p>
                        <h2 class="fw-bold mb-0 text-secondary" id="kpi-last-update">--</h2>
                    </div>
                    <div class="rounded-circle bg-white p-3 shadow-sm">
                        <i class="fa-solid fa-clock text-secondary fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6 col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light fw-bold">Top 10 Ingredient Consumption (Monthly)</div>
                <div class="card-body chart-container d-flex justify-content-center align-items-center"
                    style="min-height:320px;">
                    <div class="spinner-border text-primary" role="status"></div>
                    <canvas id="ingredientConsumptionChart" class="d-none"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light fw-bold">Sales Trend (Last 7 Days)</div>
                <div class="card-body chart-container d-flex justify-content-center align-items-center"
                    style="min-height:320px;">
                    <div class="spinner-border text-primary" role="status"></div>
                    <canvas id="salesTrendChart" class="d-none"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        @livewire('admin.dashboard.low-stock-ingredient-list')
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('#import-form').validate({
                ignore: [],
                errorElement: 'span',
                errorClass: 'invalid-feedback',
                errorPlacement: function(error, element) {
                    element.closest('.form-group').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                invalidHandler: function() {
                    notifier.show('Error!', 'Please ensure all inputs are correct.', 'warning', '',
                        4000);
                },
            });

            const dropZone = $('#dropZone'),
                fileInput = $('#fileInput'),
                fileName = $('#fileName');
            dropZone.on('click', () => fileInput[0].click());
            fileInput.on('change', function() {
                let names = $.map(this.files, file => file.name);
                fileName.html(names.join('<br>')).show();
            });
            dropZone.on('dragover', e => {
                e.preventDefault();
                dropZone.addClass('bg-light');
            });
            dropZone.on('dragleave', () => dropZone.removeClass('bg-light'));
            dropZone.on('drop', function(e) {
                e.preventDefault();
                dropZone.removeClass('bg-light');
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    fileInput[0].files = files;
                    let names = $.map(files, file => file.name);
                    fileName.html(names.join('<br>')).show();
                }
            });

            $.get("{{ route('admin.stats') }}", stats => {
                $('#kpi-revenue').text('RM ' + (stats.total_revenue ?? 0).toFixed(2));
                $('#kpi-low-stock').text(stats.low_stock_count ?? 0);
                $('#kpi-refill').text('RM ' + (stats.total_refill_amount ?? 0).toFixed(2));
                $('#kpi-last-update').text(stats.last_daily_sales_upload);
            });

            $.get("{{ route('admin.ingredient_consumption') }}", data => {
                const container = $('#ingredientConsumptionChart').parent();
                const canvas = $('#ingredientConsumptionChart');
                container.find('.spinner-border').remove();

                let combined = data.labels.map((label, index) => ({
                    label,
                    value: data.values[index]
                }));
                combined.sort((a, b) => b.value - a.value);
                let top10 = combined.slice(0, 10);
                let finalLabels = top10.map(item => item.label);
                let finalValues = top10.map(item => item.value);

                if (finalValues.length === 0) {
                    container.html(`
                <div class="d-flex flex-column align-items-center justify-content-center" style="height:280px;">
                    <i class="fa-solid fa-chart-pie text-muted" style="font-size:36px;"></i>
                    <p class="mt-3 fw-bold text-muted">No data available</p>
                    <small class="text-muted">Upload daily sales to see ingredient consumption stats.</small>
                </div>
            `);
                    return;
                }

                canvas.removeClass('d-none');
                const ctx = canvas[0].getContext('2d');
                const total = finalValues.reduce((sum, v) => sum + v, 0);

                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: finalLabels,
                        datasets: [{
                            data: finalValues,
                            backgroundColor: generateColors(finalLabels.length),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 14,
                                    padding: 10,
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: context => {
                                        const value = context.parsed;
                                        const percentage = parseFloat(((value / total) * 100)
                                            .toFixed(2));
                                        return `${percentage}%`;
                                    }
                                }
                            }
                        }
                    }
                });
            });

            $.get("{{ route('admin.sales_trend') }}", data => {
                const container = $('#salesTrendChart').parent();
                const canvas = $('#salesTrendChart');
                container.find('.spinner-border').remove();

                canvas.removeClass('d-none');
                const ctx = canvas[0].getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(54,162,235,0.3)');
                gradient.addColorStop(1, 'rgba(54,162,235,0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Sales (RM)',
                            data: data.values,
                            borderColor: '#36A2EB',
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            pointBackgroundColor: '#36A2EB',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: context =>
                                        `RM ${parseFloat(context.parsed.y).toFixed(2)}`
                                }
                            }
                        }
                    }
                });
            });

            function generateColors(count) {
                return Array.from({
                        length: count
                    }, (_, i) =>
                    `hsl(${Math.floor((i * 360) / count)}, 65%, 65%)`
                );
            }
        });
    </script>
@endsection
