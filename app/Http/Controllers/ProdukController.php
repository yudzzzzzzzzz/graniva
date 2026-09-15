<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    // ===== INDEX (publik & admin) =====
    public function index(Request $request)
    {
        if ($request->routeIs('admin.produk.*')) {
            $produks = Produk::with(['admin', 'kategori'])
                ->when($request->q, function ($q, $s) {
                    $q->where(fn($qq) => $qq->where('nama_produk', 'like', "%{$s}%")
                        ->orWhere('warna', 'like', "%{$s}%")
                        ->orWhere('ukuran', 'like', "%{$s}%"));
                })
                ->when($request->kategori, fn($q, $k) => $q->where('kategori_id', $k))
                ->latest()
                ->paginate(10)
                ->withQueryString();

            $kategoris = Kategori::all();
            return view('admin.produk.index', compact('produks', 'kategoris'));
        }

        $search = $request->query('q');
        $kategoriFilter = $request->query('kategori');
        $sort = $request->query('sort', 'terbaru');

        $produks = Produk::with(['admin', 'kategori', 'ulasan'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_produk', 'like', "%{$search}%")
                      ->orWhere('jenis', 'like', "%{$search}%")
                      ->orWhere('warna', 'like', "%{$search}%")
                      ->orWhere('ukuran', 'like', "%{$search}%");
                });
            })
            ->when($kategoriFilter, fn($q, $k) => $q->where('kategori_id', $k))
            ->when($sort === 'harga_asc', fn($q) => $q->orderBy('harga', 'asc'))
            ->when($sort === 'harga_desc', fn($q) => $q->orderBy('harga', 'desc'))
            ->when($sort === 'terbaru', fn($q) => $q->latest())
            ->paginate(12)
            ->withQueryString();

        $kategoris = Kategori::all();

        return view('produk.index', compact('produks', 'search', 'kategoris', 'sort', 'kategoriFilter'));
    }

    // ===== DETAIL PRODUK =====
    public function show($id)
    {
        $produk = Produk::with(['kategori', 'ulasan.user'])->findOrFail($id);
        $rating = $produk->ulasan()->avg('rating') ?? 0;
        $jumlahUlasan = $produk->ulasan()->count();

        // Cek apakah user yang login udah beli produk ini (pembayaran sukses)
        $sudahBeli = false;
        if (Auth::check()) {
            $sudahBeli = Pesanan::where('id_user', Auth::id())
                ->where('id_produk', $id)
                ->whereHas('pembayaran', fn($q) => $q->where('status_bayar', 'sukses'))
                ->exists();
        }

        return view('produk.show', compact('produk', 'rating', 'jumlahUlasan', 'sudahBeli'));
    }

    // ===== CREATE =====
    public function create()
    {
        $kategoris = Kategori::all();
        return view('admin.produk.create', compact('kategoris'));
    }

    // ===== STORE =====
    public function store(Request $request)
    {
        $validated = $this->rules($request);
        $validated['ongkir'] = $request->filled('ongkir') ? $request->ongkir : null;

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        $validated['id_admin'] = Auth::guard('admin')->id();

        Produk::create($validated);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil ditambahkan');
    }

    // ===== EDIT =====
    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        $kategoris = Kategori::all();
        return view('admin.produk.edit', compact('produk', 'kategoris'));
    }

    // ===== UPDATE =====
    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);
        $validated = $this->rules($request, $id);

        if ($request->boolean('hapus_ongkir')) {
            $validated['ongkir'] = null;
        } else {
            $validated['ongkir'] = $request->filled('ongkir') ? $request->ongkir : null;
        }

        if ($request->hasFile('gambar')) {
            if ($produk->gambar) {
                Storage::disk('public')->delete($produk->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        $produk->update($validated);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diupdate');
    }

    // ===== DESTROY =====
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        if ($produk->gambar) {
            Storage::disk('public')->delete($produk->gambar);
        }

        $produk->delete();

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus');
    }

    // ===== VALIDATION RULES =====
    private function rules(Request $request, $id = null)
    {
        return $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:kategoris,id_kategori',
            'ukuran'      => 'nullable|string|max:100',
            'warna'       => 'nullable|string|max:100',
            'jenis'       => 'nullable|string|max:100',
            'tekstur'     => 'nullable|string|max:100',
            'stok'        => 'required|integer|min:0',
            'harga'       => 'required|numeric|min:0',
            'ongkir'      => 'nullable|numeric|min:0',
            'deskripsi'   => 'nullable|string',
            'gambar'      => 'nullable|image|max:2048',
        ]);
    }
}