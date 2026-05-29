<!DOCTYPE html>
<html>
<head>
<style>
  body{font-family:'Georgia',serif;background:#FAF7F2;margin:0;padding:0}
  .wrap{max-width:560px;margin:40px auto;background:#fff;border:1px solid #E8D5B0;border-radius:8px;overflow:hidden}
  .header{background:#1A1612;padding:30px;text-align:center}
  .logo{color:#C9A96E;font-size:24px;letter-spacing:4px}
  .body{padding:32px}
  .ref{background:#FAF7F2;border-left:3px solid #C9A96E;padding:12px 16px;margin:20px 0;font-size:14px}
  .detail-row{display:flex;padding:10px 0;border-bottom:1px solid #F0EAE0;font-size:14px}
  .label{color:#8C7B6B;width:140px;flex-shrink:0}
  .footer{background:#1A1612;padding:20px;text-align:center;color:rgba(255,255,255,.5);font-size:12px}
</style>
</head>
<body>
<div class="wrap">
  <div class="header"><div class="logo">LUMIÈRE STUDIOS</div></div>
  <div class="body">
    <h2 style="color:#1A1612;font-weight:400">Booking Received, {{ $booking->client_name }}!</h2>
    <p style="color:#4A4035;line-height:1.8">Thank you for choosing Lumière Studios. We've received your booking request and will confirm within <strong>24 hours</strong>.</p>
    <div class="ref"><strong>Reference:</strong> {{ $booking->reference }}</div>
    <div class="detail-row"><span class="label">Package</span><span>{{ $booking->package->name }}</span></div>
    <div class="detail-row"><span class="label">Event Date</span><span>{{ $booking->event_date->format('D, d M Y') }}</span></div>
    <div class="detail-row"><span class="label">Location</span><span>{{ $booking->event_location }}</span></div>
    <div class="detail-row"><span class="label">Status</span><span style="color:#854F0B">Pending Confirmation</span></div>
    <p style="color:#4A4035;margin-top:24px;font-size:14px">Questions? Contact us at <a href="mailto:hello@lumiere.co.tz" style="color:#C9A96E">hello@lumiere.co.tz</a> or +255 754 123 456.</p>
  </div>
  <div class="footer">© {{ date('Y') }} Lumière Studios • Dar es Salaam, Tanzania</div>
</div>
</body>
</html>