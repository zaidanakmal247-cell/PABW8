<?php

namespace Modules\Peminjaman\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Modules\Peminjaman\Http\Requests\SavePeminjamanRequest;
use Modules\Peminjaman\Models\Peminjaman;

class PeminjamanController extends Controller
{
    public function index(): View
    {
        return view('peminjaman::index');
    }

    public function data(): JsonResponse
    {
        $items = Peminjaman::query()->latest()->get()->map(fn ($item) => [
            'id' => $item->id,
            'nama_peminjam' => $item->nama_peminjam,
            'judul_buku' => $item->judul_buku,
            'tanggal_pinjam' => $item->tanggal_pinjam->format('Y-m-d'),
            'tanggal_kembali' => $item->tanggal_kembali?->format('Y-m-d'),
            'status' => $item->status,
            'bukti_url' => $item->bukti_path ? URL::to('storage/'.$item->bukti_path) : null,
        ]);

        return response()->json(['data' => $items]);
    }

    public function store(SavePeminjamanRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('bukti')) {
            $data['bukti_path'] = $request->file('bukti')->store('bukti-peminjaman', 'public');
        }

        $item = Peminjaman::create($data);

        return response()->json([
            'message' => 'Data peminjaman berhasil ditambahkan.',
            'data' => $item->id,
        ], 201);
    }

    public function update(SavePeminjamanRequest $request, Peminjaman $peminjaman): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('bukti')) {
            $newPath = $request->file('bukti')->store('bukti-peminjaman', 'public');
            if ($peminjaman->bukti_path) {
                Storage::disk('public')->delete($peminjaman->bukti_path);
            }
            $data['bukti_path'] = $newPath;
        }

        $peminjaman->update($data);

        return response()->json(['message' => 'Data peminjaman berhasil diperbarui.']);
    }

    public function destroy(Peminjaman $peminjaman): JsonResponse
    {
        if ($peminjaman->bukti_path) {
            Storage::disk('public')->delete($peminjaman->bukti_path);
        }
        $peminjaman->delete();

        return response()->json(['message' => 'Data peminjaman berhasil dihapus.']);
    }
}
