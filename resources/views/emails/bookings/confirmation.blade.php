<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Booking {{ $booking->booking_code }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f7f7f5; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f7f7f5; margin:0; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; background-color:#ffffff; border-radius:20px; overflow:hidden; border:1px solid #e5e7eb;">
                    <tr>
                        <td style="padding:28px 24px; background:linear-gradient(135deg, #fff7ed 0%, #ffffff 58%, #fffbeb 100%);">
                            <p style="margin:0 0 10px; font-size:12px; letter-spacing:0.18em; text-transform:uppercase; font-weight:700; color:#c2410c;">
                                Booking Home Service
                            </p>
                            <h1 style="margin:0 0 12px; font-size:28px; line-height:1.25; color:#111827;">
                                Booking Anda sudah kami terima
                            </h1>
                            <p style="margin:0; font-size:15px; line-height:1.7; color:#4b5563;">
                                Halo {{ $booking->customer_name }}, terima kasih sudah melakukan booking servis motor. Simpan email ini sebagai ringkasan awal sebelum admin menghubungi Anda.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:20px; border:1px dashed #fdba74; border-radius:18px; background-color:#fff7ed;">
                                <tr>
                                    <td style="padding:18px 20px; text-align:center;">
                                        <p style="margin:0 0 8px; font-size:12px; font-weight:700; letter-spacing:0.2em; text-transform:uppercase; color:#c2410c;">
                                            Kode booking
                                        </p>
                                        <p style="margin:0; font-size:26px; font-weight:700; letter-spacing:0.14em; color:#111827;">
                                            {{ $booking->booking_code }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:20px;">
                                <tr>
                                    <td style="padding:0 0 12px;">
                                        <h2 style="margin:0; font-size:18px; color:#111827;">Ringkasan booking</h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:16px 18px; border:1px solid #e5e7eb; border-radius:18px; background-color:#ffffff;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding:0 0 12px; font-size:14px; color:#6b7280;">Paket terpilih</td>
                                                <td style="padding:0 0 12px; font-size:14px; font-weight:700; color:#111827;" align="right">{{ $booking->package_name_snapshot }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:0 0 12px; font-size:14px; color:#6b7280;">Status booking</td>
                                                <td style="padding:0 0 12px; font-size:14px; font-weight:700; color:#111827;" align="right">{{ $statusLabel }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:0 0 12px; font-size:14px; color:#6b7280;">Tanggal servis</td>
                                                <td style="padding:0 0 12px; font-size:14px; font-weight:700; color:#111827;" align="right">{{ optional($booking->service_date)->format('d M Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:0; font-size:14px; color:#6b7280;">Jam servis</td>
                                                <td style="padding:0; font-size:14px; font-weight:700; color:#111827;" align="right">{{ $booking->service_time }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if ($booking->customItems->isNotEmpty())
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:20px;">
                                    <tr>
                                        <td style="padding:0 0 12px;">
                                            <h2 style="margin:0; font-size:18px; color:#111827;">Item custom</h2>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:16px 18px; border:1px solid #e5e7eb; border-radius:18px; background-color:#fcfcfc;">
                                            @foreach ($booking->customItems as $item)
                                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="{{ ! $loop->last ? 'padding-bottom:12px; margin-bottom:12px; border-bottom:1px solid #e5e7eb;' : '' }}">
                                                    <tr>
                                                        <td style="font-size:14px; font-weight:700; color:#111827;">{{ $item->item_name_snapshot }}</td>
                                                        <td style="font-size:14px; font-weight:700; color:#111827;" align="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding-top:4px; font-size:13px; color:#6b7280;">Qty {{ $item->qty }} x Rp {{ number_format($item->item_price_snapshot, 0, ',', '.') }}</td>
                                                        <td></td>
                                                    </tr>
                                                </table>
                                            @endforeach
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:20px;">
                                <tr>
                                    <td style="padding:0 0 12px;">
                                        <h2 style="margin:0; font-size:18px; color:#111827;">Ringkasan harga</h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:16px 18px; border:1px solid #e5e7eb; border-radius:18px; background-color:#ffffff;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding:0 0 12px; font-size:14px; color:#6b7280;">Harga paket snapshot</td>
                                                <td style="padding:0 0 12px; font-size:14px; font-weight:700; color:#111827;" align="right">Rp {{ number_format($booking->package_price_snapshot, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:0 0 12px; font-size:14px; color:#6b7280;">Subtotal</td>
                                                <td style="padding:0 0 12px; font-size:14px; font-weight:700; color:#111827;" align="right">Rp {{ number_format($booking->subtotal_price, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:0 0 12px; font-size:14px; color:#6b7280;">Biaya layanan</td>
                                                <td style="padding:0 0 12px; font-size:14px; font-weight:700; color:#111827;" align="right">Rp {{ number_format($booking->service_fee, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:0; font-size:15px; font-weight:700; color:#111827;">Total estimasi</td>
                                                <td style="padding:0; font-size:16px; font-weight:700; color:#c2410c;" align="right">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:20px;">
                                <tr>
                                    <td style="padding:0 0 12px;">
                                        <h2 style="margin:0; font-size:18px; color:#111827;">Lokasi servis</h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:16px 18px; border:1px solid #e5e7eb; border-radius:18px; background-color:#fcfcfc;">
                                        <p style="margin:0 0 10px; font-size:14px; line-height:1.7; color:#111827;">
                                            {{ $booking->address_text }}
                                        </p>
                                        <p style="margin:0; font-size:13px; line-height:1.7; color:#6b7280;">
                                            Patokan rumah: {{ $booking->house_landmark }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:16px;">
                                <tr>
                                    <td style="padding:16px 18px; border-radius:18px; background-color:#111827;">
                                        <p style="margin:0 0 8px; font-size:14px; font-weight:700; color:#ffffff;">Butuh bantuan admin?</p>
                                        <p style="margin:0 0 6px; font-size:13px; line-height:1.7; color:#d1d5db;">
                                            Telepon: {{ $workshopPhone }}
                                        </p>
                                        <p style="margin:0; font-size:13px; line-height:1.7; color:#d1d5db;">
                                            WhatsApp: {{ $workshopWhatsapp }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0; font-size:12px; line-height:1.7; color:#6b7280;">
                                Email ini dibuat otomatis dari snapshot booking saat pesanan masuk. Jika ada perubahan jadwal atau penyesuaian servis, admin akan menghubungi Anda secara terpisah.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
