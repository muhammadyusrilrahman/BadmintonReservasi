<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Jobs\SendMaintenanceBroadcastJob;
use App\Models\Court;
use App\Models\MaintenanceBroadcast;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BroadcastMaintenanceController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $broadcasts = MaintenanceBroadcast::with(['sender', 'court'])
            ->latest()
            ->paginate(10);

        return view('admin.broadcast-maintenance.index', [
            'title'      => 'Broadcast Maintenance',
            'broadcasts' => $broadcasts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $courts = Court::active()->get();

        return view('admin.broadcast-maintenance.create', [
            'title'  => 'Buat Broadcast Baru',
            'courts' => $courts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'type'           => 'required|in:system,court',
            'court_id'       => 'required_if:type,court|nullable|exists:courts,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'duration'       => 'required|string|max:100',
            'target_type'    => 'required|in:all,affected',
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
        ], [
            'type.required'           => 'Tipe maintenance wajib dipilih.',
            'court_id.required_if'    => 'Lapangan wajib dipilih untuk tipe maintenance lapangan.',
            'court_id.exists'         => 'Lapangan yang dipilih tidak valid.',
            'scheduled_date.required' => 'Tanggal maintenance wajib diisi.',
            'scheduled_date.date'     => 'Tanggal maintenance harus berupa tanggal yang valid.',
            'scheduled_date.after_or_equal' => 'Tanggal maintenance tidak boleh di masa lampau.',
            'duration.required'       => 'Estimasi durasi wajib diisi.',
            'target_type.required'    => 'Target penerima wajib dipilih.',
            'title.required'          => 'Subjek/Judul email wajib diisi.',
            'description.required'    => 'Isi pesan wajib diisi.',
        ]);

        // Calculate recipient count
        $recipientsCount = 0;
        if ($request->target_type === 'affected') {
            if ($request->type === 'court' && $request->court_id) {
                $recipientsCount = Reservation::where('court_id', $request->court_id)
                    ->whereDate('date', $request->scheduled_date)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->pluck('user_id')
                    ->unique()
                    ->count();
            } else {
                $recipientsCount = Reservation::whereDate('date', $request->scheduled_date)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->pluck('user_id')
                    ->unique()
                    ->count();
            }
        } else {
            $recipientsCount = User::role('customer')->count();
        }

        // Save broadcast record
        $broadcast = MaintenanceBroadcast::create([
            'sender_id'        => auth()->id(),
            'type'             => $request->type,
            'court_id'         => $request->type === 'court' ? $request->court_id : null,
            'title'            => $request->title,
            'description'      => $request->description,
            'scheduled_date'   => $request->scheduled_date,
            'duration'         => $request->duration,
            'target_type'      => $request->target_type,
            'recipients_count' => $recipientsCount,
        ]);

        // Get court name if type is court
        $courtName = null;
        if ($request->type === 'court' && $request->court_id) {
            $courtName = Court::find($request->court_id)?->name;
        }

        // Dispatch background job
        SendMaintenanceBroadcastJob::dispatch([
            'type'           => $request->type,
            'court_id'       => $request->type === 'court' ? $request->court_id : null,
            'court_name'     => $courtName,
            'scheduled_date' => $request->scheduled_date,
            'duration'       => $request->duration,
            'target_type'    => $request->target_type,
            'title'          => $request->title,
            'description'    => $request->description,
        ]);

        return $this->redirectWithSuccess('admin.broadcast-maintenance.index', 'Broadcast berhasil dibuat dan sedang dikirim ke antrean!');
    }
}
