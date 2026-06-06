<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan {{ $typeLabel }} — Adenia Salsa Badminton Center</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #1e293b; line-height: 1.5; }

        .header { text-align: center; padding: 20px 0 15px; border-bottom: 3px solid #0F1D36; margin-bottom: 20px; }
        .header h1 { font-size: 18px; font-weight: 800; color: #0F1D36; letter-spacing: 1px; }
        .header p { font-size: 10px; color: #64748b; margin-top: 4px; }

        .meta { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .meta-item { font-size: 10px; color: #475569; }
        .meta-item strong { color: #0F1D36; }

        .stats-row { margin-bottom: 20px; }
        .stats-row table { width: 100%; border-collapse: collapse; }
        .stats-row td { text-align: center; padding: 10px 8px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .stats-row .stat-value { font-size: 16px; font-weight: 800; color: #0F1D36; }
        .stats-row .stat-label { font-size: 8px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }

        .section-title { font-size: 12px; font-weight: 700; color: #0F1D36; padding: 8px 0; border-bottom: 1px solid #e2e8f0; margin-bottom: 10px; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data th { background: #0F1D36; color: #ffffff; font-size: 9px; font-weight: 600; text-align: left; padding: 8px 6px; text-transform: uppercase; letter-spacing: 0.3px; }
        table.data td { padding: 6px; font-size: 9px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        table.data tr:nth-child(even) { background: #f8fafc; }
        table.data tr:hover { background: #f1f5f9; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-blue { background: #dbeafe; color: #1e40af; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 8px; color: #94a3b8; padding: 10px 0; border-top: 1px solid #e2e8f0; }
        .footer .page-number:after { content: counter(page); }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .currency { font-variant-numeric: tabular-nums; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>ADENIA SALSA BADMINTON CENTER</h1>
        <p>Laporan {{ $typeLabel }} &mdash; Periode {{ $startDate->format('d/m/Y') }} s.d. {{ $endDate->format('d/m/Y') }}</p>
    </div>

    {{-- Meta Info --}}
    <table style="width: 100%; margin-bottom: 15px;">
        <tr>
            <td style="width: 50%; padding: 0;">
                <span class="meta-item"><strong>Jenis Laporan:</strong> {{ $typeLabel }}</span>
            </td>
            <td style="width: 50%; padding: 0; text-align: right;">
                <span class="meta-item"><strong>Dicetak:</strong> {{ now()->format('d/m/Y H:i') }} WIB</span>
            </td>
        </tr>
    </table>

    {{-- Summary Stats --}}
    @if(!empty($stats))
    <div class="stats-row">
        <table>
            <tr>
                <td>
                    <div class="stat-value">Rp {{ number_format($stats['net_revenue'], 0, ',', '.') }}</div>
                    <div class="stat-label">Pendapatan Bersih</div>
                </td>
                <td>
                    <div class="stat-value">{{ $stats['total_reservations'] }}</div>
                    <div class="stat-label">Total Reservasi</div>
                </td>
                <td>
                    <div class="stat-value">{{ $stats['utilization_rate'] }}%</div>
                    <div class="stat-label">Utilisasi</div>
                </td>
                <td>
                    <div class="stat-value">{{ $stats['payment_success_rate'] }}%</div>
                    <div class="stat-label">Sukses Bayar</div>
                </td>
            </tr>
        </table>
    </div>
    @endif

    {{-- Report Data Table --}}
    <div class="section-title">Detail Data {{ $typeLabel }}</div>

    @if($data->isNotEmpty())
    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                @foreach($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                @foreach($row as $key => $value)
                    <td @if(in_array($key, ['jumlah', 'total', 'total_belanja'])) class="text-right currency" @endif>
                        @if(in_array($key, ['jumlah', 'total', 'total_belanja']))
                            Rp {{ number_format((int) $value, 0, ',', '.') }}
                        @elseif($key === 'status')
                            <span class="badge
                                @if(in_array($value, ['Lunas', 'Selesai', 'Dikonfirmasi'])) badge-green
                                @elseif(in_array($value, ['Menunggu', 'Diajukan'])) badge-amber
                                @elseif(in_array($value, ['Gagal', 'Dibatalkan', 'Ditolak'])) badge-red
                                @else badge-blue @endif
                            ">{{ $value }}</span>
                        @elseif($key === 'rating')
                            {{ str_repeat('⭐', (int) $value) }}
                        @else
                            {{ $value }}
                        @endif
                    </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    <p style="font-size: 9px; color: #94a3b8; text-align: right;">Total: {{ $data->count() }} data</p>
    @else
    <p style="text-align: center; color: #94a3b8; padding: 30px 0;">Tidak ada data untuk periode ini.</p>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Dicetak otomatis oleh Sistem Adenia Salsa Badminton Center &bull; {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
