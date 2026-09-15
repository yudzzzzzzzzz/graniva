<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminKategoriController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = Kategori::withCount('produk')
            ->when($request->q, fn($q, $s) => $q->where('nama_kategori', 'like', "%{$s}%"))
            ->latest()->paginate(10)->withQueryString();

        return view('admin.kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('admin.kategori.form', ['kategori' => null]);
    }

    public function store(Request $request)
    {
        $v = $this->rules($request);
        $v['slug'] = Str::slug($v['nama_kategori']).'-'.uniqid();

        if ($request->hasFile('gambar')) {
            $v['gambar'] = $request->file('gambar')->store('kategori', 'public');
        }

        Kategori::create($v);
        return redirect()->route('admin.kategori.index')->with('success', 'Kategori ditambahkan');
    }

    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('admin.kategori.form', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);
        $v = $this->rules($request, $id);

        if ($request->hasFile('gambar')) {
            if ($kategori->gambar) Storage::disk('public')->delete($kategori->gambar);
            $v['gambar'] = $request->file('gambar')->store('kategori', 'public');
        }

        $kategori->update($v);
        return redirect()->route('admin.kategori.index')->with('success', 'Kategori diupdate');
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        if ($kategori->gambar) Storage::disk('public')->delete($kategori->gambar);
        $kategori->delete();
        return back()->with('success', 'Kategori dihapus');
    }

    private function rules(Request $request, $id = null)
    {
        return $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategoris,nama_kategori'.($id ? ",{$id},id_kategori" : ''),
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|max:2048',
        ]);
    }
}