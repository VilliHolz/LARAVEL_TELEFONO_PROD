@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Reporte Gráfico</h5>
            <hr>
            <div class="row">
                <!-- Tarjeta para Ventas -->
                <div class="col-md-4 stretch-card grid-margin">
                    <div class="card bg-gradient-danger card-img-holder text-white">
                        <div class="card-body">
                            <h4 class="font-weight-normal mb-3">Ventas <i class="mdi mdi-cash mdi-24px float-right"></i>
                            </h4>
                            <h2>$ {{ number_format($totalSales, 2) }}</h2>
                        </div>
                    </div>
                </div>
                <!-- Tarjeta para Compras -->
                <div class="col-md-4 stretch-card grid-margin">
                    <div class="card bg-gradient-info card-img-holder text-white">
                        <div class="card-body">
                            <h4 class="font-weight-normal mb-3">Compras <i
                                    class="mdi mdi-cart-outline mdi-24px float-right"></i>
                            </h4>
                            <h2>$ {{ number_format($totalPurchases, 2) }}</h2>
                        </div>
                    </div>
                </div>
                <!-- Tarjeta para Reparaciones -->
                <div class="col-md-4 stretch-card grid-margin">
                    <div class="card bg-gradient-success card-img-holder text-white">
                        <div class="card-body">
                            <h4 class="font-weight-normal mb-3">Reparaciones <i
                                    class="mdi mdi-wrench mdi-24px float-right"></i>
                            </h4>
                            <h2>$ {{ number_format($totalRepairs, 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="chart-container">
                        <canvas id="monthlyReportChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/admin/vendors/chart.js/Chart.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Datos pasados desde el backend
            const months = @json($months);
            const sales = @json($sales);
            const purchases = @json($purchases);
            const repairs = @json($repairs);

            const ctx = document.getElementById('monthlyReportChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                            label: 'Ventas',
                            data: sales,
                            backgroundColor: 'rgba(233, 30, 99, 0.7)', // Red (bg-gradient-danger)
                            borderColor: 'rgba(233, 30, 99, 1)', // Red
                            borderWidth: 1
                        },
                        {
                            label: 'Compras',
                            data: purchases,
                            backgroundColor: 'rgba(33, 150, 243, 0.7)', // Blue (bg-gradient-info)
                            borderColor: 'rgba(33, 150, 243, 1)', // Blue
                            borderWidth: 1
                        },
                        {
                            label: 'Reparaciones',
                            data: repairs,
                            backgroundColor: 'rgba(76, 175, 80, 0.7)', // Green (bg-gradient-success)
                            borderColor: 'rgba(76, 175, 80, 1)', // Green
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.dataset.label + ': $' + tooltipItem.raw
                                        .toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
