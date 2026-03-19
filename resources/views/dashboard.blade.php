<x-layouts.app :title="__('Dashboard')">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ time() }}">
    <div class="dashboard-container">

      <!-- Top Summary Row -->
      <div style="display: flex; gap: 24px;">
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Devices Online</div>
          <div>{{ $devices_online ?? '0' }}</div>
        </div>
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Charges Today</div>
          <div>{{ $charges_today ?? '0' }}</div>
        </div>
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Real-Time Utilization</div>
          <div>{{ $real_time_utilization ?? '0%' }}</div>
        </div>
      </div>

      <!-- Map + Chart Row -->
      <div style="display: flex; gap: 24px; flex: 1;">
        <!-- Map -->
        <!-- Charging Trend Chart -->
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px; display: flex; flex-direction: column;height:400px;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
              <h6 data-i18n="map">Station Map（Real-Time Status）</h6>
              <button id="expand-map-btn" style="background:none;border:none;cursor:pointer;" title="Expand Map">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
                  <path d="M10 4v2H6v4H4V4h6zm4 0h6v6h-2V6h-4V4zm6 16h-6v-2h4v-4h2v6zM4 14h2v4h4v2H4v-6z" fill="#ef4444"/>
                </svg>
              </button>
            </div>

            <div id="station-map-wrapper" style="position:relative;">
              <div id="station-map" style="height: 300px;"></div>
              <button id="collapse-map-btn"
                      style="display:none;
                            position:absolute;
                            top:12px;
                            right:12px;
                            background:none;
                            border:none;
                            cursor:pointer;
                            z-index:1000;" >
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
                  <path d="M10 4v2H6v4H4V4h6zm4 0h6v6h-2V6h-4V4zm6 16h-6v-2h4v-4h2v6zM4 14h2v4h4v2H4v-6z" fill="#ef4444"/>
                </svg>
              </button>
            </div>


            <p style="font-size:14px; margin-top:6px; color:#999;">Colors:
              <span><span class="dot-online">●</span> Online,</span>
              <span><span class="dot-inuse">●</span> In Use,</span>
              <span><span class="dot-offline">●</span> Offline,</span>
              <span><span class="dot-maintenance">●</span> Maintenance</span>
            </p>
        </div>

        
        <!-- Charging Trend Chart -->
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px; display: flex; flex-direction: column;height:400px;">
          <div style="font-weight:bold; color:#fff;">Charging Trend</div>
          <!-- Controls row -->
          <div style="display:flex; justify-content:space-between; align-items:center; margin:8px 0;">
            <div>
              <button id="btn1D" class="ctrl-Dbtn">1D</button>
              <button id="btn1W" class="ctrl-Dbtn">1W</button>
              <button id="btn1M" class="ctrl-Dbtn">1M</button>
            </div>
            <div style="display:flex; align-items:center; color:#e2e8f0;">
              <input id="startDateInput" type="date" style="width:150px; height:36px; background-color:#fff; color:#000; border:1px solid #475569; border-radius:4px; padding:0 8px;" />
              <span style="margin:0 8px;">–</span>
              <input id="endDateInput" type="date" style="width:150px; height:36px; background-color:#fff; color:#000; border:1px solid #475569; border-radius:4px; padding:0 8px;" />
              <button id="btnSearch" class="ctrl-btn">Search</button>
            </div>
          </div>
          <div >        
            <canvas id="charging-trend-chart" style="width:100%; height:300px;"></canvas>
          </div>
        </div>

      </div>

      <!-- Totals Row -->
      <div style="display: flex; gap: 24px;">
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Total Energy (kWh)</div>
          <div>{{ $total_energy_kwh ?? '0' }}</div>
        </div>
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Total Revenue ($)</div>
          <div>{{ $total_revenue_usd ?? '0' }}</div>
        </div>
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Total CO₂ Reduction (kg)</div>
          <div>{{ $total_co2_reduction_kg ?? '0' }}</div>
        </div>
      </div>

      <!-- Alerts Table -->
      <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px; overflow-y: auto; height:200px;">
        <div style="font-weight: bold; margin-bottom: 12px;">Abnormal Alerts</div>
          <table style="width:100%; border-collapse:collapse; color:#e2e8f0; font-size:14px;">
            <tbody>
              @foreach($abnormal_alerts as $alert)
                <tr style="border-bottom:1px solid #334155;">
                  <td>
                  ID:{{ $alert['device_id'] }}&nbsp;&nbsp;&nbsp;{{ $alert['occurred_at'] }}&nbsp;&nbsp;&nbsp;{{ $alert['alert_type'] }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
      </div>

    </div>
</x-layouts.app>

<!-- ====== Real-Time Monitoring Table ====== -->
@php
  $data = $devices_with_status->map(function($row){
    // 安全取值（避免 null 字面量）
    $city     = $row->city     ?? '';
    $district = $row->district ?? '';
    $address  = $row->address  ?? ''; // 用 address，不用不存在的 street
    $status   = $row->status   ?? '';
    $timeRaw  = $row->timestamp ?? null;

    return [
      'id'      => (string) $row->device_id,                   // 確保為字串 key
      'status'  => strtolower($status),                        // 可能為空字串
      'current' => is_null($row->current_a)     ? 0 : $row->current_a,
      'voltage' => is_null($row->voltage_v)     ? 0 : $row->voltage_v,
      'temp'    => is_null($row->temperature_c) ? 0 : $row->temperature_c,

      // time：若為 null 或不可解析，給空字串
      'time'    => $timeRaw
                    ? \Carbon\Carbon::parse($timeRaw)->format('Y/m/d H:i:s')
                    : '',

      // addr：拼接後再去除多餘空白，全部小寫
     'addr' => trim(
        ($row->address ?? '') .
        ($row->district ? ' ' . $row->district : '') .
        ($row->city ? ' (' . $row->city . ')' : '')
      )
      ,
    ];
  })->values();
@endphp

<!-- ====== Charging Trend Chart ====== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
      const ctx = document.getElementById('charging-trend-chart').getContext('2d');

      // 從 Controller 傳進來的資料
      const allData = @json($all_status_history);

      // 初始化 Chart
      const chart = new Chart(ctx, {
          type: 'line',
          data: {
              labels: [],
              datasets: [
                { type:'line', label:'Energy (kWh)', data: deviceData.map(d => (d.voltage*d.current)/1000), borderColor:'#8b5cf6', backgroundColor:'rgba(139,92,246,0.2)', tension:0.25, yAxisID:'y' },
                { type:'bar', label:'Sessions', data: deviceData.map(d => d.current>0?1:0), backgroundColor:'rgba(6,182,212,0.5)', borderColor:'#06b6d4', yAxisID:'y1' }
              ]
          },
          options: {
              responsive: true,
              maintainAspectRatio: false, // 固定高度
              plugins:{ legend:{ align:'end', labels:{ color:'#ffffff' }} },
              scales: {
                  y: {
                      type: 'linear',
                      position: 'left',
                      title: { display: false, text: 'Energy (kWh)' },
                      ticks: { stepSize: 150, color:'#ffffff' }, // 間隔 150
                      grid: { color: '#ccc' }
                  },
                  y1: {
                      type: 'linear',
                      position: 'right',
                      title: { display: false, text: 'Sessions' },
                      grid: { drawOnChartArea: false,color: '#ccc' },
                      ticks: { stepSize: 150, color:'#ffffff'  } // 間隔 150
                  },
                  x: {
                      title: { display: false, text: 'Time' }, 
                      ticks:{ color:'#ffffff' }, // 👉 X 軸白色
                      grid: { color: '#ccc' }
                  }
              }
          }
      });

      function parseTs(s){
          if(!s) return null;
          const normalized = s.replace(/\//g,'-').replace(' ', 'T');
          const d = new Date(normalized);
          return isNaN(d.getTime()) ? null : d;
      }

      function updateChart(mode, startDate=null, endDate=null) {
          let filtered = allData;

          if(startDate && endDate){
              const start = new Date(startDate);
              const end   = new Date(endDate);
              filtered = filtered.filter(item => {
                  const ts = parseTs(item.timestamp);
                  return ts && ts >= start && ts <= end;
              });
          }

          let labels = [], powerValues = [], sessionValues = [];

          if(mode === '1D'){
              const groupedPower = {}, groupedSessions = {};
              filtered.forEach(item => {
                  const day = item.timestamp.substring(0,10);
                  if(!groupedPower[day]) groupedPower[day] = 0;
                  if(!groupedSessions[day]) groupedSessions[day] = 0;
                  groupedPower[day] += Number(item.power_kw) || 0;
                  groupedSessions[day] += 1; // 每筆算一次充電 session
              });
              labels = Object.keys(groupedPower).sort();
              powerValues = labels.map(d => groupedPower[d]);
              sessionValues = labels.map(d => groupedSessions[d]);
          }
          else if(mode === '1W'){
              const groupedPower = {}, groupedSessions = {};
              filtered.forEach(item => {
                  const dt = parseTs(item.timestamp);
                  if(!dt) return;
                  const year = dt.getFullYear();
                  const startOfYear = new Date(year,0,1);
                  const dayOfYear = Math.floor((dt - startOfYear) / 86400000) + 1;
                  const week = Math.ceil((dayOfYear + startOfYear.getDay()) / 7);
                  const key = `${year}-W${String(week).padStart(2,'0')}`;
                  if(!groupedPower[key]) groupedPower[key] = 0;
                  if(!groupedSessions[key]) groupedSessions[key] = 0;
                  groupedPower[key] += Number(item.power_kw) || 0;
                  groupedSessions[key] += 1;
              });
              labels = Object.keys(groupedPower).sort();
              powerValues = labels.map(k => groupedPower[k]);
              sessionValues = labels.map(k => groupedSessions[k]);
          }
          else if(mode === '1M'){
              const groupedPower = {}, groupedSessions = {};
              filtered.forEach(item => {
                  const month = item.timestamp.substring(0,7);
                  if(!groupedPower[month]) groupedPower[month] = 0;
                  if(!groupedSessions[month]) groupedSessions[month] = 0;
                  groupedPower[month] += Number(item.power_kw) || 0;
                  groupedSessions[month] += 1;
              });
              labels = Object.keys(groupedPower).sort();
              powerValues = labels.map(m => groupedPower[m]);
              sessionValues = labels.map(m => groupedSessions[m]);
          }

          chart.data.labels = labels;
          chart.data.datasets[0].data = powerValues;
          chart.data.datasets[1].data = sessionValues;
          chart.update();
      }

      // 預設顯示 1D
      updateChart('1D');

      // 綁定按鈕事件
      document.getElementById('btn1D').addEventListener('click', ()=>updateChart('1D'));
      document.getElementById('btn1W').addEventListener('click', ()=>updateChart('1W'));
      document.getElementById('btn1M').addEventListener('click', ()=>updateChart('1M'));
      document.getElementById('btnSearch').addEventListener('click', ()=>{
          const start = document.getElementById('startDateInput').value;
          const end = document.getElementById('endDateInput').value;
          updateChart('1D', start, end);
      });
  });
