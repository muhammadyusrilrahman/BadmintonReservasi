<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\BaseController;
use App\Services\PromoCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class PromoController extends BaseController
{
    public function __construct(private readonly PromoCodeService $promoCodeService)
    {
    }

    /**
     * Show create form for kasir.
     */
    public function create(): View
    {
        return view('kasir.promos.create', [
            'title' => 'Tambah Promo',
        ]);
    }

    /**
     * Store new promo code (kasir).
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code'            => ['required', 'string', 'max:50', 'unique:promo_codes,code'],
            'description'     => ['nullable', 'string', 'max:500'],
            'discount_type'   => ['required', 'in:percentage,fixed'],
            'discount_value'  => ['required', 'integer', 'min:1'],
            'max_discount'    => ['nullable', 'integer', 'min:0'],
            'valid_from'      => ['required', 'date'],
            'valid_until'     => ['required', 'date', 'after:valid_from'],
            'max_usage'       => ['nullable', 'integer', 'min:1'],
            'activation_mode' => ['required', 'in:manual,auto'],
            'is_active'       => ['sometimes', 'boolean'],
        ], [], [
            'code'            => 'kode promo',
            'description'     => 'deskripsi',
            'discount_type'   => 'tipe diskon',
            'discount_value'  => 'nilai diskon',
            'max_discount'    => 'maks diskon',
            'valid_from'      => 'berlaku dari',
            'valid_until'     => 'berlaku sampai',
            'max_usage'       => 'maks penggunaan',
            'activation_mode' => 'mode aktivasi',
        ]);

        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active');

        if ($data['activation_mode'] === 'auto') {
            $data['is_active'] = now()->gte($data['valid_from']) && now()->lte($data['valid_until']);
        }

        try {
            $this->promoCodeService->create($data);
            return $this->redirectWithSuccess('kasir.promos.index', 'Kode promo berhasil ditambahkan!');
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage())->withInput();
        }
    }
}
