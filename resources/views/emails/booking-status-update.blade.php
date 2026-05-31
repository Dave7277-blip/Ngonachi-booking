<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking {{ $booking->reference }} — {{ ucfirst($booking->status) }}</title>
  <style>
    body        { margin:0; padding:0; background:#FAF7F2; font-family:'Georgia',serif; }
    .wrapper    { max-width:580px; margin:40px auto; background:#ffffff;
                  border:1px solid #E8D5B0; border-radius:8px; overflow:hidden; }
    .header     { background:#1A1612; padding:32px; text-align:center; }
    .logo       { color:#C9A96E; font-size:26px; letter-spacing:6px; margin:0; }
    .tagline    { color:rgba(255,255,255,0.4); font-size:11px;
                  letter-spacing:3px; margin-top:6px; }
    .body       { padding:36px; }
    h2          { color:#1A1612; font-weight:400; margin-top:0; font-size:22px; }
    p           { color:#4A4035; line-height:1.85; font-size:15px; }
    .ref-box    { background:#FAF7F2; border-left:3px solid #C9A96E;
                  padding:14px 18px; margin:24px 0; font-size:14px; color:#1A1612; }
    .status-confirmed { background:#EAF3DE; color:#3B6D11; }
    .status-completed { background:#E6F1FB; color:#185FA5; }
    .status-rejected  { background:#FCEBEB; color:#A32D2D; }
    .status-badge     { display:inline-block; padding:5px 16px; border-radius:20px;
                        font-size:13px; font-weight:bold; letter-spacing:0.5px; }
    .detail-table { width:100%; border-collapse:collapse; margin:0 0 24px; }
    .detail-table td { padding:12px 0; border-bottom:1px solid #F0EAE0;
                       font-size:14px; vertical-align:top; }
    .detail-table td:first-child { color:#8C7B6B; width:140px; }
    .detail-table td:last-child  { color:#1A1612; font-weight:500; }
    .footer { background:#1A1612; padding:22px; text-align:center;
              color:rgba(255,255,255,0.4); font-size:12px; }
    .footer a { color:#C9A96E; text-decoration:none; }
    .social-row { margin-top:10px; }
    .social-row a { color:#C9A96E; text-decoration:none; margin:0 6px; font-size:12px; }
  </style>
</head>
<body>
  <div class="wrapper">

    <div class="header">
      <p class="logo">NGONACHI</p>
      <p class="tagline">PIX PHOTOGRAPHY</p>
    </div>

    <div class="body">

      @if($booking->status === 'confirmed')
        <h2>Great News, {{ $booking->client_name }}! 🎉</h2>
        <p>
          We are delighted to confirm your booking with Ngonachi Pix Photography.
          Your date is now secured and we look forward to capturing your special moments!
        </p>
      @elseif($booking->status === 'completed')
        <h2>Thank You, {{ $booking->client_name }}!</h2>
        <p>
          Your event has been marked as completed. Your edited photographs will
          be delivered as per your package agreement. Thank you for choosing
          Ngonachi Pix Photography!
        </p>
      @elseif($booking->status === 'rejected')
        <h2>Booking Update — {{ $booking->client_name }}</h2>
        <p>
          Unfortunately we are unable to accommodate your booking at this time.
          Please contact us directly so we can explore alternative dates or options.
        </p>
      @endif

      <div class="ref-box">
        <strong>Reference:</strong> {{ $booking->reference }}
        &nbsp;|&nbsp;
        <span class="status-badge status-{{ $booking->status }}">
          {{ ucfirst($booking->status) }}
        </span>
      </div>

      <table class="detail-table">
        <tr>
          <td>Package</td>
          <td>{{ $booking->package->name ?? 'N/A' }}</td>
        </tr>
        <tr>
          <td>Event Date</td>
          <td>{{ $booking->event_date->format('l, d F Y') }}</td>
        </tr>
        <tr>
          <td>Location</td>
          <td>{{ $booking->event_location }}</td>
        </tr>
        @if($booking->admin_notes)
        <tr>
          <td>Note from us</td>
          <td>{{ $booking->admin_notes }}</td>
        </tr>
        @endif
      </table>

      <p style="font-size:14px;">
        Questions? Contact us at
        <a href="mailto:ngonachi62@gmail.com" style="color:#C9A96E;">
          ngonachi62@gmail.com
        </a>
        or call <strong>+255 621 018 229</strong>.
      </p>

      <p style="font-size:13px;color:#8C7B6B;">Follow us for our latest work:</p>
      <div class="social-row">
        <a href="https://www.instagram.com/ngonachi_pix/" target="_blank">📷 Instagram</a>
        <a href="https://www.tiktok.com/@ngonachi_pix" target="_blank">🎵 TikTok</a>
        <a href="https://wa.me/255621018229" target="_blank">💬 WhatsApp</a>
      </div>
    </div>

    <div class="footer">
      &copy; {{ date('Y') }} Ngonachi Pix Photography &bull; Arusha, Tanzania<br>
      <a href="mailto:ngonachi62@gmail.com">ngonachi62@gmail.com</a>
      &bull; +255 621 018 229
    </div>

  </div>
</body>
</html>