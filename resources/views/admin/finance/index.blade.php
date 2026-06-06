<x-layouts.app :title="$title ?? 'Keuangan'">
    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Keuangan 💰</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Analisa pendapatan, pengeluaran & performa bisnis.</p>
            </div>
        </div>
    </div>

    {{-- Date Filter --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 mb-8">
        <form method="GET" action="{{ route('admin.finance.index') }}" class="flex flex-col sm:flex-row items-end gap-3">
            <div class="w-full sm:w-44">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                       class="w-full h-[42px] rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3">
            </div>
            <div class="w-full sm:w-44">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                       class="w-full h-[42px] rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3">
            </div>
            <div class="w-full sm:w-36">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Periode</label>
                <select name="period"
                        class="w-full h-[42px] rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3">
                    <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Harian</option>
                    <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                    <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>
            <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 h-[42px] px-5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm shadow-blue-600/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Terapkan
            </button>
        </form>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Pendapatan Kotor --}}
        <div class="group relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 hover:shadow-lg hover:shadow-emerald-200/30 dark:hover:shadow-slate-900/50 transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Masuk</span>
                </div>
                <p class="text-xl font-extrabold text-slate-800 dark:text-white tabular-nums">Rp {{ number_format($stats['gross_revenue'], 0, ',', '.') }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pendapatan Kotor</p>
            </div>
        </div>

        {{-- Total Refunded --}}
        <div class="group relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 hover:shadow-lg hover:shadow-red-200/30 dark:hover:shadow-slate-900/50 transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-red-600 dark:text-red-400">Keluar</span>
                </div>
                <p class="text-xl font-extrabold text-slate-800 dark:text-white tabular-nums">Rp {{ number_format($stats['total_refunded'], 0, ',', '.') }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Total Refund</p>
            </div>
        </div>

        {{-- Net Revenue --}}
        <div class="group relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 hover:shadow-lg hover:shadow-blue-200/30 dark:hover:shadow-slate-900/50 transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Bersih</span>
                </div>
                <p class="text-xl font-extrabold text-slate-800 dark:text-white tabular-nums">Rp {{ number_format($stats['net_revenue'], 0, ',', '.') }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pendapatan Bersih</p>
            </div>
        </div>

        {{-- Payment Success Rate --}}
        <div class="group relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 hover:shadow-lg hover:shadow-violet-200/30 dark:hover:shadow-slate-900/50 transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-100 dark:bg-violet-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-xl font-extrabold text-slate-800 dark:text-white">{{ $stats['payment_success_rate'] }}%</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Sukses Pembayaran</p>
            </div>
        </div>
    </div>

    {{-- Charts Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Revenue Trend --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white">Tren Pendapatan</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pendapatan kotor per {{ $period === 'daily' ? 'hari' : ($period === 'weekly' ? 'minggu' : 'bulan') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Pendapatan</span>
                </div>
            </div>
            <div class="relative" style="height: 300px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Court Occupancy --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-white">Okupansi Lapangan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Jumlah booking per lapangan</p>
            </div>
            <div class="relative" style="height: 280px;">
                <canvas id="courtChart"></canvas>
            </div>
        </div>

        {{-- Payment Methods --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-white">Metode Pembayaran</h3>
                <p class="text-xs text-slate-400 mt-0.5">Distribusi metode bayar</p>
            </div>
            <div class="relative flex items-center justify-center" style="height: 280px;">
                <canvas id="paymentChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Transaksi Masuk & Refund Side by Side --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Transaksi Masuk Terakhir --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white">💳 Uang Masuk Terakhir</h3>
                    <p class="text-xs text-slate-400 mt-0.5">10 transaksi pembayaran terbaru</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                    {{ $recentTransactions->count() }} data
                </span>
            </div>
            <div class="overflow-x-auto">
                @if($recentTransactions->isNotEmpty())
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Tanggal</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Customer</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Jumlah</th>
                            <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($recentTransactions as $trx)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-2.5 text-slate-500 dark:text-slate-400 text-xs">{{ $trx['tanggal'] }}</td>
                            <td class="px-4 py-2.5 text-slate-700 dark:text-slate-300 font-medium text-xs">{{ Str::limit($trx['customer'], 18) }}</td>
                            <td class="px-4 py-2.5 text-right tabular-nums font-semibold text-emerald-600 dark:text-emerald-400 text-xs">+Rp {{ number_format($trx['jumlah'], 0, ',', '.') }}</td>
                            <td class="px-4 py-2.5 text-center">
                                @php
                                    $color = match($trx['status']) {
                                        'Lunas' => 'emerald',
                                        'Menunggu' => 'amber',
                                        default => 'red',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-500/10 dark:text-{{ $color }}-400">{{ $trx['status'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="py-10 text-center text-slate-400 dark:text-slate-500 text-sm">Tidak ada transaksi masuk.</div>
                @endif
            </div>
        </div>

        {{-- Refund / Uang Keluar --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white">🔄 Refund / Uang Keluar</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pengajuan refund & pengembalian dana</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400">
                    {{ $refundSummary->count() }} data
                </span>
            </div>
            <div class="overflow-x-auto">
                @if($refundSummary->isNotEmpty())
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Tanggal</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Customer</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Jumlah</th>
                            <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($refundSummary->take(10) as $refund)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-2.5 text-slate-500 dark:text-slate-400 text-xs">{{ $refund['tanggal'] }}</td>
                            <td class="px-4 py-2.5 text-slate-700 dark:text-slate-300 font-medium text-xs">{{ Str::limit($refund['customer'], 18) }}</td>
                            <td class="px-4 py-2.5 text-right tabular-nums font-semibold text-red-600 dark:text-red-400 text-xs">-Rp {{ number_format($refund['jumlah'], 0, ',', '.') }}</td>
                            <td class="px-4 py-2.5 text-center">
                                @php
                                    $color = match($refund['status']) {
                                        'Selesai' => 'emerald',
                                        'Disetujui' => 'blue',
                                        'Diajukan', 'Menunggu' => 'amber',
                                        default => 'red',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-500/10 dark:text-{{ $color }}-400">{{ $refund['status'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="py-10 text-center text-slate-400 dark:text-slate-500 text-sm">Tidak ada data refund.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Info Footer --}}
    <div class="text-center text-xs text-slate-400 dark:text-slate-500 py-4">
        Periode data: {{ $startDate->format('d/m/Y') }} — {{ $endDate->format('d/m/Y') }} &bull;
        <a href="{{ route('admin.reports.index') }}" class="text-blue-500 hover:text-blue-600 hover:underline">Lihat laporan detail →</a>
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(148,163,184,0.1)' : 'rgba(226,232,240,0.8)';
            const textColor = isDark ? '#94a3b8' : '#64748b';

            Chart.defaults.font.family = "'Inter', 'system-ui', '-apple-system', sans-serif";
            Chart.defaults.font.size = 11;
            Chart.defaults.color = textColor;

            // ── Revenue Trend Chart ──
            const revenueTrend = @json($revenueTrend);
            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: revenueTrend.labels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: revenueTrend.data,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDark ? '#1e293b' : '#0f172a',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: { weight: '600' },
                            callbacks: {
                                label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { maxRotation: 45, maxTicksLimit: 15 }
                        },
                        y: {
                            grid: { color: gridColor },
                            ticks: {
                                callback: v => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(v)
                            }
                        }
                    }
                }
            });

            // ── Court Occupancy Chart ──
            const courtOccupancy = @json($courtOccupancy);
            new Chart(document.getElementById('courtChart'), {
                type: 'bar',
                data: {
                    labels: courtOccupancy.labels,
                    datasets: [{
                        label: 'Jumlah Booking',
                        data: courtOccupancy.bookings,
                        backgroundColor: [
                            'rgba(59,130,246,0.7)', 'rgba(168,85,247,0.7)',
                            'rgba(236,72,153,0.7)', 'rgba(6,182,212,0.7)',
                            'rgba(245,158,11,0.7)', 'rgba(16,185,129,0.7)',
                        ],
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDark ? '#1e293b' : '#0f172a',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                afterLabel: function(ctx) {
                                    return courtOccupancy.hours[ctx.dataIndex] + ' jam total';
                                }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            grid: { color: gridColor },
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

            // ── Payment Methods Chart ──
            const paymentMethods = @json($paymentMethods);
            new Chart(document.getElementById('paymentChart'), {
                type: 'doughnut',
                data: {
                    labels: paymentMethods.labels,
                    datasets: [{
                        data: paymentMethods.data,
                        backgroundColor: [
                            '#3b82f6', '#8b5cf6', '#ec4899',
                            '#06b6d4', '#f59e0b', '#10b981',
                            '#f43f5e', '#6366f1',
                        ],
                        borderWidth: 0,
                        hoverOffset: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 16,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            backgroundColor: isDark ? '#1e293b' : '#0f172a',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                afterLabel: function(ctx) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(paymentMethods.amounts[ctx.dataIndex]);
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-layouts.app>
