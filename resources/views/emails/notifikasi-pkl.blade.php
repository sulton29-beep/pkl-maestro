<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judul }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3f4f6;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;background-color:#ffffff;border-radius:12px;overflow:hidden;">
                    {{-- HEADER --}}
                    <tr>
                        <td style="background-color:#1e40af;padding:24px 32px;text-align:left;">
                            <h1 style="margin:0;color:#ffffff;font-size:20px;font-weight:bold;letter-spacing:0.5px;">
                                PKL SMKS MAESTRO
                            </h1>
                            <p style="margin:4px 0 0;color:#bfdbfe;font-size:12px;">
                                SMKS Maestro Sepatan
                            </p>
                        </td>
                    </tr>

                    {{-- BODY --}}
                    <tr>
                        <td style="padding:32px;">
                            @if($namaPenerima)
                                <p style="margin:0 0 8px;font-size:14px;color:#6b7280;">
                                    Halo, <strong style="color:#1f2937;">{{ $namaPenerima }}</strong>
                                </p>
                            @endif

                            <h2 style="margin:0 0 16px;font-size:18px;color:#1e40af;font-weight:bold;">
                                {{ $judul }}
                            </h2>

                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#374151;">
                                {!! nl2br(e($pesan)) !!}
                            </p>

                            @if(!empty($dataTambahan))
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:16px 0;background-color:#f9fafb;border-radius:8px;border-left:4px solid #1e40af;">
                                    <tr>
                                        <td style="padding:16px;">
                                            @foreach($dataTambahan as $label => $nilai)
                                                <p style="margin:0 0 8px;font-size:13px;">
                                                    <span style="color:#6b7280;display:inline-block;min-width:140px;">{{ $label }}</span>
                                                    <span style="color:#1f2937;font-weight:600;">: {{ $nilai }}</span>
                                                </p>
                                            @endforeach
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            @if($linkAksi)
                                <table cellpadding="0" cellspacing="0" border="0" style="margin:24px 0 8px;">
                                    <tr>
                                        <td style="background-color:#1e40af;border-radius:6px;">
                                            <a href="{{ $linkAksi }}" style="display:inline-block;padding:12px 24px;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;">
                                                {{ $labelAksi }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <p style="margin:24px 0 0;font-size:13px;color:#6b7280;">
                                Terima kasih,<br>
                                <strong style="color:#1f2937;">Tim PKL SMKS Maestro</strong>
                            </p>
                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td style="background-color:#f9fafb;padding:20px 32px;border-top:1px solid #e5e7eb;">
                            <p style="margin:0 0 4px;font-size:11px;color:#9ca3af;">
                                Email ini dikirim otomatis oleh sistem <strong>PKL SMKS MAESTRO</strong>.
                            </p>
                            <p style="margin:0;font-size:11px;color:#9ca3af;">
                                Jl. Raya Kramat Pakuhaji Km 3,5, Kayu Agung, Kec. Sepatan, Tangerang
                            </p>
                            <p style="margin:8px 0 0;font-size:11px;color:#9ca3af;">
                                &copy; {{ date('Y') }} SMKS Maestro Sepatan. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>