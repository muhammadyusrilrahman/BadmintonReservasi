@props([
    'courts',
    'scheduleDate',
    'operationalHours',
    'dashboardRoute',
    'showUserName' => true,
])

@php
    $statusBg = [
        'pending'   => 'background:#fbbf24',
        'confirmed' => 'background:#3b82f6',
        'completed' => 'background:#10b981',
    ];
    $statusLabel = [
        'pending'   => 'Menunggu',
        'confirmed' => 'Dikonfirmasi',
        'completed' => 'Selesai',
    ];
@endphp

<div class="mb-8">
    {{-- Section Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Jadwal Lapangan</h2>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    {{ $scheduleDate->translatedFormat('l, d F Y') }}
                </span>
                @if ($scheduleDate->isToday())
                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-1.5 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Hari Ini
                    </span>
                @elseif ($scheduleDate->isPast())
                    <span class="text-[10px] font-medium text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-full">Lampau</span>
                @else
                    <span class="text-[10px] font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 px-1.5 py-0.5 rounded-full">Mendatang</span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            {{-- Date Navigation --}}
            <form method="GET" action="{{ route($dashboardRoute) }}" class="flex items-center gap-1.5">
                <a href="{{ route($dashboardRoute, ['schedule_date' => $scheduleDate->copy()->subDay()->toDateString()]) }}"
                   class="flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 hover:border-pink-400 hover:text-pink-600 dark:hover:border-pink-500 dark:hover:text-pink-400 transition-all duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>

                <div class="relative">
                    <input type="date" name="schedule_date"
                           value="{{ $scheduleDate->toDateString() }}"
                           onchange="this.form.submit()"
                           class="pl-8 pr-3 py-1.5 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 cursor-pointer transition-all">
                    <svg class="absolute left-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>

                <a href="{{ route($dashboardRoute, ['schedule_date' => $scheduleDate->copy()->addDay()->toDateString()]) }}"
                   class="flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 hover:border-pink-400 hover:text-pink-600 dark:hover:border-pink-500 dark:hover:text-pink-400 transition-all duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                @if (!$scheduleDate->isToday())
                    <a href="{{ route($dashboardRoute) }}"
                       class="flex items-center gap-1 px-2.5 py-1.5 text-[10px] font-medium rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 hover:border-pink-400 hover:text-pink-600 dark:hover:text-pink-400 transition-all duration-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Hari Ini
                    </a>
                @endif
            </form>

            {{-- Legend --}}
            <div class="hidden sm:flex items-center gap-3 ml-2 pl-3 border-l border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm" style="background:#fbbf24"></span><span class="text-[10px] text-slate-400 dark:text-slate-500">Menunggu</span></div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm" style="background:#3b82f6"></span><span class="text-[10px] text-slate-400 dark:text-slate-500">Dikonfirmasi</span></div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm" style="background:#10b981"></span><span class="text-[10px] text-slate-400 dark:text-slate-500">Selesai</span></div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm border border-dashed border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-800"></span><span class="text-[10px] text-slate-400 dark:text-slate-500">Tersedia</span></div>
            </div>
        </div>
    </div>

    {{-- Mobile Legend --}}
    <div class="flex sm:hidden flex-wrap items-center gap-x-4 gap-y-1.5 mb-3 text-[10px]">
        <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm" style="background:#fbbf24"></span><span class="text-slate-400">Menunggu</span></div>
        <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm" style="background:#3b82f6"></span><span class="text-slate-400">Dikonfirmasi</span></div>
        <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm" style="background:#10b981"></span><span class="text-slate-400">Selesai</span></div>
        <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm border border-dashed border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-800"></span><span class="text-slate-400">Tersedia</span></div>
    </div>

    {{-- Schedule Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        @if ($courts->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h18v18H3V3zm9 0v18M3 12h18"/></svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Belum ada lapangan aktif</p>
            </div>
        @else
            {{-- Scrollable wrapper --}}
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800">
                            <th class="sticky left-0 z-20 bg-slate-50 dark:bg-slate-800/80 px-2.5 py-2 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide border-r border-slate-200 dark:border-slate-700" style="min-width:100px;width:100px">
                                Lapangan
                            </th>
                            @foreach ($operationalHours as $hour)
                                <th class="px-0 py-2 text-center text-[10px] font-medium text-slate-400 dark:text-slate-500"
                                    style="min-width:28px;width:28px">
                                    {{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($courts as $court)
                            @php
                                $slotMap = [];
                                foreach ($operationalHours as $h) {
                                    $slotMap[$h] = null;
                                }

                                foreach ($court->reservations as $res) {
                                    $startH = (int) \Carbon\Carbon::parse($res->start_time)->format('H');
                                    $endH   = (int) \Carbon\Carbon::parse($res->end_time)->format('H');
                                    for ($h = $startH; $h < $endH; $h++) {
                                        if (array_key_exists($h, $slotMap)) {
                                            $slotMap[$h] = $res;
                                        }
                                    }
                                }

                                // Build merged slot segments
                                $segments = [];
                                $i = 0;
                                $hours = array_values($operationalHours);
                                while ($i < count($hours)) {
                                    $h = $hours[$i];
                                    $res = $slotMap[$h];
                                    if ($res) {
                                        $span = 1;
                                        while (
                                            $i + $span < count($hours) &&
                                            $slotMap[$hours[$i + $span]] &&
                                            $slotMap[$hours[$i + $span]]->id === $res->id
                                        ) {
                                            $span++;
                                        }
                                        $segments[] = ['hour' => $h, 'span' => $span, 'res' => $res];
                                        $i += $span;
                                    } else {
                                        $segments[] = ['hour' => $h, 'span' => 1, 'res' => null];
                                        $i++;
                                    }
                                }
                            @endphp

                            <tr class="group hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="sticky left-0 z-10 bg-white dark:bg-slate-900 group-hover:bg-slate-50/70 dark:group-hover:bg-slate-800/30 px-2.5 py-1.5 border-r border-slate-200 dark:border-slate-700 transition-colors" style="min-width:100px;width:100px">
                                    <p class="text-[11px] font-semibold text-slate-800 dark:text-white leading-tight truncate">{{ $court->name }}</p>
                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-0.5 truncate">{{ $court->type_label }}</p>
                                </td>

                                @foreach ($segments as $seg)
                                    @if ($seg['res'])
                                        @php $res = $seg['res']; @endphp
                                        <td colspan="{{ $seg['span'] }}" class="px-px py-1 align-middle"
                                            style="min-width:{{ $seg['span'] * 28 }}px">
                                            <button type="button"
                                                    onclick="showSlotPopup(this)"
                                                    class="schedule-slot w-full rounded h-7 cursor-pointer transition-all hover:brightness-110 hover:scale-[1.03] active:scale-100 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-pink-500 dark:focus:ring-offset-slate-900"
                                                    style="{{ $statusBg[$res->status] ?? 'background:#94a3b8' }}"
                                                    data-name="{{ $showUserName ? $res->user->name : '' }}"
                                                    data-time="{{ \Carbon\Carbon::parse($res->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($res->end_time)->format('H:i') }}"
                                                    data-status="{{ $statusLabel[$res->status] ?? ucfirst($res->status) }}"
                                                    data-status-raw="{{ $res->status }}"
                                                    data-code="{{ $showUserName ? '#' . $res->booking_code : '' }}"
                                                    data-price="{{ $showUserName ? 'Rp ' . number_format($res->total_price, 0, ',', '.') : '' }}"
                                                    data-court="{{ $court->name }}"
                                                    data-guest-mode="{{ $showUserName ? '0' : '1' }}">
                                            </button>
                                        </td>
                                    @else
                                        <td class="px-px py-1 align-middle" style="min-width:28px">
                                            <div class="h-7 rounded bg-slate-100 dark:bg-slate-800 border border-dashed border-slate-200 dark:border-slate-700 hover:border-pink-300 dark:hover:border-pink-600 hover:bg-pink-50/30 dark:hover:bg-pink-500/5 transition-all duration-150"></div>
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Summary Footer --}}
            <div class="px-4 py-2.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 flex flex-wrap items-center gap-x-5 gap-y-1 text-[11px] text-slate-500 dark:text-slate-400">
                @php
                    $allRes = $courts->flatMap->reservations;
                    $pending   = $allRes->where('status', 'pending')->count();
                    $confirmed = $allRes->where('status', 'confirmed')->count();
                    $completed = $allRes->where('status', 'completed')->count();
                @endphp
                <span>Total: <strong class="text-slate-700 dark:text-slate-200">{{ $allRes->count() }}</strong></span>
                @if ($pending)
                    <span>Menunggu: <strong class="text-amber-600 dark:text-amber-400">{{ $pending }}</strong></span>
                @endif
                @if ($confirmed)
                    <span>Dikonfirmasi: <strong class="text-blue-600 dark:text-blue-400">{{ $confirmed }}</strong></span>
                @endif
                @if ($completed)
                    <span>Selesai: <strong class="text-emerald-600 dark:text-emerald-400">{{ $completed }}</strong></span>
                @endif
            </div>
        @endif
    </div>
</div>

{{-- Slot Detail Popup --}}
<div id="slotPopup" class="hidden fixed inset-0 z-[999]" onclick="hideSlotPopup(event)">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
    <div id="slotPopupCard" class="absolute bg-white dark:bg-slate-900 rounded-2xl shadow-2xl shadow-black/20 border border-slate-200 dark:border-slate-700 w-72 overflow-hidden transform transition-all duration-200 scale-95 opacity-0"
         style="top:50%;left:50%;transform:translate(-50%,-50%)">
        <div id="slotPopupHeader" class="px-4 py-3 flex items-center justify-between" style="background:#3b82f6">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span id="slotPopupStatus" class="text-xs font-semibold text-white"></span>
            </div>
            <button onclick="hideSlotPopup()" class="w-6 h-6 rounded-md bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-4 space-y-3">
            {{-- Full info (admin/staff/kasir) --}}
            <div id="slotPopupFullInfo">
                <div class="flex items-center gap-3">
                    <div id="slotPopupAvatar" class="w-9 h-9 rounded-full bg-pink-100 dark:bg-pink-500/20 flex items-center justify-center text-pink-600 dark:text-pink-400 font-bold text-sm uppercase flex-shrink-0"></div>
                    <div class="min-w-0">
                        <p id="slotPopupName" class="text-sm font-semibold text-slate-800 dark:text-white truncate"></p>
                        <p id="slotPopupCode" class="text-[10px] text-slate-400 dark:text-slate-500 font-mono"></p>
                    </div>
                </div>
            </div>
            {{-- Guest/customer info --}}
            <div id="slotPopupGuestInfo" class="hidden">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-red-100 dark:bg-red-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-white">Slot Terisi</p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500">Tidak tersedia untuk booking</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-lg px-3 py-2">
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mb-0.5">Lapangan</p>
                    <p id="slotPopupCourt" class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate"></p>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-lg px-3 py-2">
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mb-0.5">Waktu</p>
                    <p id="slotPopupTime" class="text-xs font-semibold text-slate-700 dark:text-slate-200"></p>
                </div>
            </div>
            <div id="slotPopupPriceRow" class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                <span class="text-[11px] text-slate-400 dark:text-slate-500">Total Harga</span>
                <span id="slotPopupPrice" class="text-sm font-bold text-slate-800 dark:text-white"></span>
            </div>
        </div>
    </div>
</div>

<script>
    const statusColors = {
        pending: '#fbbf24',
        confirmed: '#3b82f6',
        completed: '#10b981',
    };

    function showSlotPopup(btn) {
        const popup = document.getElementById('slotPopup');
        const card = document.getElementById('slotPopupCard');
        const header = document.getElementById('slotPopupHeader');
        const isGuest = btn.dataset.guestMode === '1';

        // Toggle full vs guest info sections
        document.getElementById('slotPopupFullInfo').classList.toggle('hidden', isGuest);
        document.getElementById('slotPopupGuestInfo').classList.toggle('hidden', !isGuest);
        document.getElementById('slotPopupPriceRow').classList.toggle('hidden', isGuest);

        if (!isGuest) {
            const name = btn.dataset.name;
            document.getElementById('slotPopupName').textContent = name;
            document.getElementById('slotPopupAvatar').textContent = name.charAt(0);
            document.getElementById('slotPopupCode').textContent = btn.dataset.code;
            document.getElementById('slotPopupPrice').textContent = btn.dataset.price;
        }

        document.getElementById('slotPopupCourt').textContent = btn.dataset.court;
        document.getElementById('slotPopupTime').textContent = btn.dataset.time;
        document.getElementById('slotPopupStatus').textContent = isGuest ? 'Terisi' : btn.dataset.status;

        const raw = btn.dataset.statusRaw;
        header.style.background = isGuest ? '#ef4444' : (statusColors[raw] || '#94a3b8');

        popup.classList.remove('hidden');
        requestAnimationFrame(() => {
            card.style.transform = 'translate(-50%, -50%) scale(1)';
            card.style.opacity = '1';
        });
    }

    function hideSlotPopup(e) {
        if (e && e.target !== e.currentTarget) return;
        const popup = document.getElementById('slotPopup');
        const card = document.getElementById('slotPopupCard');
        card.style.transform = 'translate(-50%, -50%) scale(0.95)';
        card.style.opacity = '0';
        setTimeout(() => popup.classList.add('hidden'), 150);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const popup = document.getElementById('slotPopup');
            if (!popup.classList.contains('hidden')) hideSlotPopup();
        }
    });
</script>
