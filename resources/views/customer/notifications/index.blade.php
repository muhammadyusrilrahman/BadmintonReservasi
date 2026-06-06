<x-layouts.app :title="$title">
    <div class="max-w-4xl mx-auto py-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    Kotak Masuk Notifikasi
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Riwayat semua pemberitahuan sistem mengenai transaksi dan reservasi Anda.</p>
            </div>
            @if(auth()->user()->unreadNotifications()->count() > 0)
                <form action="{{ route('customer.notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-650 dark:text-indigo-400 text-sm font-medium rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition duration-200 shadow-sm border border-indigo-100 dark:border-indigo-900/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/85 rounded-2xl overflow-hidden shadow-sm">
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($notifications as $notification)
                    @php
                        $isUnread = is_null($notification->read_at);
                        $type = $notification->data['type'] ?? 'default';
                        $iconMap = [
                            'booking_success' => ['bg-blue-100 text-blue-655 dark:bg-blue-950/50 dark:text-blue-400', '🏸'],
                            'payment_success' => ['bg-emerald-100 text-emerald-655 dark:bg-emerald-950/50 dark:text-emerald-400', '✅'],
                            'payment_failed' => ['bg-rose-100 text-rose-655 dark:bg-rose-950/50 dark:text-rose-400', '❌'],
                            'refund_approved' => ['bg-amber-100 text-amber-655 dark:bg-amber-950/50 dark:text-amber-400', '💰'],
                            'reschedule_approved' => ['bg-violet-100 text-violet-655 dark:bg-violet-950/50 dark:text-violet-400', '🕒'],
                            'schedule_changed' => ['bg-pink-100 text-pink-655 dark:bg-pink-950/50 dark:text-pink-400', '⚠️'],
                        ];
                        $colors = $iconMap[$type] ?? ['bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400', '🔔'];
                    @endphp
                    <div class="p-4 sm:p-5 flex gap-4 transition duration-200 hover:bg-slate-50/50 dark:hover:bg-slate-800/20 {{ $isUnread ? 'bg-indigo-50/20 dark:bg-indigo-950/5' : '' }}">
                        <div class="flex-shrink-0 w-10 h-10 rounded-xl {{ $colors[0] }} flex items-center justify-center text-lg font-bold shadow-inner">
                            {{ $colors[1] }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-semibold {{ $isUnread ? 'text-slate-900 dark:text-white' : 'text-slate-700 dark:text-slate-350' }}">
                                    {{ $notification->data['title'] ?? 'Pemberitahuan Baru' }}
                                </p>
                                <span class="text-xs text-slate-400 dark:text-slate-500 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <div class="flex items-center gap-3 mt-3">
                                @if($notification->data['url'] ?? false)
                                    <a href="{{ route('customer.notifications.read', $notification->id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                        Lihat Detail
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                @endif
                                @if($isUnread)
                                    <form action="{{ route('customer.notifications.read', $notification->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-slate-405 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-400 transition duration-150">
                                            Tandai Dibaca
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Tidak ada notifikasi</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Semua pemberitahuan sistem akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
            
            @if($notifications->hasPages())
                <div class="p-4 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
