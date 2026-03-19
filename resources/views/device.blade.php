<x-layouts.app :title="__('Device Management')">
  <link rel="stylesheet" href="{{ asset('css/device.css') }}?v={{ time() }}">
  <div class="content">
    <!-- ====== Panels Grid ====== -->
    <div class="grid">
      <!-- Station Map -->
      <section class="panel devices">
         
        <div style="display:flex; align-items:center; justify-content:space-between;" style="width:100%; height:320px;">
          <h6 data-i18n="map">Station Map（Real-Time Status）</h6>
          <button id="expand-map-btn" style="background:none;border:none;cursor:pointer;" title="Expand Map">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
              <path d="M10 4v2H6v4H4V4h6zm4 0h6v6h-2V6h-4V4zm6 16h-6v-2h4v-4h2v6zM4 14h2v4h4v2H4v-6z" fill="#ef4444"/>
            </svg>
          </button>
        </div>

        <div id="station-map-wrapper" style="position:relative;">
          <div id="station-map" style="height:150px;"></div>
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

      </section>

      <!-- Device Monitoring Table -->
      <section class="panel devices">
          
        <div class="monitoring-container">

          <div class="query-section">
            <div class="query-left"><h2 class="section-title">Device Real-Time Monitoring</h2></div>

          <div class="query-main">
            <div class="query-box query-main-box">
              <!-- 第一行 -->
              <div class="query-row">
                <div class="field">
                  <label>ID&nbsp;&nbsp;&nbsp;&nbsp;</label>
                  <input type="text" name="id" class="input">
                </div>

                <div class="field">
                  <label>Status</label>
                  <select name="status" class="select">
                    <option value="">All</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="online">On line</option>
                    <option value="inuse">In Use</option>
                    <option value="offline">Off line</option>
                  </select>
                </div>
                
                <div class="field">
                  <label>Type</label>
                  <select name="type" class="select">
                    <option value="">All</option>
                    @foreach($types as $t)
                      <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="field">
                  <label>Firmware Version</label>
                  <select name="firmware" class="select">
                    <option value="">All</option>
                    @foreach($firmwares as $f)
                      <option value="{{ $f }}">{{ $f }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <!-- 第二行 -->
              <div class="query-row">
                <div class="field field-button">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <button type="button" class="btn btn-primary">Apply</button>
                  <button type="button" class="btn btn-clear">Reset</button>
                </div>
              </div>

            </div>
          </div>

            <div class="query-right">
              <div class="show-entries-box">
                <div class="show-label">Show</div>
                <select name="per_page" class="select">
                  <option value="5" selected>5</option>
                  <option value="10">10</option>                  
                  <option value="20">20</option>
                  <option value="30">30</option>
                  <option value="40">40</option>
                  <option value="50">50</option>
                </select>
                <div class="entries-label">Entries</div>
              </div>
            </div>
          </div>

          <!-- ====== Real-Time Monitoring Table ====== -->
          <table class="device-table">
            <thead>
              <tr>
                <th onclick="sortTable('id')" data-field="id">Device ID <span class="sort-icon" id="sort-id"></span></th>
                <th onclick="sortTable('code')" data-field="code">Station<span class="sort-icon" id="sort-current"></span></th>
                <th onclick="sortTable('status')" data-field="status">Status <span class="sort-icon" id="sort-status"></span></th>
                <th onclick="sortTable('type')" data-field="type">Power<span class="sort-icon" id="sort-voltage"></span></th>
                <th onclick="sortTable('firmware_version')" data-field="firmware_version">FirmWare <span class="sort-icon" id="sort-temp"></span></th>
                <th onclick="sortTable('timel')" data-field="timel">Last Online <span class="sort-icon" id="sort-time"></span></th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="deviceTableBody"></tbody>
          </table>

          <div id="paginationControls" class="pagination-controls"></div>



        </div>

      </section>
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
    $timelast  = $row->last_online_at ?? null;

    return [
      'id'      => (string) $row->device_id,                   // 確保為字串 key
      'status'  => strtolower($status),                        // 可能為空字串
      'current' => is_null($row->current_a)     ? 0 : $row->current_a,
      'voltage' => is_null($row->voltage_v)     ? 0 : $row->voltage_v,
      'temp'    => is_null($row->temperature_c) ? 0 : $row->temperature_c,

      'status'  => strtolower($status), 
      'code'      => (string) $row->code,
      'type'      => (string) $row->type,
      'firmware_version'      => (string) $row->firmware_version,
      // time：若為 null 或不可解析，給空字串
      'time'    => $timeRaw
                    ? \Carbon\Carbon::parse($timeRaw)->format('Y/m/d H:i:s')
                    : '',

      // time：若為 null 或不可解析，給空字串
      'timel'    => $timelast
                    ? \Carbon\Carbon::parse($timelast)->format('Y/m/d H:i:s')
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

    // 從 Blade 傳入的 data
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

    // 自動縮放到所有 marker 範圍
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

    setTimeout(() => {
      if (map) map.invalidateSize();
    }, 300);
  });

  collapseBtn.addEventListener("click", () => {
    mapWrapper.style.position = "relative";
    mapWrapper.style.width = "auto";
    mapWrapper.style.height = "auto";
    mapDiv.style.height = "320px";
    collapseBtn.style.display = "none";
    document.body.style.overflow = "auto";

    setTimeout(() => {
      if (map) map.invalidateSize();
    }, 300);
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
  let pageSize = 5;
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
        <td>${row.code}</td>
        <td ><span style="width:120px;" class="${statusClass(row.status)}">${row.status}</span></td>
        <td>${row.type}</td>
        <td>${row.firmware_version}</td>
        <td>${row.timel}</td>
        <td>
          <button class="stream-btn" data-id="${row.id}" title="View Stream">
          <svg width="28" height="27" viewBox="0 0 28 27" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g clip-path="url(#clip0_61_302)">
          <path d="M12.1953 5.48401H2.82031V6.53401H12.1953V5.48401Z" fill="#2186F1"/>
          <path d="M12.1023 8.56201H2.82031V9.61201H12.1023V8.56201Z" fill="#2186F1"/>
          <path d="M12.1023 11.643H2.82031V12.693H12.1023V11.643Z" fill="#2186F1"/>
          <path d="M12.1023 14.721H2.82031V15.771H12.1023V14.721Z" fill="#2186F1"/>
          <path d="M12.1023 17.799H2.82031V18.849H12.1023V17.799Z" fill="#2186F1"/>
          <path d="M13.7703 20.88H2.82031V21.93H13.7703V20.88Z" fill="#2186F1"/>
          <path d="M18.0808 18.438L13.6738 19.641L14.8708 15.234L18.0808 18.438Z" fill="#2186F1"/>
          <path d="M23.5527 6.26942L22.4189 7.40552L25.9016 10.8808L27.0353 9.74474L23.5527 6.26942Z" fill="#2186F1"/>
          <path d="M21.759 9.60006L22.179 10.0171L16.392 15.8071L16.737 16.1521L22.524 10.3621L22.944 10.7821L17.16 16.5721L17.505 16.9171L23.286 11.1271L23.709 11.5471L17.922 17.3341L18.69 18.1021L25.551 11.2351L22.068 7.75806L15.207 14.6251L15.972 15.3901L21.759 9.60006Z" fill="#2186F1"/>
          <path d="M19.326 18.738L19.092 18.975V24.966H1.263V1.263H13.803V6.552H19.092V9.465L20.355 8.199V6.858V6.714L20.253 6.612L13.74 0.102L13.641 0H13.497H0.345H0V0.345V25.887V26.232H0.345H20.01H20.355V25.887V17.709L19.326 18.738Z" fill="#2186F1"/>
          </g>
          <defs>
          <clipPath id="clip0_61_302">
          <rect width="27.036" height="26.232" fill="white"/>
          </clipPath>
          </defs>
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

      // 日期欄位用 Date 比較
      if (field === 'date_time') {
        A = new Date(A);
        B = new Date(B);
      }
      // 數字欄位用 parseFloat
      else if (['kwh','amount'].includes(field)) {
        A = parseFloat(A);
        B = parseFloat(B);
      }
      // 其他字串欄位
      else {
        A = String(A).toLowerCase().trim();
        B = String(B).toLowerCase().trim();
      }

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

    const device = window.deviceData?.find(d => d.id == id);
    const addr = device?.addr || "—";

    const tr = document.createElement("tr");
    tr.id = "arrow-row";
    tr.innerHTML = `
      <td colspan="7" style="background-color:#1A3258;color:#ffffff;font-weight:bold;padding:8px;text-align:left;">
        <svg width="19" height="13" viewBox="0 0 19 13" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1.5 10.6887L9.5 2.18872L17.5 10.6887" stroke="#FF0000" stroke-width="3" stroke-linecap="round"/>
        </svg>
        &nbsp;&nbsp;&nbsp;&nbsp;${addr}
      </td>`;
    tbody.insertBefore(tr, targetRow.nextSibling);
    window.activeArrowId = id;
  }

  // DataStream 視窗：藍色 DeviceID + logs + 查詢功能
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
    popup.style.width = "360px";
    popup.style.fontSize = "13px";
    popup.style.zIndex = "9999";
    popup.style.textAlign = "left";


    // 新增：可拉大小 + 超過 10 筆出現 Scroll
    popup.style.height = "320px";      // 初始高度
    popup.style.minHeight = "200px";   // 最小高度（避免太小）
    popup.style.maxHeight = "none";    // 不限制最大高度
    popup.style.overflowY = "auto";    // 超過內容時出現捲軸
    popup.style.resize = "both";       // 可拉大小
    popup.style.boxSizing = "border-box";

    const statusValues = [...new Set(logs.map(l => l.status))];

    // 查詢區塊 HTML（白底黑字 + 日期與 Search 同一行）
    const filterHtml = `
      <div style="margin-bottom:8px;">
        <div style="margin-bottom:4px;">
          <label>Action:</label>
          <select id="filter-status-${id}" style="width:100%;height:28px;background-color:#fff;color:#000;border:1px solid #475569;border-radius:4px;padding:0 8px;">
            <option value="">All</option>
            ${statusValues.map(s => `<option value="${s}">${s}</option>`).join("")}
          </select>
        </div>
        <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;">
          <input type="date" id="filter-start-${id}" style="flex:1;width:100px;height:28px;background-color:#fff;color:#000;border:1px solid #475569;border-radius:4px;padding:0 8px;">
          <span>–</span>
          <input type="date" id="filter-end-${id}" style="flex:1;width:100px;height:28px;background-color:#fff;color:#000;border:1px solid #475569;border-radius:4px;padding:0 8px;">
          <button id="filter-btn-${id}" class="btn btn-clear" style="height:28px;">Search</button>
        </div>
        <hr style="margin-top:8px; border:0; border-top:1px solid #ccc;">
      </div>
    `;

    const logsContainer = document.createElement("div");

    function renderLogs(filteredLogs) {
      const logHtml = filteredLogs.length
        ? filteredLogs.map(l => `<div>${l.timestamp} - ${l.status}</div>`).join("")
        : `<div>No logs available</div>`;
      logsContainer.innerHTML = logHtml;
    }

    renderLogs(logs);

    popup.innerHTML = `
      <div style="font-weight:bold;margin-bottom:6px;">
        <span style="color:#000;">ID:</span>
        <span style="color:#1D4ED8;"> ${id}</span>
        <span style="float:right;cursor:pointer;color:#000;"
              onclick="this.closest('.stream-popup').remove(); window.activeStreamId=null;">✕</span>
      </div>
      ${filterHtml}
    `;
    popup.appendChild(logsContainer);

    container.appendChild(popup);
    window.activeStreamId = id;

    // ====== 日期預設值（開始：今天往前一個月；結束：今天） ======
    const today = new Date();
    const endDate = today.toISOString().split('T')[0];
    const startDate = new Date();
    startDate.setMonth(startDate.getMonth() - 1);
    const startDateStr = startDate.toISOString().split('T')[0];

    const startEl = document.getElementById(`filter-start-${id}`);
    const endEl = document.getElementById(`filter-end-${id}`);
    startEl.value = startDateStr;
    endEl.value = endDate;

    // ====== 藍色日曆選擇器 ======
    if (!document.getElementById('date-blue-style')) {
      const style = document.createElement('style');
      style.id = 'date-blue-style';
      style.innerHTML = `
        input[type="date"]::-webkit-calendar-picker-indicator {
          filter: invert(29%) sepia(92%) saturate(746%) hue-rotate(180deg) brightness(95%) contrast(90%);
        }
      `;
      document.head.appendChild(style);
    }

    // 綁定查詢事件
    document.getElementById(`filter-btn-${id}`).addEventListener("click", () => {
      const statusVal = document.getElementById(`filter-status-${id}`).value;
      const startVal = document.getElementById(`filter-start-${id}`).value;
      const endVal = document.getElementById(`filter-end-${id}`).value;

      const filtered = logs.filter(l => {
        const ts = new Date(l.timestamp);
        const matchStatus = statusVal ? l.status === statusVal : true;
        const matchStart = startVal ? ts >= new Date(startVal) : true;
        const matchEnd = endVal ? ts <= new Date(endVal) : true;
        return matchStatus && matchStart && matchEnd;
      });

      renderLogs(filtered);
    });

    // Reset：回到預設日期 + 全部 logs
    document.getElementById(`reset-btn-${id}`).addEventListener("click", () => {
      renderLogs(logs);
      document.getElementById(`filter-status-${id}`).value = "";
      startEl.value = startDateStr;
      endEl.value = endDate;
    });
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


