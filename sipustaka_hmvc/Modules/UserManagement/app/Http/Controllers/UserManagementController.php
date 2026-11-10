<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Modules\UserManagement\Http\Requests\SaveUserRequest;

class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('usermanagement::index', ['users' => User::query()->orderBy('name')->paginate(10)]);
    }

    public function create(): View
    {
        return view('usermanagement::form', [
            'managedUser' => new User(),
            'action' => route('users.store'),
            'method' => 'POST',
        ]);
    }

    public function store(SaveUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('usermanagement::form', [
            'managedUser' => $user,
            'action' => route('users.update', $user),
            'method' => 'PUT',
        ]);
    }

    public function update(SaveUserRequest $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user) && $request->validated('role') !== 'admin') {
            return back()->withInput()->withErrors([
                'role' => 'Role akun yang sedang digunakan tidak dapat diturunkan.',
            ]);
        }

        $data = $request->safe()->except('password');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->validated('password'));
        }
        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (request()->user()->is($user)) {
            return redirect()->route('users.index')->withErrors([
                'user' => 'Akun yang sedang digunakan tidak dapat dihapus.',
            ]);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
