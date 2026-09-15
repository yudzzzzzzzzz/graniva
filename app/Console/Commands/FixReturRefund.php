<?php

namespace App\Console\Commands;

use App\Models\Retur;
use Illuminate\Console\Command;

class FixReturRefund extends Command
{
    protected $signature = 'retur:fix';
    protected $description = 'Koreksi otomatis refund retur lama yang belum 100%';

    public function handle()
    {
        $returs = Retur::with(['pesanan', 'user'])->where('status', 'disetujui')->get();

        foreach ($returs as $r) {
            $p = $r->pesanan;
            if (!$p) continue;

            $full   = $p->harga + ($p->ongkir ?? 0);  // yang seharusnya (100%)
            $sudah  = $p->harga;                      // asumsi kode lama cuma ngasih harga
            $kurang = $full - $sudah;

            if ($kurang > 0) {
                $r->user()->increment('saldo', $kurang);
                $this->info("✅ User #{$r->id_user} ditambah Rp {$kurang}");
            }
        }

        $this->info('Selesai! Semua refund retur lama udah 100%.');
        return 0;
    }
}