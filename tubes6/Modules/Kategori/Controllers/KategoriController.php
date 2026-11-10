<?php

namespace Modules\Kategori\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Kategori\Models\Kategori;

class KategoriController extends Controller
{
    public function index(): View
    {
        return view('kategori::index', [
            'kategoris' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function create(): View
    {
        return view('kategori::create');
    }

    public function store(Request $request): RedirectResponse
    {
        Kategori::create($this->validated($request));

        return redirect()->route('kategori.index')->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori): View
    {
        return view('kategori::edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori): RedirectResponse
    {
        $kategori->update($this->validated($request, $kategori));

        return redirect()->route('kategori.index')->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        if ($kategori->reservasis()->exists()) {
            return redirect()->route('kategori.index')
                ->with('error', 'Kategori masih digunakan oleh reservasi dan tidak dapat dihapus.');
        }

        $kategori->delete();

        return redirect()->route('kategori.index')->with('status', 'Kategori berhasil dihapus.');
    }

    /**
     * @return array{nama_kategori: string, deskripsi: ?string}
     */
    private function validated(Request $request, ?Kategori $kategori = null): array
    {
        return $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kategori', 'nama_kategori')->ignore($kategori?->id),
            ],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
