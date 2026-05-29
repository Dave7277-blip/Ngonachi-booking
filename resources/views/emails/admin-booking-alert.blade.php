<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Booking — {{ $booking->reference }}</title>
  <style>
    body        { margin:0; padding:0; background:#F0EDE8; font-family:'Georgia',serif; }
    .wrapper    { max-width:580px; margin:40px auto; background:#ffffff;
                  border-radius:8px; overflow:hidden;
                  border:2px solid #C9A96E; }
    .header     { background:#1A1612; padding:28px 32px;
                  display:flex; align-items:center; justify-content:space-between; }
    .logo       { color:#C9A96E; font-size:22px; letter-spacing:4px; margin:0; }
    .alert-badge{ background:#C9A96E; color:#1A1612; font-size:11px;
                  letter-spacing:2px; padding:5px 14px; border-radius:20px;
                  font-family:Arial,sans-serif; font-weight:bold; }
    .body       { padding:32px; }
    h2          { color:#1A1612; font-weight:400; margin-top:0; font-size:20px; }
    .ref-box    { background:#FAF7F2; border-left:4px solid #C9A96E;
                  padding:14px 18px; margin:20px 0; font-size:14px; }
    .ref-box strong { color:#8B6914; font-size:16px; }
    .detail-table { width:100%; border-collapse:collapse; margin:0 0 20px; }
    .detail-table th { background:#FAF7F2; padding:10px 14px; text-align:left;
                       font-size:11px; letter-spacing:2px; text-transform:uppercase;
                       color:#8C7B6B; font-family:Arial,sans-serif; font-weight:normal; }
    .detail-table td { padding:12px 14px; border-bottom:1px solid #F0EAE0;
                       font-size:14px; color:#1A1612; vertical-align:top; }
    .detail-table tr:last-child td { border-bottom:none; }
    .status-badge { display:inline-block; padding:4px 14px; border-radius:20px;
                    background:#FAEEDA; color:#854F0B; font-size:12px;
                    font-family:Arial,sans-serif; font-weight:bold; }
    .action-btn { display:inline-block; margin-top:16px; padding:12px 28px;
                  background:#C9A96E; color:#1A1612; text-decoration:none;
                  border-radius:4px; font-size:13px; letter-spacing:1px;
                  font-family:Arial,sans-serif; font-weight:bold; }
    .notes-box  { background:#FAF7F2; border-radius:4px; padding:14px;
                  font-size:13px; color:#4A4035; line-height:1.8; margin-top:4px; }
    .footer     { background:#1A1612; padding:20px 32px; text-align:center;
                  color:rgba(255,255,255,0.4); font-size:12px;
                  font-family:Arial,sans-serif; }
    .footer a   { color:#C9A96E; text-decoration:none; }
  </style>
</head>
<body>
<div class="wrapper">

  <div class="header">
    <p class="logo">LUMIÈRE</p>
    <span class="alert-badge">NEW BOOKING</span>
  </div>

  <div class="body">
    <h2>New Booking Request Received!</h2>
    <p style="color:#4A4035;font-size:14px;line-height:1.8;margin-bottom:20px">
      A new photography booking has just been submitted and is awaiting your review.
      Please log in to the admin dashboard to approve or reject it.
    </p>

    <div class="ref-box">
      <strong>{{ $booking->reference }}</strong>
      &nbsp;&nbsp;·&nbsp;&nbsp;
      <span class="status-badge">Pending Review</span>
    </div>

    <table class="detail-table">
      <tr>
        <th colspan="2">Client Details</th>
      </tr>
      <tr>
        <td style="width:140px;color:#8C7B6B">Full Name</td>
        <td><strong>{{ $booking->client_name }}</strong></td>
      </tr>
      <tr>
        <td style="color:#8C7B6B">Email</td>
        <td><a href="mailto:{{ $booking->client_email }}" style="color:#C9A96E">{{ $booking->client_email }}</a></td>
      </tr>
      <tr>
        <td style="color:#8C7B6B">Phone</td>
        <td><a href="tel:{{ $booking->client_phone }}" style="color:#C9A96E">{{ $booking->client_phone }}</a></td>
      </tr>

      <tr>
        <th colspan="2" style="padding-top:20px">Event Details</th>
      </tr>
      <tr>
        <td style="color:#8C7B6B">Event Type</td>
        <td>{{ ucfirst($booking->event_type) }}</td>
      </tr>
      <tr>
        <td style="color:#8C7B6B">Package</td>
        <td><strong>{{ $booking->package->name ?? 'N/A' }}</strong></td>
      </tr>
      <tr>
        <td style="color:#8C7B6B">Package Price</td>
        <td>{{ $booking->package->formatted_price ?? 'N/A' }}</td>
      </tr>
      <tr>
        <td style="color:#8C7B6B">Event Date</td>
        <td><strong>{{ $booking->event_date->format('l, d F Y') }}</strong></td>
      </tr>
      <tr>
        <td style="color:#8C7B6B">Location</td>
        <td>{{ $booking->event_location }}</td>
      </tr>
      @if($booking->notes)
      <tr>
        <td style="color:#8C7B6B;vertical-align:top">Notes</td>
        <td><div class="notes-box">{{ $booking->notes }}</div></td>
      </tr>
      @endif
      <tr>
        <td style="color:#8C7B6B">Submitted At</td>
        <td>{{ $booking->created_at->format('d M Y, h:i A') }}</td>
      </tr>
    </table>

    <a href="{{ env('APP_URL', 'http://localhost:8000') }}/admin/bookings" class="action-btn">
      View in Dashboard →
    </a>
  </div>

  <div class="footer">
    This is an automated notification from your Lumière Studios booking system.<br>
    <a href="mailto:hello@lumiere.co.tz">hello@lumiere.co.tz</a>
    &bull; +255 754 123 456
  </div>

</div>
</body>
</html>