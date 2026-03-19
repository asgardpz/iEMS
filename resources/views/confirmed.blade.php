<x-layouts.app :title="__('QRCode 登入確認')">
  <div class="content">
    <h2>登入成功</h2>
    <p>會員姓名：{{ $name }}</p>
    <p>DeviceID：{{ $device_id }}</p>
    <p>SessionID：{{ $session_id }}</p>
  </div>
</x-layouts.app>
