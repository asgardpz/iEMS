<x-layouts.app :title="__('QRInuse')">
  <div class="content">
    <h2>INUSE</h2>
    <p>DeviceID: <strong>{{ request()->query('device_id') }}</strong></p>
    <div id="map" style="height: 240px; margin-bottom: 12px;"></div>
    <button id="btn_use">Use</button>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
      const map = L.map('map').setView([23.5, 121], 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

      document.getElementById("btn_use").addEventListener("click", async () => {
        const sessionId = "{{ request()->query('session_id') }}";
        const resp = await fetch(`/api/login/qrcode/confirm?session_id=${encodeURIComponent(sessionId)}`);
        const data = await resp.json();
        alert(`${data.result}: ${data.message}`);
      });
    </script>
  </div>
</x-layouts.app>
