<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifs = Notifikasi::where('id_user', Auth::id())->latest()->get();
        return view('user.notifikasi.index', compact('notifs'));
    }

    public function markRead($id)
    {
        Notifikasi::where('id_notifikasi', $id)->where('id_user', Auth::id())->update(['is_read' => true]);
        return back();
    }

    public function markAllRead()
    {
        Notifikasi::where('id_user', Auth::id())->update(['is_read' => true]);
        return back()->with('success', 'Semua ditandai dibaca');
    }
}