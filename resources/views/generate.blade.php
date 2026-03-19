<x-layouts.app :title="__('QRCode 登入產生')">
  <div class="content">
    <h2>產生 QRCode</h2>
    <p>DeviceID：{{ $device_id }}</p>
    <p>SessionID：{{ $session_id }}</p>
    <p>掃描以下 QRCode 進行登入：</p>
    <div>{!! QrCode::size(240)->generate($qr_url) !!}</div>
    <p><a href="{{ $qr_url }}" target="_blank">{{ $qr_url }}</a></p>
  </div>
</x-layouts.app>

