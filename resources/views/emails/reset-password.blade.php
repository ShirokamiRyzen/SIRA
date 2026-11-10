<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Atur Ulang Kata Sandi SIRA</title>
</head>
<body style="margin: 0; padding: 0; background-color: #FBFBFA; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #111111;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #FBFBFA; padding: 32px 16px;">
<tr>
<td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 520px; background-color: #FFFFFF; border: 1px solid #EAEAEA; border-radius: 8px; overflow: hidden;">
<tr>
<td style="padding: 32px 32px 24px 32px; border-bottom: 1px solid #EAEAEA;">
<table role="presentation" cellpadding="0" cellspacing="0">
<tr>
<td style="vertical-align: middle; padding-right: 10px;">
<img src="{{ asset('android-chrome-512x512.png') }}" alt="SIRA" width="26" height="26" style="display: block; border-radius: 4px;">
</td>
<td style="vertical-align: middle;">
<span style="display: inline-block; padding: 3px 8px; border-radius: 999px; background-color: #EDF3EC; color: #346538; font-family: 'Courier New', monospace; font-size: 10px; letter-spacing: 1px; text-transform: uppercase;">
Pemulihan Akun
</span>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td style="padding: 32px;">
<h1 style="margin: 0 0 12px 0; font-size: 22px; line-height: 1.3; font-weight: 700; color: #111111;">
Halo, {{ $user->name }}
</h1>
<p style="margin: 0 0 16px 0; font-size: 13px; line-height: 1.7; color: #787774;">
Kami menerima permintaan untuk mengatur ulang kata sandi akun SIRA Anda. Klik tombol di bawah ini untuk membuat kata sandi baru.
</p>

<table role="presentation" cellpadding="0" cellspacing="0" style="margin: 24px 0;">
<tr>
<td style="border-radius: 6px; background-color: #111111;">
<a href="{{ $url }}" style="display: inline-block; padding: 12px 22px; font-size: 13px; font-weight: 600; color: #FFFFFF; text-decoration: none; border-radius: 6px;">
Atur Ulang Kata Sandi
</a>
</td>
</tr>
</table>

<p style="margin: 0 0 16px 0; font-size: 12px; line-height: 1.7; color: #787774;">
Tautan ini berlaku selama <strong style="color: #111111;">{{ $expires }} menit</strong>. Jika Anda tidak merasa meminta reset kata sandi, abaikan email ini dan kata sandi Anda tidak akan berubah.
</p>

<p style="margin: 0 0 8px 0; font-size: 11px; line-height: 1.6; color: #999999;">
Jika tombol di atas tidak berfungsi, salin dan tempel tautan berikut ke peramban Anda:
</p>
<p style="margin: 0; font-size: 11px; line-height: 1.6; word-break: break-all;">
<a href="{{ $url }}" style="color: #346538;">{{ $url }}</a>
</p>
</td>
</tr>
<tr>
<td style="padding: 20px 32px; border-top: 1px solid #EAEAEA; background-color: #FBFBFA;">
<p style="margin: 0; font-size: 11px; line-height: 1.6; color: #999999;">
Email ini dikirim otomatis oleh SIRA. Mohon tidak membalas email ini.
</p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