</script>


<!-- ====== Station Map: dynamic markers ====== -->
@php
  $mapdata = $maps
    ->groupBy('station_id')
    ->map(function($group){
      $first = $group->first();
      return [
        'station_id' => $first->station_id,
        'station'    => $first->station_name ?? '',
        'lat'        => $first->latitude ? (float) $first->latitude : null,
        'lng'        => $first->longitude ? (float) $first->longitude : null,
        'devices'    => $group->map(function($row){
          return [
            'device_id' => (string) $row->device_id,
            'status'    => strtolower($row->status ?? ''),
          ];
        })->values()
      ];
    })->values();
@endphp



<!-- ====== Station Map: dynamic markers ====== -->
<script>
  const mapEl = document.getElementById('station-map');
  let map;
  if (mapEl) {
    map = L.map('station-map').setView([25.043, 121.564], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

    const stations = @json($mapdata);
    const markers = [];

    stations.forEach(station => {
      if (!station.lat || !station.lng) return;

      // 計算可用設備數量
      const availableCount = station.devices.filter(d => d.status === 'online' || d.status === 'available').length;
      const totalCount = station.devices.length;

      // 判斷顏色：有可用 → 綠色；全部沒有可用 → 紅色
      const bgColor = availableCount > 0 ? '#16a34a' : '#ef4444';

      // Marker icon：顯示可用數字
      const icon = L.divIcon({
        className: 'custom-marker',
        html: `<div style="background:${bgColor};border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;">
                 ${availableCount}
               </div>`,
        iconSize: [24, 24]
      });

      // Popup：顯示站點與所有設備狀態
      const popupHtml = `
        <strong>${station.station || 'Unknown Station'}</strong><br>
        可用設備：${availableCount} / ${totalCount}<br>
        ${station.devices.map(d => `Device ${d.device_id}: ${d.status}`).join('<br>')}
      `;

      const marker = L.marker([station.lat, station.lng], { icon })
        .addTo(map)
        .bindPopup(popupHtml);

      markers.push(marker);
    });

    if (markers.length > 0) {
      const group = L.featureGroup(markers);
      map.fitBounds(group.getBounds());
    }
  }

  // ====== 放大／縮小功能 ======
  const expandBtn = document.getElementById("expand-map-btn");
  const collapseBtn = document.getElementById("collapse-map-btn");
  const mapWrapper = document.getElementById("station-map-wrapper");
  const mapDiv = document.getElementById("station-map");

  expandBtn.addEventListener("click", () => {
    mapWrapper.style.position = "fixed";
    mapWrapper.style.top = "0";
    mapWrapper.style.left = "0";
    mapWrapper.style.width = "100vw";
    mapWrapper.style.height = "100vh";
    mapWrapper.style.zIndex = "9999";
    mapWrapper.style.background = "#fff";
    mapDiv.style.height = "100%";
    collapseBtn.style.display = "block";
    document.body.style.overflow = "hidden";

    setTimeout(() => { if (map) map.invalidateSize(); }, 300);
  });

  collapseBtn.addEventListener("click", () => {
    mapWrapper.style.position = "relative";
    mapWrapper.style.width = "auto";
    mapWrapper.style.height = "auto";
    mapDiv.style.height = "320px";
    collapseBtn.style.display = "none";
    document.body.style.overflow = "auto";

    setTimeout(() => { if (map) map.invalidateSize(); }, 300);
  });
</script>


<!-- ====== 折線圖 ====== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Utilization (%)
  const utilHistory = @json($utilization_history).reverse();
  const utilLabels = utilHistory.map((d, i) => {
    const t = new Date(d.timestamp);
    return `${t.getMinutes().toString().padStart(2,'0')}:${t.getSeconds().toString().padStart(2,'0')}`;
  });
  const utilValues = utilHistory.map(d => Number(d.utilization_pct ?? 0));

  new Chart(document.getElementById('utilization-chart'), {
    type: 'line',
    data: {
      labels: utilLabels,
      datasets: [{
        data: utilValues,
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59,130,246,0.1)',
        fill: true,
        tension: 0.3,
        pointRadius: 0
      }]
    },
    options: {
      animation: false,
        plugins: {
          legend: { display: false }
        },
      scales: {
        y: { min: 0, max: 100, ticks: { stepSize: 5 }, grid: { color: '#ccc' } },
        x: { grid: { color: '#ccc' } }
      }
    }
  });

  // Total Power (kWh)
  const powerHistory = @json($power_history).reverse();
  const powerLabels = powerHistory.map((d, i) => {
    const t = new Date(d.timestamp);
    return `${t.getMinutes().toString().padStart(2,'0')}:${t.getSeconds().toString().padStart(2,'0')}`;
  });
  const powerValues = powerHistory.map(d => Number(d.total_power_kw ?? 0));

  new Chart(document.getElementById('power-chart'), {
    type: 'line',
    data: {
      labels: powerLabels,
      datasets: [{
        data: powerValues,
        borderColor: '#f59e0b',
        backgroundColor: 'rgba(245,158,11,0.1)',
        fill: true,
        tension: 0.3,
        pointRadius: 0
      }]
    },
    options: {
      animation: false,
        plugins: {
          legend: { display: false }
        },
      scales: {
        y: { grid: { color: '#ccc' } },
        x: { grid: { color: '#ccc' } }
      }
    }
  });
