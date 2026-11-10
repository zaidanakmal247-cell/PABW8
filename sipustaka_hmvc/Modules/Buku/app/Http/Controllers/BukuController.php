<?php

namespace Modules\Buku\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Buku\Http\Requests\SaveBukuRequest;
use Modules\Buku\Models\Buku;

class BukuController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $bukus = Buku::query()
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('penerbit', 'like', "%{$search}%");
            }))
            ->orderBy('judul')
            ->paginate(10)
            ->withQueryString();

        return view('buku::index', compact('bukus', 'search'));
    }

    public function create(): View
    {
        return view('buku::form', ['buku' => new Buku(), 'action' => route('buku.store'), 'method' => 'POST']);
    }

    public function store(SaveBukuRequest $request): RedirectResponse
    {
        Buku::create($request->validated());

        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Buku $buku): View
    {
        return view('buku::form', [
            'buku' => $buku,
            'action' => route('buku.update', $buku),
            'method' => 'PUT',
        ]);
    }

    public function update(SaveBukuRequest $request, Buku $buku): RedirectResponse
    {
        $buku->update($request->validated());

        return redirect()->route('buku.index')->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Buku $buku): RedirectResponse
    {
        $buku->delete();

        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus.');
    }
}
