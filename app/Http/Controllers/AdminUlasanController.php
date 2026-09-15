<?php

namespace App\Http\Controllers;

use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminUlasanController extends Controller
{
    public function index()
    {
        $ulasans = Ulasan::with(['user', 'produk'])->latest()->paginate(10);
        return view('admin.ulasan.index', compact('ulasans'));
    }

    // Balas ulasan user
    public function reply(Request $request, $id)
    {
        $ulasan = Ulasan::findOrFail($id);
        $v = $request->validate(['balasan' => 'required|string']);

        $ulasan->update(['balasan_admin' => $v['balasan']]);

        return back()->with('success', 'Balasan terkirim dan tampil di halaman produk');
    }

    // Hapus ulasan aneh
    public function destroy($id)
    {
        $ulasan = Ulasan::findOrFail($id);

        if ($ulasan->gambar) {
            Storage::disk('public')->delete($ulasan->gambar);
        }

        $ulasan->delete();
        return back()->with('success', 'Ulasan dihapus');
    }
}