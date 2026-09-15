<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;

class AdminNotifikasiController extends Controller
{
    public function index()
    {
        $notifikasis = Notifikasi::with('user')->latest()->paginate(15)->withQueryString();
        $users = User::orderBy('nama')->get();

        return view('admin.notifikasi.index', compact('notifikasis', 'users'));
    }

    // Kirim notifikasi (ke semua / user tertentu)
    public function send(Request $request)
    {
        $v = $request->validate([
            'target'  => 'required|in:semua,user',
            'id_user' => 'nullable|exists:users,id_user',
            'judul'   => 'required|string|max:255',
            'pesan'   => 'required|string',
            'tipe'    => 'required|in:success,info,error',
        ]);

        if ($v['target'] === 'user' && empty($v['id_user'])) {
            return back()->with('error', 'Pilih user terlebih dahulu')->withInput();
        }

        if ($v['target'] === 'semua') {
            $users = User::where('is_blocked', false)->get();
            foreach ($users as $user) {
                Notifikasi::create([
                    'id_user' => $user->id_user,
                    'judul'   => $v['judul'],
                    'pesan'   => $v['pesan'],
                    'tipe'    => $v['tipe'],
                ]);
            }
            return back()->with('success', 'Notifikasi terkirim ke semua user');
        } else {
            Notifikasi::create([
                'id_user' => $v['id_user'],
                'judul'   => $v['judul'],
                'pesan'   => $v['pesan'],
                'tipe'    => $v['tipe'],
            ]);
            return back()->with('success', 'Notifikasi terkirim ke user terpilih');
        }
    }

    // Hapus SEMUA notifikasi (semua user)
    public function deleteAll()
    {
        $jumlah = Notifikasi::count();
        Notifikasi::query()->delete();

        return back()->with('success', $jumlah.' notifikasi dihapus untuk semua user');
    }

    // Hapus semua notifikasi milik USER TERTENTU
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $jumlah = Notifikasi::where('id_user', $id)->count();

        Notifikasi::where('id_user', $id)->delete();

        return back()->with('success', $jumlah.' notifikasi dihapus untuk user '.$user->nama);
    }

    // Hapus SATU notifikasi
    public function deleteOne($id)
    {
        Notifikasi::findOrFail($id)->delete();
        return back()->with('success', 'Notifikasi dihapus');
    }
}