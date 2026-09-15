<?php

namespace App\Http\Controllers;

use App\Models\Ongkir;
use Illuminate\Http\Request;

class AdminOngkirController extends Controller
{
    public function index()
    {
        $ongkirs = Ongkir::where('kota', '!=', 'SEMUA_KOTA')->latest()->paginate(10);
        $defaultOngkir = Ongkir::where('kota', 'SEMUA_KOTA')->first();

        return view('admin.ongkir.index', compact('ongkirs', 'defaultOngkir'));
    }

    // Tambah kota spesifik
    public function store(Request $request)
    {
        $v = $request->validate([
            'kota' => 'required|string|max:100|unique:ongkir,kota',
            'biaya' => 'required|numeric|min:0',
        ]);

        Ongkir::create($v);
        return back()->with('success', 'Ongkir kota '.$v['kota'].' ditambahkan');
    }

    // Set ongkir DEFAULT untuk SEMUA KOTA
    public function storeDefault(Request $request)
    {
        $v = $request->validate(['biaya' => 'required|numeric|min:0']);

        Ongkir::updateOrCreate(
            ['kota' => 'SEMUA_KOTA'],
            ['biaya' => $v['biaya']]
        );

        return back()->with('success', 'Ongkir default (semua kota) disimpan');
    }

    // HAPUS ongkir default (Semua Kota)
    public function destroyDefault()
    {
        Ongkir::where('kota', 'SEMUA_KOTA')->delete();
        return back()->with('success', 'Ongkir default (semua kota) dihapus');
    }

    public function update(Request $request, $id)
    {
        $ongkir = Ongkir::findOrFail($id);
        $v = $request->validate([
            'kota' => 'required|string|max:100|unique:ongkir,kota,'.$id.',id_ongkir',
            'biaya' => 'required|numeric|min:0',
        ]);

        $ongkir->update($v);
        return back()->with('success', 'Ongkir diupdate');
    }

    public function destroy($id)
    {
        Ongkir::findOrFail($id)->delete();
        return back()->with('success', 'Ongkir dihapus');
    }
}