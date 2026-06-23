<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\PromoCode;
use App\Services\PromoCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class PromoCodeController extends BaseController
{
    public function __construct(private readonly PromoCodeService $promoCodeService)
    {
    }

    /**
     * List all promo codes with search and filter.
     */
    public function index(Request $request): View
    {
        $promos = $this->promoCodeService->getPaginatedFiltered(
            perPage: 10,
            search: $request->string('search')->trim()->value() ?: null,
            status: $request->input('status') ?: null,
        );

        return view('admin.promos.index', [
            'title'  => 'Kelola Promo',
            'promos' => $promos,
        ]);
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('admin.promos.create', [
            'title' => 'Tambah Promo',
        ]);
    }

    /**
     * Store new promo code.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code'             => ['required', 'string', 'max:50', 'unique:promo_codes,code'],
            'description'      => ['nullable', 'string', 'max:500'],
            'discount_type'    => ['required', 'in:percentage,fixed'],
            'discount_value'   => ['required', 'integer', 'min:1'],
            'max_discount'     => ['nullable', 'integer', 'min:0'],
            'valid_from'       => ['required', 'date'],
            'valid_until'      => ['required', 'date', 'after:valid_from'],
            'max_usage'        => ['nullable', 'integer', 'min:1'],
            'activation_mode'  => ['required', 'in:manual,auto'],
            'is_active'        => ['sometimes', 'boolean'],
        ], [], [
            'code'             => 'kode promo',
            'description'      => 'deskripsi',
            'discount_type'    => 'tipe diskon',
            'discount_value'   => 'nilai diskon',
            'max_discount'     => 'maks diskon',
            'valid_from'       => 'berlaku dari',
            'valid_until'      => 'berlaku sampai',
            'max_usage'        => 'maks penggunaan',
            'activation_mode'  => 'mode aktivasi',
        ]);

        // Normalize
        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active');

        // Auto-mode: determine is_active based on date range
        if ($data['activation_mode'] === 'auto') {
            $data['is_active'] = now()->gte($data['valid_from']) && now()->lte($data['valid_until']);
        }

        try {
            $this->promoCodeService->create($data);
            return $this->redirectWithSuccess('admin.promos.index', 'Kode promo berhasil ditambahkan!');
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage())->withInput();
        }
    }

    /**
     * Show edit form.
     */
    public function edit(PromoCode $promo): View
    {
        return view('admin.promos.edit', [
            'title' => 'Edit Promo',
            'promo' => $promo,
        ]);
    }

    /**
     * Update promo code.
     */
    public function update(Request $request, PromoCode $promo): RedirectResponse
    {
        $data = $request->validate([
            'code'             => ['required', 'string', 'max:50', 'unique:promo_codes,code,' . $promo->id],
            'description'      => ['nullable', 'string', 'max:500'],
            'discount_type'    => ['required', 'in:percentage,fixed'],
            'discount_value'   => ['required', 'integer', 'min:1'],
            'max_discount'     => ['nullable', 'integer', 'min:0'],
            'valid_from'       => ['required', 'date'],
            'valid_until'      => ['required', 'date', 'after:valid_from'],
            'max_usage'        => ['nullable', 'integer', 'min:1'],
            'activation_mode'  => ['required', 'in:manual,auto'],
            'is_active'        => ['sometimes', 'boolean'],
        ], [], [
            'code'             => 'kode promo',
            'description'      => 'deskripsi',
            'discount_type'    => 'tipe diskon',
            'discount_value'   => 'nilai diskon',
            'max_discount'     => 'maks diskon',
            'valid_from'       => 'berlaku dari',
            'valid_until'      => 'berlaku sampai',
            'max_usage'        => 'maks penggunaan',
            'activation_mode'  => 'mode aktivasi',
        ]);

        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active');

        if ($data['activation_mode'] === 'auto') {
            $data['is_active'] = now()->gte($data['valid_from']) && now()->lte($data['valid_until']);
        }

        try {
            $this->promoCodeService->update($promo->id, $data);
            return $this->redirectWithSuccess('admin.promos.index', 'Kode promo berhasil diperbarui!');
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage())->withInput();
        }
    }

    /**
     * Delete promo code.
     */
    public function destroy(PromoCode $promo): RedirectResponse
    {
        try {
            $this->promoCodeService->delete($promo->id);
            return $this->redirectWithSuccess('admin.promos.index', 'Kode promo berhasil dihapus!');
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage());
        }
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleActive(PromoCode $promo): RedirectResponse
    {
        $promo = $this->promoCodeService->toggleActive($promo->id);
        $status = $promo->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return $this->backWithSuccess("Promo \"{$promo->code}\" berhasil {$status}.");
    }
}
