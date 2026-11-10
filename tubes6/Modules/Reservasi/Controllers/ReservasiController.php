<?php

namespace Modules\Reservasi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Kategori\Models\Kategori;
use Modules\Reservasi\Models\Reservasi;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReservasiController extends Controller
{
    public function index(): View
    {
        return view('reservasi::index', [
            'kategoris' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function data(): JsonResponse
    {
        $query = Reservasi::with(['kategori', 'user'])->latest();
        if (! auth()->user()->isPetugas()) {
            $query->where('user_id', auth()->id());
        }

        $reservasis = $query->get()
            ->map(fn (Reservasi $reservasi): array => $this->serialize($reservasi));

        return response()->json(['data' => $reservasis]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatedReservation($request);
        $file = $validated['file_stnk'] ?? null;
        unset($validated['file_stnk']);

        $reservasi = Reservasi::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);
        if ($file) {
            $reservasi->file_stnk = $this->storeStnk($file);
            $reservasi->save();
        }

        return response()->json([
            'message' => 'Reservasi berhasil ditambahkan.',
            'data' => $this->serialize($reservasi->load('kategori')),
        ], 201);
    }

    public function update(Request $request, Reservasi $reservasi): JsonResponse
    {
        if ($request->user()->isPetugas()) {
            $validated = $request->validate([
                'status' => ['required', Rule::in(['pending', 'disetujui', 'selesai'])],
                'denda_nominal' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99', 'required_with:denda_keterangan'],
                'denda_keterangan' => ['nullable', 'string', 'max:2000', 'required_with:denda_nominal'],
            ]);
        } else {
            abort_unless($reservasi->user_id === $request->user()->id, 403);
            $validated = $this->validatedReservation($request);
            unset($validated['status']);

            $file = $validated['file_stnk'] ?? null;
            unset($validated['file_stnk']);
            if ($file) {
                $validated['file_stnk'] = $this->storeStnk($file);
            }
        }

        $oldFile = $reservasi->file_stnk;
        $reservasi->update($validated);
        if (! $request->user()->isPetugas() && $file && $oldFile) {
            $this->deleteStnk($oldFile);
        }

        return response()->json([
            'message' => $request->user()->isPetugas()
                ? 'Status reservasi dan denda berhasil diperbarui.'
                : 'Reservasi berhasil diperbarui.',
            'data' => $this->serialize($reservasi->load(['kategori', 'user'])),
        ]);
    }

    public function stnk(Request $request, Reservasi $reservasi): BinaryFileResponse
    {
        abort_unless(
            $request->user()->isPetugas() || $reservasi->user_id === $request->user()->id,
            403
        );
        abort_unless($reservasi->file_stnk, 404);
        $path = $reservasi->file_stnk;

        if (! Storage::disk('local')->exists($path)) {
            abort_unless(
                preg_match('#\Astnk/[^/\\\\]+\z#', $path) === 1
                    && Storage::disk('public')->exists($path),
                404
            );

            $privatePath = 'reservasi/stnk/'.Str::uuid().'.'.pathinfo($path, PATHINFO_EXTENSION);
            $content = Storage::disk('public')->get($path);
            if (! Storage::disk('local')->put($privatePath, $content)) {
                throw new \RuntimeException('Berkas STNK gagal dipindahkan ke penyimpanan privat.');
            }

            $reservasi->file_stnk = $privatePath;
            $reservasi->save();
            $this->deleteStnk($path);
            $path = $privatePath;
        }

        return response()->file(Storage::disk('local')->path($path), [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function deleteStnk(string $path): void
    {
        Storage::disk('local')->delete($path);

        if (preg_match('#\Astnk/[^/\\\\]+\z#', $path) === 1) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedReservation(Request $request): array
    {
        return $request->validate([
            'kategori_id' => ['required', 'integer', 'exists:kategori,id'],
            'nama_pengguna' => ['required', 'string', 'max:255'],
            'nomor_plat' => ['required', 'string', 'max:20'],
            'tanggal_reservasi' => ['required', 'date'],
            'durasi_jam' => ['required', 'integer', 'min:1', 'max:24'],
            'file_stnk' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);
    }

    private function storeStnk(UploadedFile $file): string
    {
        $filename = Str::uuid().'.'.$file->extension();
        $path = $file->storeAs('reservasi/stnk', $filename, 'local');

        if (! $path) {
            throw new \RuntimeException('Berkas STNK gagal disimpan.');
        }

        return $path;
    }

    /**
     * @return array<string, mixed>
     */
    private function serialize(Reservasi $reservasi): array
    {
        return [
            'id' => $reservasi->id,
            'pemilik' => $reservasi->user?->name ?? 'Data lama',
            'kategori_id' => $reservasi->kategori_id,
            'kategori' => $reservasi->kategori->nama_kategori,
            'nama_pengguna' => $reservasi->nama_pengguna,
            'nomor_plat' => $reservasi->nomor_plat,
            'tanggal_reservasi' => $reservasi->tanggal_reservasi->format('Y-m-d'),
            'durasi_jam' => $reservasi->durasi_jam,
            'status' => $reservasi->status,
            'denda_nominal' => $reservasi->denda_nominal,
            'denda_keterangan' => $reservasi->denda_keterangan,
            'file_url' => $reservasi->file_stnk ? route('reservasi.stnk', $reservasi) : null,
        ];
    }
}