</script>

<!-- ====== Real-Time Monitoring Table ====== -->
<script>
  const chartData = {!! json_encode($data) !!};
  let deviceData = [...chartData];
  let filteredData = [...deviceData];
  let currentPage = 1;
  let pageSize = 10;
  let sortField = null;
  let sortAsc = true;
  let activeArrowId = null;

  function clampCurrentPage(totalItems) {
    const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;
    return totalPages;
  }

  function statusClass(status) {
    const s = String(status || '').toLowerCase();
    if (s === 'online')      return 'status-box status-online';
    if (s === 'offline')     return 'status-box status-offline';
    if (s === 'maintenance') return 'status-box status-maintenance';
    if (s === 'inuse')       return 'status-box status-inuse';
    if (s === 'available')   return 'status-box status-available';
    return 'status-box status-unknown';
  }

  function renderTable(data) {
    const tbody = document.getElementById("deviceTableBody");
    tbody.innerHTML = "";
    clampCurrentPage(data.length);
    const start = (currentPage - 1) * pageSize;
    const pageData = data.slice(start, start + pageSize);
    pageData.forEach(row => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td style="text-align:left;">
          <span class="pin-wrapper" data-id="${row.id}">
          <svg width="17" height="24" viewBox="0 0 17 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M8.55957 0.75C12.3205 0.75 15.3699 3.79871 15.3701 7.55957C15.3701 8.42173 14.9657 9.65287 14.2646 11.0928C13.5765 12.5061 12.6515 14.0247 11.7158 15.4297C10.7819 16.8321 9.84616 18.1085 9.14355 19.0342C8.9245 19.3228 8.72569 19.575 8.55957 19.7881C8.39353 19.5751 8.19546 19.3226 7.97656 19.0342C7.27396 18.1085 6.33825 16.8321 5.4043 15.4297C4.46863 14.0246 3.54358 12.5061 2.85547 11.0928C2.15444 9.65285 1.75 8.42174 1.75 7.55957C1.75023 3.79885 4.79885 0.750232 8.55957 0.75Z" fill="#FF0000" stroke="#FF0000" stroke-width="1.5"/>
          <path d="M1.5 23H15.5" stroke="#FF0000" stroke-width="2" stroke-linecap="round"/>
          <circle cx="8.5" cy="7.5" r="4.5" fill="white"/>
          </svg>
          </span>
          ${row.id}
        </td>
        <td ><span style="width:120px;" class="${statusClass(row.status)}">${row.status}</span></td>
        <td>${parseFloat(row.current).toFixed(1)}</td>
        <td>${parseFloat(row.voltage).toFixed(1)}</td>
        <td>${parseFloat(row.temp).toFixed(1)}</td>
        <td>${row.time}</td>
        <td>
          <button class="stream-btn" data-id="${row.id}" title="View Stream">
          <svg width="30" height="28" viewBox="0 0 30 28" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M22.75 10.25C22.75 4.96556 23.17 4.03334 21.5284 2.39166C19.8867 0.75 17.2444 0.75 11.96 0.75C6.67556 0.75 4.03334 0.75 2.39166 2.39166C0.75 4.03334 0.75 6.67556 0.75 11.96C0.75 17.2444 0.75 19.8867 2.39166 21.5284C4.03334 23.17 8.96556 23.25 14.25 23.25" stroke="#2186F1" stroke-width="1.5"/>
          <path d="M5.75 5.75H18.25" stroke="#2186F1" stroke-width="2" stroke-linecap="round"/>
          <path d="M5.75 11.75H14.25" stroke="#2186F1" stroke-width="2" stroke-linecap="round"/>
          <path d="M5.75 17.75H11.75" stroke="#2186F1" stroke-width="2" stroke-linecap="round"/>
          <path d="M25.35 23.35L28.75 26.75M27.05 17.4C27.05 13.175 23.625 9.75 19.4 9.75C15.175 9.75 11.75 13.175 11.75 17.4C11.75 21.625 15.175 25.05 19.4 25.05C23.625 25.05 27.05 21.625 27.05 17.4Z" stroke="#2186F1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });
    renderPagination(data.length);
    bindRowEvents();
  }

  function renderPagination(total) {
    const container = document.getElementById("paginationControls");
    container.innerHTML = "";

    // 容器樣式
    Object.assign(container.style, {
      marginTop: "16px",
      display: "flex",
      justifyContent: "center",
      gap: "12px",
      flexWrap: "wrap",
      fontSize: "14px"
    });

    const totalPages = Math.max(1, Math.ceil(total / pageSize));
    const maxVisible = 5;
    const showEllipsis = totalPages > maxVisible + 2;

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    if (showEllipsis) {
      if (currentPage <= 3) {
        startPage = 1;
        endPage = maxVisible;
      } else if (currentPage >= totalPages - 2) {
        startPage = totalPages - maxVisible + 1;
        endPage = totalPages;
      }
    }

    // 建立按鈕（純文字樣式）
    const makeBtn = (label, isActive = false, isDisabled = false, onClick = null, color = "#ffffff") => {
      const b = document.createElement("button");
      b.textContent = label;

      Object.assign(b.style, {
        background: "none",
        border: "none",
        padding: "0",
        margin: "0",
        cursor: isDisabled ? "not-allowed" : "pointer",
        fontSize: "14px",
        lineHeight: "1",
        color: isDisabled ? "#999999" : (isActive ? "#3b82f6" : color),
        textDecoration: isActive ? "underline" : "none",
        fontWeight: isActive ? "500" : "400",
        opacity: isDisabled ? "0.4" : "1"
      });

      if (!isDisabled && onClick) b.onclick = onClick;
      return b;
    };

    // 建立省略號
    const makeEllipsis = () => {
      const s = document.createElement("span");
      s.textContent = "...";
      Object.assign(s.style, {
        color: "#999999",
        fontSize: "14px",
        lineHeight: "1"
      });
      return s;
    };

    // ◀︎ Prev
    container.appendChild(makeBtn("◀︎", false, currentPage <= 1, () => {
      currentPage--;
      renderTable(filteredData);
    }, "#3b82f6"));

    // 頁碼群組
    for (let i = startPage; i <= endPage; i++) {
      container.appendChild(makeBtn(String(i), i === currentPage, false, () => {
        currentPage = i;
        renderTable(filteredData);
      }));
    }

    // ... + 最後一頁
    if (showEllipsis && endPage < totalPages) {
      container.appendChild(makeEllipsis());
      container.appendChild(makeBtn(String(totalPages), totalPages === currentPage, false, () => {
        currentPage = totalPages;
        renderTable(filteredData);
      }));
    }

    // ▶︎ Next
    container.appendChild(makeBtn("▶︎", false, currentPage >= totalPages, () => {
      currentPage++;
      renderTable(filteredData);
    }, "#3b82f6"));
  }



  function updateSortIcons() {
    document.querySelectorAll('.sort-icon').forEach(el => el.textContent = '');
    if (sortField) {
      const icon = document.getElementById(`sort-${sortField}`);
      if (icon) icon.textContent = sortAsc ? '▲' : '▼';
    }
  }

  function sortTable(field) {
    if (sortField === field) sortAsc = !sortAsc;
    else { sortField = field; sortAsc = true; }

    filteredData.sort((a, b) => {
      let A = a[field], B = b[field];
      const numA = parseFloat(A), numB = parseFloat(B);
      if (!Number.isNaN(numA) && !Number.isNaN(numB)) { A = numA; B = numB; }
      if (A < B) return sortAsc ? -1 : 1;
      if (A > B) return sortAsc ? 1 : -1;
      return 0;
    });

    currentPage = 1;
    renderTable(filteredData);
    updateSortIcons();
  }

  function bindRowEvents() {
    document.querySelectorAll('.pin-wrapper').forEach(el => {
      el.onclick = () => {
        const id = el.dataset.id;
        toggleArrowRow(id);
      };
    });

    document.querySelectorAll('.stream-btn').forEach(btn => {
      btn.onclick = () => {
        const id = btn.dataset.id;
        openStreamModal(id);
      };
    });
  }

  function applyFilters() {
    const idVal = document.querySelector('input[name="id"]')?.value.trim() || '';
    const statusVal = (document.querySelector('select[name="status"]')?.value || '').trim().toLowerCase();
    const tempOp = document.querySelector('select[name="temp_operator"]')?.value || '';
    const tempVal = parseFloat(document.querySelector('input[name="temp_value"]')?.value || '');
    const cityVal = (document.querySelector('select[name="city"]')?.value || '').trim().toLowerCase();
    const districtVal = (document.querySelector('select[name="district"]')?.value || '').trim().toLowerCase();
    const streetVal = (document.querySelector('input[name="street"]')?.value || '').trim().toLowerCase();

    filteredData = deviceData.filter(row => {
      const matchID = !idVal || String(row.id).includes(idVal);
      const matchStatus = !statusVal || String(row.status).toLowerCase() === statusVal;
      const matchTemp = Number.isNaN(tempVal) || (
        tempOp === 'gte' ? parseFloat(row.temp) >= tempVal :
        tempOp === 'lte' ? parseFloat(row.temp) <= tempVal :
        tempOp === 'eq'  ? parseFloat(row.temp) === tempVal : true
      );
      const addr = String(row.addr || '').toLowerCase();
      const matchAddr =
        (!cityVal || addr.includes(cityVal)) &&
        (!districtVal || addr.includes(districtVal)) &&
        (!streetVal || addr.includes(streetVal));

      return matchID && matchStatus && matchTemp && matchAddr;
    });

    currentPage = 1;
    renderTable(filteredData);
  }

  function resetFilters() {
    document.querySelectorAll('.query-box input, .query-box select').forEach(el => el.value = '');
    filteredData = [...deviceData];
    currentPage = 1;
    renderTable(filteredData);
  }

  function changePageSize() {
    const per = document.querySelector('select[name="per_page"]');
    pageSize = parseInt(per ? per.value : 10);
    currentPage = 1;
    renderTable(filteredData);
  }

  document.querySelector('.btn-primary')?.addEventListener('click', applyFilters);
  document.querySelector('.btn-clear')?.addEventListener('click', resetFilters);
  document.querySelector('select[name="per_page"]')?.addEventListener('change', changePageSize);

  renderTable(filteredData);
</script>

<!-- ====== Device/Data Stream ====== -->
<script>
    // 先把後端資料掛到全域變數
  window.deviceData = @json($data);        // 來自你 map 的 address/district/city 組合
  window.deviceLogs = @json($device_logs); // 來自 Controller 的 logs 陣列

  window.activeArrowId = null;
  window.activeStreamId = null;

  // Device ID 視窗：↑ address + district + (city)
  function toggleArrowRow(id) {
    const tbody = document.getElementById("deviceTableBody");
    const targetRow = Array.from(tbody.querySelectorAll("tr"))
      .find(r => r.querySelector(`.pin-wrapper[data-id="${id}"]`));
    if (!targetRow) return;

    if (window.activeArrowId === id) {
      document.getElementById("arrow-row")?.remove();
      window.activeArrowId = null;
      return;
    }

    document.getElementById("arrow-row")?.remove();

    // 從 window.deviceData 取值
    const device = window.deviceData?.find(d => d.id == id);
    const addr = device?.addr || "—";

    const tr = document.createElement("tr");
    tr.id = "arrow-row";
    tr.innerHTML = `<td colspan="7" style="background-color:#1A3258;color:#ffffff;font-weight:bold;padding:8px;text-align:left;">
    <svg width="19" height="13" viewBox="0 0 19 13" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M1.5 10.6887L9.5 2.18872L17.5 10.6887" stroke="#FF0000" stroke-width="3" stroke-linecap="round"/>
    </svg>
    &nbsp;&nbsp;&nbsp;&nbsp;
      ${addr}
    </td>`;
    tbody.insertBefore(tr, targetRow.nextSibling);
    window.activeArrowId = id;
  }

  // DataStream 視窗：藍色 DeviceID + logs
  function openStreamModal(id) {
    const btn = document.querySelector(`.stream-btn[data-id="${id}"]`);
    if (!btn) return;

    const cell = btn.closest("td");
    if (!cell) return;

    let container = cell.querySelector(".stream-popup-container");
    if (!container) {
      container = document.createElement("div");
      container.className = "stream-popup-container";
      container.style.flex = "1";
      container.style.position = "relative";
      cell.insertBefore(container, btn);
    }

    if (window.activeStreamId === id) {
      container.innerHTML = "";
      window.activeStreamId = null;
      return;
    }

    document.querySelectorAll(".stream-popup-container").forEach(c => c.innerHTML = "");

    const logs = Array.isArray(window.deviceLogs?.[id]) ? window.deviceLogs[id] : [];

    const popup = document.createElement("div");
    popup.className = "stream-popup";
    popup.style.position = "absolute";
    popup.style.left = "-200px";
    popup.style.top = "0px";
    popup.style.background = "#fff";
    popup.style.color = "#000";
    popup.style.border = "1px solid #000";
    popup.style.padding = "8px";
    popup.style.width = "240px";
    popup.style.fontSize = "13px";
    popup.style.zIndex = "9999";
    popup.style.textAlign = "left";

    // 新增：可拉大小 + 超過 10 筆出現 Scroll
    popup.style.maxHeight = "200px";   // 預設高度，約可放 10 筆
    popup.style.overflowY = "auto";    // 超過高度時出現右側 Scroll
    popup.style.resize = "both";       // 右下角可拉大拉小
    popup.style.boxSizing = "border-box";

    const logHtml = logs.length
      ? logs.map(l => `<div>${l.timestamp} - ${l.status}</div>`).join("")
      : `<div>No logs available</div>`;

    popup.innerHTML = `
      <div style="font-weight:bold;margin-bottom:6px;">
        <span style="color:#000;">ID:</span>
        <span style="color:#1D4ED8;"> ${id}</span>
        <span style="float:right;cursor:pointer;color:#000;"
              onclick="this.closest('.stream-popup').remove(); window.activeStreamId=null;">✕</span>
      </div>
      ${logHtml}
    `;
    container.appendChild(popup);
    window.activeStreamId = id;
  }
</script>


 <!-- 引入 html5-qrcode -->
<script src="https://unpkg.com/html5-qrcode"></script>
<div id="reader" style="width:300px"></div>
<script>
  document.getElementById("scanBtn").addEventListener("click", function() {
    const html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
      { facingMode: "environment" }, // 後鏡頭
      {
        fps: 10,
        qrbox: 250
      },
      qrCodeMessage => {
        console.log("掃描結果:", qrCodeMessage);
        // 這裡可以直接導向掃描到的網址
        window.location.href = qrCodeMessage;
        html5QrCode.stop();
      },
      errorMessage => {
        // 掃描失敗時的訊息
        console.warn("掃描中:", errorMessage);
      }
    ).catch(err => {
      console.error("相機開啟失敗:", err);
    });
  });
</script>
<!-- 日歷格式 -->
<script>
  // 設定預設日期：開始日期 = 今天往前一個月；結束日期 = 今天
  const today = new Date();
  const endDate = today.toISOString().split('T')[0];
  const startDate = new Date();
  startDate.setMonth(startDate.getMonth() - 1);
  const startDateStr = startDate.toISOString().split('T')[0];

  document.getElementById('startDateInput').value = startDateStr;
  document.getElementById('endDateInput').value = endDate;

  // 改成藍色日曆選擇器（不同瀏覽器支援度略有差異）
  const style = document.createElement('style');
  style.innerHTML = `
    input[type="date"]::-webkit-calendar-picker-indicator {
      filter: invert(29%) sepia(92%) saturate(746%) hue-rotate(180deg) brightness(95%) contrast(90%);
    }
  `;
  document.head.appendChild(style);
</script>

