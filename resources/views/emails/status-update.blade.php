<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Status Update</title>
<style>
  body { margin: 0; padding: 0; background: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
  .wrapper { max-width: 560px; margin: 40px auto; padding: 0 16px; }
  .card { background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(15,23,42,0.08); }
  .header-approved { background: linear-gradient(135deg, #059669, #10b981); padding: 32px 32px 24px; }
  .header-rejected { background: linear-gradient(135deg, #dc2626, #ef4444); padding: 32px 32px 24px; }
  .header-approved h1, .header-rejected h1 { margin: 0; color: #fff; font-size: 20px; font-weight: 700; }
  .header-approved p, .header-rejected p { margin: 6px 0 0; color: rgba(255,255,255,0.8); font-size: 13px; }
  .badge-approved { display: inline-block; background: rgba(255,255,255,0.2); color: #fff; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px; }
  .body { padding: 28px 32px; }
  .status-approved { background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 16px; margin-bottom: 20px; text-align: center; }
  .status-rejected { background: #fff1f2; border: 1px solid #fecdd3; border-radius: 12px; padding: 16px; margin-bottom: 20px; text-align: center; }
  .status-icon { font-size: 32px; margin-bottom: 8px; }
  .status-approved p { margin: 0; font-size: 14px; color: #065f46; font-weight: 600; }
  .status-rejected p { margin: 0; font-size: 14px; color: #9f1239; font-weight: 600; }
  .section-title { font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 10px; }
  .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
  .info-row:last-child { border-bottom: none; }
  .info-label { font-size: 12px; color: #94a3b8; }
  .info-value { font-size: 13px; color: #1e293b; font-weight: 500; }
  .note-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; margin-top: 16px; }
  .note-box p { margin: 0; font-size: 13px; color: #475569; }
  .btn-approved { display: block; text-align: center; background: #059669; color: #fff; text-decoration: none; font-size: 14px; font-weight: 600; padding: 14px 28px; border-radius: 12px; margin: 24px 0 0; }
  .footer { text-align: center; padding: 20px 32px; }
  .footer p { margin: 0; font-size: 11px; color: #94a3b8; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="header-{{ $status }}">
      <div class="badge-approved">{{ ucfirst($type) }} Update</div>
      <h1>Your {{ ucfirst($type) }} has been {{ ucfirst($status) }}</h1>
      <p>Hello {{ $userName }}, here is an update on your submission.</p>
    </div>
    <div class="body">
      <div class="status-{{ $status }}">
        <div class="status-icon">{{ $status === 'approved' ? '✅' : '❌' }}</div>
        <p>{{ $status === 'approved' ? 'Congratulations! Your ' . $type . ' is now live.' : 'Your ' . $type . ' has not been approved at this time.' }}</p>
      </div>

      <p class="section-title">Details</p>
      <div style="background:#f9fbfd; border:1px solid #e5ebf2; border-radius:12px; padding:4px 16px; margin-bottom:16px;">
        <div class="info-row">
          <span class="info-label">{{ ucfirst($type) }}</span>
          <span class="info-value">{{ $itemTitle }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Status</span>
          <span class="info-value">{{ ucfirst($status) }}</span>
        </div>
      </div>

      @if($adminNote)
      <div class="note-box">
        <p><strong>Note from Admin:</strong> {{ $adminNote }}</p>
      </div>
      @endif

      <a href="{{ config('app.url') }}" class="btn-approved">Visit Website →</a>
    </div>
    <div class="footer">
      <p>Hectare Property &nbsp;|&nbsp; {{ now()->format('d M Y, h:i A') }}</p>
    </div>
  </div>
</div>
</body>
</html>