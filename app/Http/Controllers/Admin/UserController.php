<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends BaseController
{
    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * List all users with search and filter.
     */
    public function index(Request $request): View
    {
        $users = $this->userService->getPaginatedFiltered(
            perPage: 10,
            search:  $request->string('search')->trim()->value() ?: null,
            role:    $request->input('role') ?: null,
        );

        return view('admin.users.index', [
            'title' => 'Kelola Pengguna',
            'users' => $users,
            'roles' => Role::pluck('name', 'name')->toArray(),
        ]);
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('admin.users.create', [
            'title' => 'Tambah Pengguna',
            'roles' => Role::pluck('name', 'name')->toArray(),
        ]);
    }

    /**
     * Store new user.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->create($request->validated());

        return $this->redirectWithSuccess('admin.users.index', 'Pengguna berhasil ditambahkan!');
    }

    /**
     * Show edit form.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'title' => 'Edit Pengguna',
            'user'  => $user,
            'roles' => Role::pluck('name', 'name')->toArray(),
        ]);
    }

    /**
     * Update user.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->update($user->id, $request->validated());

        return $this->redirectWithSuccess('admin.users.index', 'Pengguna berhasil diperbarui!');
    }

    /**
     * Delete user (guard: reject if deleting self).
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return $this->backWithError('Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
        }

        $this->userService->delete($user->id);

        return $this->redirectWithSuccess('admin.users.index', 'Pengguna berhasil dihapus!');
    }
}
