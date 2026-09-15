<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background:#faf7f4; margin:0; padding:30px; }
        .card { max-width:480px; margin:auto; background:#ffffff; border-radius:16px; padding:32px; border:1px solid #eeeeee; }
        .code { font-size:32px; letter-spacing:8px; font-weight:bold; color:#7a4a2b; background:#faf7f4; border:1px dashed #e0d4c8; border-radius:12px; padding:16px; text-align:center; margin:24px 0; }
        .muted { color:#888888; font-size:13px; }
    </style>
</head>
<body>
    <div class="card">
        <h2 style="margin:0 0 8px">Graniva 🌾</h2>
        <p style="margin:0">Halo <strong>{{ $nama }}</strong>,</p>
        <p class="muted">Gunakan kode di bawah ini untuk verifikasi email Anda. Kode berlaku <strong>10 menit</strong>.</p>
        <div class="code">{{ $code }}</div>
        <p class="muted">Jika Anda tidak merasa mendaftar di Graniva, abaikan email ini.</p>
    </div>
</body>
</html>