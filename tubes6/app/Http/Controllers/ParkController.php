<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Kategori\Models\Kategori;
use Modules\Reservasi\Models\Reservasi;

class ParkController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $reservasiQuery = Reservasi::query();
        if (! $user->isPetugas()) {
            $reservasiQuery->where('user_id', $user->getAuthIdentifier());
        }

        $totalReservasi = (clone $reservasiQuery)->count();
        $totalKategori = Kategori::count();
        $reservasiTerbaru = (clone $reservasiQuery)->with('kategori')->latest()->take(5)->get();

        return view('dashboard', compact('totalReservasi', 'totalKategori', 'reservasiTerbaru'));
    }

    public function denda(Request $request): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $query = Reservasi::with(['kategori', 'user'])
            ->whereNotNull('denda_nominal')
            ->where('denda_nominal', '>', 0)
            ->latest();
        if (! $user->isPetugas()) {
            $query->where('user_id', $user->getAuthIdentifier());
        }

        $denda = $query->get();

        return view('denda.index', compact('denda'));
    }
}
