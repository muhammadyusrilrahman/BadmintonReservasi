<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\BaseController;
use App\Models\Court;
use App\Models\CourtMaintenance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MaintenanceController extends BaseController
{
    /**
     * Display listing of maintenance records for staff.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $courtId = $request->input('court_id');

        $query = CourtMaintenance::with(['court', 'staff'])
            ->orderByRaw("CASE WHEN status = 'in_progress' THEN 0 WHEN status = 'scheduled' THEN 1 ELSE 2 END")
            ->orderBy('scheduled_date', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($courtId) {
            $query->where('court_id', $courtId);
        }

        $maintenances = $query->paginate(15)->withQueryString();

        $courts = Court::orderBy('name')->get();

        // Stats
        $scheduledCount = CourtMaintenance::where('status', 'scheduled')->count();
        $inProgressCount = CourtMaintenance::where('status', 'in_progress')->count();
        $completedCount = CourtMaintenance::where('status', 'completed')->count();

        return view('staff.maintenance.index', [
            'title'           => 'Maintenance Lapangan',
            'maintenances'    => $maintenances,
            'courts'          => $courts,
            'selectedStatus'  => $status,
            'selectedCourt'   => $courtId,
            'scheduledCount'  => $scheduledCount,
            'inProgressCount' => $inProgressCount,
            'completedCount'  => $completedCount,
        ]);
    }

    /**
     * Show form to create new maintenance record.
     */
    public function create()
    {
        $courts = Court::orderBy('name')->get();

        return view('staff.maintenance.create', [
            'title'  => 'Buat Jadwal Maintenance',
            'courts' => $courts,
        ]);
    }

    /**
     * Store a new maintenance record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'court_id'       => 'required|exists:courts,id',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string|max:1000',
            'scheduled_date' => 'required|date|after_or_equal:today',
        ], [
            'court_id.required'       => 'Lapangan harus dipilih.',
            'title.required'          => 'Judul maintenance harus diisi.',
            'scheduled_date.required' => 'Tanggal jadwal harus diisi.',
            'scheduled_date.after_or_equal' => 'Tanggal tidak boleh kurang dari hari ini.',
        ]);

        $validated['staff_id'] = auth()->id();
        $validated['status'] = 'scheduled';

        CourtMaintenance::create($validated);

        return redirect()
            ->route('staff.maintenance.index')
            ->with('success', 'Jadwal maintenance berhasil dibuat.');
    }

    /**
     * Show a specific maintenance record.
     */
    public function show(CourtMaintenance $maintenance)
    {
        $maintenance->load(['court', 'staff']);

        return view('staff.maintenance.show', [
            'title'       => 'Detail Maintenance',
            'maintenance' => $maintenance,
        ]);
    }

    /**
     * Update maintenance status (start progress / mark complete).
     */
    public function updateStatus(Request $request, CourtMaintenance $maintenance)
    {
        $newStatus = $request->input('status');

        $allowedTransitions = [
            'scheduled'   => ['in_progress'],
            'in_progress' => ['completed'],
        ];

        $allowed = $allowedTransitions[$maintenance->status] ?? [];

        if (!in_array($newStatus, $allowed)) {
            return back()->with('error', 'Perubahan status tidak diizinkan.');
        }

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'completed') {
            $updateData['completed_at'] = now();
        }

        $maintenance->update($updateData);

        $statusLabels = CourtMaintenance::STATUS_LABELS;
        $label = $statusLabels[$newStatus] ?? $newStatus;

        return back()->with('success', "Status maintenance berhasil diubah menjadi \"{$label}\".");
    }
}
