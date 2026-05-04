<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Submission</title>
<style>
  body { margin: 0; padding: 0; background: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
  .wrapper { max-width: 560px; margin: 40px auto; padding: 0 16px; }
  .card { background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(15,23,42,0.08); }
  .header { background: linear-gradient(135deg, #059669, #10b981); padding: 32px 32px 24px; }
  .header h1 { margin: 0; color: #fff; font-size: 20px; font-weight: 700; }
  .header p { margin: 6px 0 0; color: rgba(255,255,255,0.8); font-size: 13px; }
  .badge { display: inline-block; background: rgba(255,255,255,0.2); color: #fff; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px; }
  .body { padding: 28px 32px; }
  .alert-box { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px; }
  .alert-box p { margin: 0; font-size: 13px; color: #92400e; font-weight: 500; }
  .section-title { font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 10px; }
  .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
  .info-row:last-child { border-bottom: none; }
  .info-label { font-size: 12px; color: #94a3b8; }
  .info-value { font-size: 13px; color: #1e293b; font-weight: 500; text-align: right; max-width: 60%; }
  .btn { display: block; text-align: center; background: #059669; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 600; padding: 14px 28px; border-radius: 12px; margin: 24px 0 0; }
  .footer { text-align: center; padding: 20px 32px; }
  .footer p { margin: 0; font-size: 11px; color: #94a3b8; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="header">
      <div class="badge">{{ ucfirst($type) }} Submission</div>
      <h1>New {{ ucfirst($type) }} Waiting for Approval</h1>
      <p>A new {{ $type }} has been submitted and requires your review.</p>
    </div>
    <div class="body">
      <div class="alert-box">
        <p>⏳ This {{ $type }} is pending your approval. Please review it at your earliest convenience.</p>
      </div>

      <p class="section-title">Submission Details</p>
      <div style="background:#f9fbfd; border:1px solid #e5ebf2; border-radius:12px; padding:4px 16px; margin-bottom:20px;">
        <div class="info-row">
          <span class="info-label">{{ ucfirst($type) }} Title</span>
          <span class="info-value">{{ $itemTitle }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Submitted By</span>
          <span class="info-value">{{ $submitterName }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Email</span>
          <span class="info-value">{{ $submitterEmail }}</span>
        </div>
        @foreach($details as $key => $value)
        <div class="info-row">
          <span class="info-label">{{ $key }}</span>
          <span class="info-value">{{ $value }}</span>
        </div>
        @endforeach
      </div>

      <a href="{{ config('app.url') }}/admin" class="btn">Go to Admin Dashboard →</a>
    </div>
    <div class="footer">
      <p>Hectare Property — Admin Notification &nbsp;|&nbsp; {{ now()->format('d M Y, h:i A') }}</p>
    </div>
  </div>
</div>
</body>
</html>