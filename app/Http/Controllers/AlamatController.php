<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlamatController extends Controller
{
    public function index()
    {
        $alamats = Alamat::where('id_user', Auth::id())->latest()->get();
        return view('alamat.index', compact('alamats'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'label'          => 'required|string|max:50',
            'penerima'       => 'required|string|max:255',
            'no_hp'          => 'required|string|max:20',
            'alamat_lengkap' => 'required|string',
            'kota'           => 'required|string|max:100',
            'kode_pos'       => 'nullable|string|max:10',
        ]);

        // Alamat pertama otomatis jadi default
        $isFirst = Alamat::where('id_user', Auth::id())->count() == 0;

        Alamat::create([
            'id_user'        => Auth::id(),
            'label'          => $v['label'],
            'penerima'       => $v['penerima'],
            'no_hp'          => $v['no_hp'],
            'alamat_lengkap' => $v['alamat_lengkap'],
            'kota'           => $v['kota'],
            'kode_pos'       => $v['kode_pos'] ?? '',
            'is_default'     => $isFirst,
        ]);

        return back()->with('success', 'Alamat berhasil ditambahkan');
    }

    public function setDefault($id)
    {
        Alamat::where('id_user', Auth::id())->update(['is_default' => false]);
        Alamat::where('id_alamat', $id)->where('id_user', Auth::id())->update(['is_default' => true]);

        return back()->with('success', 'Alamat default diubah');
    }

    public function destroy($id)
    {
        $alamat = Alamat::where('id_alamat', $id)->where('id_user', Auth::id())->firstOrFail();
        $alamat->delete();

        // Kalau yang dihapus itu default, jadikan alamat lain sebagai default
        $masihAdaDefault = Alamat::where('id_user', Auth::id())->where('is_default', true)->exists();
        if (!$masihAdaDefault) {
            $first = Alamat::where('id_user', Auth::id())->first();
            if ($first) $first->update(['is_default' => true]);
        }

        return back()->with('success', 'Alamat berhasil dihapus');
    }
}