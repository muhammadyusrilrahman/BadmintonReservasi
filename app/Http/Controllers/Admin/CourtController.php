<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Court\StoreCourtRequest;
use App\Http\Requests\Court\UpdateCourtRequest;
use App\Models\Court;
use App\Services\CourtService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourtController extends BaseController
{
    public function __construct(private readonly CourtService $courtService)
    {
    }

    /**
     * List all courts with search and filter.
     */
    public function index(Request $request): View
    {
        $courts = $this->courtService->getPaginatedFiltered(
            perPage: 10,
            search:   $request->string('search')->trim()->value() ?: null,
            type:     $request->input('type') ?: null,
            isActive: match($request->input('status')) {
                'active'   => true,
                'inactive' => false,
                default    => null,
            },
        );

        return view('admin.courts.index', [
            'title'     => 'Kelola Lapangan',
            'courts'    => $courts,
            'typeLabels' => Court::TYPE_LABELS,
        ]);
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('admin.courts.create', [
            'title'      => 'Tambah Lapangan',
            'typeLabels' => Court::TYPE_LABELS,
        ]);
    }

    /**
     * Store new court.
     */
    public function store(StoreCourtRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Pass UploadedFile directly — service handles storage
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');
        }

        $this->courtService->create($data);

        return $this->redirectWithSuccess('admin.courts.index', 'Lapangan berhasil ditambahkan!');
    }

    /**
     * Show edit form.
     */
    public function edit(Court $court): View
    {
        return view('admin.courts.edit', [
            'title'      => 'Edit Lapangan',
            'court'      => $court,
            'typeLabels' => Court::TYPE_LABELS,
        ]);
    }

    /**
     * Update court.
     */
    public function update(UpdateCourtRequest $request, Court $court): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');
        }

        $this->courtService->update($court->id, $data);

        return $this->redirectWithSuccess('admin.courts.index', 'Lapangan berhasil diperbarui!');
    }

    /**
     * Delete court (guard: reject if has active reservations).
     */
    public function destroy(Court $court): RedirectResponse
    {
        $hasActive = $court->reservations()
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasActive) {
            return $this->backWithError('Lapangan tidak dapat dihapus karena masih memiliki reservasi aktif.');
        }

        $this->courtService->delete($court->id);

        return $this->redirectWithSuccess('admin.courts.index', 'Lapangan berhasil dihapus!');
    }

    /**
     * Toggle active/inactive status via AJAX-friendly POST.
     */
    public function toggleActive(Court $court): RedirectResponse
    {
        $this->courtService->toggleActive($court->id);

        $status = $court->fresh()->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return $this->backWithSuccess("Lapangan \"{$court->name}\" berhasil {$status}.");
    }
}
