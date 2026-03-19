<x-layouts.app :title="__('Reports & Analytics')">
  <link rel="stylesheet" href="{{ asset('css/report.css') }}?v={{ time() }}">
  <div id="report-content" style="padding:24px; box-sizing:border-box; width:100%; margin:0; display:flex; flex-direction:column; gap:24px; background-color:#0f172a; color:#e2e8f0;">

    <!-- Top Summary Row -->
    <div style="display: flex; gap: 24px;">
      <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
        <div style="font-weight: bold;">Energy Consumption This Month (kWh)</div>
        <div>{{ $total_charging_sessions ?? '0' }}</div>
      </div>
      <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
        <div style="font-weight: bold;">Charging Sessions This Month</div>
        <div>{{ $total_energy_consumption ?? '0' }}</div>
      </div>
      <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
        <div style="font-weight: bold;">Revenue This Month($)</div>
        <div>{{ $total_revenue ?? '0' }}</div>
      </div>
      <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
        <div style="font-weight: bold;">Average Utilization</div>
        <div>{{ isset($avg_utilization_rate) ? number_format((float)$avg_utilization_rate, 2) : '0.00' }}</div>
      </div>
    </div>

    <!-- Controls + Label Block -->
    <div style="background-color:#1e293b; border:1px solid #3b82f6; border-radius:8px; padding:12px 16px; display:flex; flex-direction:column; gap:8px;">
      <!-- 上層：Controls Row -->
      <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
        Dimension<select id="dimensionSelect" style="width:160px; height:36px; background-color:#fff; color:#000; border:1px solid #475569; border-radius:4px; padding:0 8px;">
          <option value="Daily">Daily</option>
          <option value="Month">Month</option>
          <option value="Year">Year</option>
        </select>
        Station<select id="stationSelect" style="width:200px; height:36px; background-color:#fff; color:#000; border:1px solid #475569; border-radius:4px; padding:0 8px;">
          <option value="all">All</option>
          @foreach($stations ?? [] as $station)
            <option value="{{ $station->name }}">{{ $station->name }}</option>
          @endforeach
        </select>
        <input id="startDateInput" type="date" style="width:150px; height:36px; background-color:#fff; color:#000; border:1px solid #475569; border-radius:4px; padding:0 8px;" />
        <span style="color:#e2e8f0;">–</span>
        <input id="endDateInput" type="date" style="width:150px; height:36px; background-color:#fff; color:#000; border:1px solid #475569; border-radius:4px; padding:0 8px;" />
        <button id="btnApply" class="ctrl-btn">Apply</button>
        <button id="btnReset" class="ctrl-btn">Reset</button>
        <button id="btnExportPDF" class="ctrl-btn">Export PDF</button>
        <button id="btnExportCSV" class="ctrl-btn">Export CSV</button>
      </div>
      <!-- 下層：Label Row -->
      <div style="display:flex; gap:24px; align-items:center; font-size:14px; color:#e2e8f0;">
        <span id="labelDimension">Dimension: Daily</span>
        <span id="labelStation">Station: All</span>
        <span id="labelRange">Range: 2025/01/01 ~ 2025/01/31</span>
      </div>
    </div>

    <!-- Row 1 -->
    <div style="display:flex; gap:24px; width:100%;">
      <div style="flex:1; min-width:0; background-color:#1e293b; border:1px solid #3b82f6; border-radius:8px; padding:16px; height:160px;">
        Charging Trend  
        <canvas id="charging-trend-chart" style="width:100%; height:150px;"></canvas>
      </div>
      <div style="flex:1; min-width:0; background-color:#1e293b; border:1px solid #3b82f6; border-radius:8px; padding:16px; height:160px;">
        Station Performance Ranking
        <canvas id="station-ranking-chart" style="width:100%; height:150px;"></canvas>
      </div>
    </div>

    <!-- Row 2 -->
    <div style="display:flex; gap:24px; width:100%;">
      <div style="flex:1; min-width:0; background-color:#1e293b; border:1px solid #3b82f6; border-radius:8px; padding:16px; height:160px;">
        Member vs Non-Member
        <canvas id="member-ratio-chart" style="width:100%; height:150px;"></canvas>
      </div>

      <div style="flex:1; min-width:0; background-color:#1e293b; border:1px solid #3b82f6; border-radius:8px; padding:16px; height:160px;">
        Time Heatmap
        <canvas id="time-heatmap" style="width:100%; height:150px;"></canvas>
      </div>

    </div>

    <!-- Row 3 -->
    <div style="display:flex; gap:24px; width:100%;">
      <div style="flex:1; min-width:0; background-color:#1e293b; border:1px solid #3b82f6; border-radius:8px; padding:16px; height:160px;">
        Payment Method
        <canvas id="payment-method-chart" style="width:100%; height:150px;"></canvas>
      </div>

      <div style="flex:1; min-width:0; background-color:#1e293b; border:1px solid #3b82f6; border-radius:8px; padding:16px; height:160px; overflow:auto;">
        Station Summary
        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr >
              <th style="padding:8px; text-align:center;">Station</th>
              <th style="padding:8px; text-align:center;">kWh</th>
              <th style="padding:8px; text-align:center;">Sessions</th>
              <th style="padding:8px; text-align:center;">Utilization</th>
              <th style="padding:8px; text-align:center;">Revenue</th>
            </tr>
          </thead>
          <tbody id="station-summary-body">
            <!-- JS 會插入 -->
          </tbody>
        </table>
      </div>

    </div>

  </div>
</x-layouts.app>


<!-- ====== Real-Time Monitoring Table 原有資料 (保留) ====== -->
@php
  $data = $devices_with_status->map(function($row){
    return [
      'id'      => (string) $row->device_id,
      'status'  => strtolower($row->status ?? ''),
      'current' => is_null($row->current_a) ? 0 : (float) $row->current_a,
      'voltage' => is_null($row->voltage_v) ? 0 : (float) $row->voltage_v,
      'temp'    => is_null($row->temperature_c) ? 0 : (float) $row->temperature_c,
      'time'    => $row->timestamp ? \Carbon\Carbon::parse($row->timestamp)->format('Y/m/d H:i:s') : '',
      'addr'    => trim(($row->address ?? '') . ($row->district ? ' '.$row->district : '') . ($row->city ? ' ('.$row->city.')' : '')),
    ];
  })->values();
@endphp

<!-- ====== Report & Analytics 資料預處理（以 map 固化） ====== -->
@php
  // 1) Energy & Sessions (按日對齊，用 union 日期，kWh 不得為負)
  $chargingSessionsDailyPrepared = collect($charging_sessions_daily)->map(function($r){
    return [
      'date'     => \Carbon\Carbon::parse($r->date)->format('m-d'),
      'sessions' => (int) ($r->sessions ?? 0),
    ];
  })->values();

  $energyConsumptionDailyPrepared = collect($energy_consumption_daily)->map(function($r){
    $kwh = (float) ($r->total_kwh ?? 0);
    return [
      'date' => \Carbon\Carbon::parse($r->date)->format('m-d'),
      'kwh'  => $kwh < 0 ? 0 : $kwh,
    ];
  })->values();

  $dates = $energyConsumptionDailyPrepared->pluck('date')
            ->merge($chargingSessionsDailyPrepared->pluck('date'))
            ->unique()->sort()->values();

  $sessionsByDate = $chargingSessionsDailyPrepared->keyBy('date');
  $kwhByDate      = $energyConsumptionDailyPrepared->keyBy('date');

  $trendData = $dates->map(function($d) use ($sessionsByDate, $kwhByDate){
    return [
      'date'     => $d,
      'kwh'      => isset($kwhByDate[$d]) ? (float) $kwhByDate[$d]['kwh'] : 0,
      'sessions' => isset($sessionsByDate[$d]) ? (int) $sessionsByDate[$d]['sessions'] : 0,
    ];
  })->values();

  // 2) Top 10 Revenue（站點營收長條圖）
  $top10RevenueData = collect($top10_revenue)->map(function($r){
    return [
      'station' => (string) ($r->name ?? $r->station ?? ''),
      'revenue' => (float) ($r->total_revenue ?? 0),
    ];
  })->values();

  // 3) Member vs Non-Member（圓餅圖）
  $memberRatioData = collect($member_ratio)->map(function($r){
    return [
      'label' => (string) ($r->type ?? 'unknown'),
      'value' => (int) ($r->count ?? 0),
    ];
  })->values();

  // 4) Payment Method Distribution（圓餅圖，若有提供）
  $paymentMethodData = collect($payment_method_distribution ?? [])->map(function($r){
    return [
      'label' => (string) ($r->method ?? 'Unknown'),
      'value' => (int) ($r->count ?? 0),
    ];
  })->values();

  // 5) Time Heatmap（Bubble：weekday × hour，半徑 = density）
  $heatmapPrepared = collect($heatmap_data)->map(function($r){
    return [
      'weekday' => (int) ($r->weekday ?? 0),
      'hour'    => (int) ($r->hour ?? 0),
      'density' => (int) ($r->density ?? 0),
    ];
  })->values();

  // 6) Station Summary（表格：站點、kWh、sessions、utilization、revenue）
  $station_summary = collect($station_summary ?? [])->map(function($r){
    $kwh        = (float) ($r->kwh ?? 0);
    $sessions   = (int)   ($r->sessions ?? 0);
    $util       = $r->utilization ?? null; // 可能為 null
    $revenue    = $r->revenue ?? null;     // 可能為 null

    return [
      'station'     => (string) ($r->station ?? $r->name ?? ''),
      'kwh'         => $kwh < 0 ? 0 : $kwh,
      'sessions'    => $sessions < 0 ? 0 : $sessions,
      'utilization' => is_null($util) ? null : (float) $util,
      'revenue'     => is_null($revenue) ? null : (float) $revenue,
    ];
  })->values();

@endphp

<script>
  const rawTrendData   = @json($trendData);
  const top10Revenue   = @json($top10RevenueData);
  const memberRatio    = @json($memberRatioData);
  const paymentMethod  = @json($paymentMethodData);
  const heatmap        = @json($heatmapPrepared);
  const stationSummary = @json($station_summary);

  let charts = {};

  function aggregateTrendData(dimension, startDate, endDate) {
    const grouped = {};
    rawTrendData.forEach(d => {
      const dateObj = new Date(d.date);
      let key = dimension==='Month'
        ? dateObj.getFullYear()+'-'+String(dateObj.getMonth()+1).padStart(2,'0')
        : dimension==='Year'
          ? dateObj.getFullYear().toString()
          : d.date;
      if (startDate && endDate) {
        if (d.date < startDate || d.date > endDate) return;
      }
      if (!grouped[key]) grouped[key] = { date:key, kwh:0, sessions:0 };
      grouped[key].kwh += d.kwh;
      grouped[key].sessions += d.sessions;
    });
    return Object.values(grouped).sort((a,b)=>a.date.localeCompare(b.date));
  }

  function refreshAllCharts(dimension, station, start, end) {
    // Charging Trend
    const trendData = aggregateTrendData(dimension, start, end);
    if (charts.trend) charts.trend.destroy();
    charts.trend = new Chart(document.getElementById('charging-trend-chart').getContext('2d'), {
      data:{ labels:trendData.map(d=>d.date),
        datasets:[
          {type:'line',label:'Energy (kWh)',data:trendData.map(d=>d.kwh),borderColor:'#8b5cf6',backgroundColor:'rgba(139,92,246,0.2)',tension:0.25,yAxisID:'y'},
          {type:'bar',label:'Sessions',data:trendData.map(d=>d.sessions),backgroundColor:'rgba(6,182,212,0.5)',borderColor:'#06b6d4',yAxisID:'y1'}
        ]},
      options:{responsive:false,maintainAspectRatio:false,plugins:{ legend:{ align:'end' } },
        scales:{y:{grid: { color: '#ccc' },position:'left',ticks:{color:'#ffffff'},title:{display:false,text:'Energy (kWh)'}},y1:{grid: { color: '#ccc' },position:'right',ticks:{color:'#ffffff'},title:{display:false,text:'Sessions'},grid:{drawOnChartArea:false}},x:{grid: { color: '#ccc' },title:{display:false,text:'Date'},ticks:{color:'#ffffff'}}}}
    });

    // Top10Revenue (受 station 篩選)
    let filteredRevenue = station!=='all' ? top10Revenue.filter(d=>d.station===station) : top10Revenue;
    if (charts.station) charts.station.destroy();
    charts.station = new Chart(document.getElementById('station-ranking-chart').getContext('2d'), {
      type:'bar',
      data:{ labels:filteredRevenue.map(d=>d.station), datasets:[{ label:'Revenue ($)', data:filteredRevenue.map(d=>d.revenue), backgroundColor:'#10b981' }] },
      options:{ responsive:false, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{grid: { color: '#ccc' },beginAtZero:true,ticks:{color:'#ffffff'}}, x:{grid: { color: '#ccc' },ticks:{color:'#ffffff'}}} }
    });

    // Member Ratio
    if (charts.member) charts.member.destroy();
    charts.member = new Chart(document.getElementById('member-ratio-chart').getContext('2d'), {
      type:'pie',
      data:{ labels:memberRatio.map(d=>d.label), datasets:[{ data:memberRatio.map(d=>d.value), backgroundColor:['#3b82f6','#8b5cf6','#10b981','#f59e0b'] }] },
      options:{ responsive:false, maintainAspectRatio:false ,plugins:{ legend:{ align:'end' } },}
    });

    // Payment Method
    if (charts.payment) charts.payment.destroy();
    charts.payment = new Chart(document.getElementById('payment-method-chart').getContext('2d'), {
      type:'pie',
      data:{ labels:paymentMethod.map(d=>d.label), datasets:[{ data:paymentMethod.map(d=>d.value), backgroundColor:['#3b82f6','#8b5cf6','#10b981','#f59e0b'] }] },
      options:{ responsive:false, maintainAspectRatio:false ,plugins:{ legend:{ align:'end' } },}
    });

    // Heatmap
    if (charts.heatmap) charts.heatmap.destroy();
    charts.heatmap = new Chart(document.getElementById('time-heatmap').getContext('2d'), {
      type:'bubble',
      data:{ datasets:heatmap.map(d=>({ label:`${d.weekday}-${d.hour}`, data:[{x:d.hour,y:d.weekday,r:Math.max(d.density,2)}], backgroundColor:'#3b82f6' })) },
      options:{ responsive:false, maintainAspectRatio:false,plugins:{ legend:{ display:false } }, scales:{ x:{title:{display:false,text:'Hour'},min:0,max:23,ticks:{color:'#ffffff'}}, y:{title:{display:false,text:'Weekday'},min:0,max:6,ticks:{color:'#ffffff'}} } }
    });
    
    // Station Summary 表格渲染
    const summaryTable = document.getElementById('station-summary-body');
    if (summaryTable) {
      let filteredSummary = station !== 'all'
        ? stationSummary.filter(d => d.station.trim().toLowerCase() === station.trim().toLowerCase())
        : stationSummary;

      summaryTable.innerHTML = '';
      filteredSummary.forEach(row => {
        summaryTable.innerHTML += `<tr style="background-color:#1e293b; border:none;">
          <td style="padding:8px; text-align:center; border:none;">${row.station}</td>
          <td style="padding:8px; text-align:center; border:none;">${Math.max(0, Math.round(row.kwh))}</td>
          <td style="padding:8px; text-align:center; border:none;">${row.sessions}</td>
          <td style="padding:8px; text-align:center; border:none;">${row.utilization ?? 0}%</td>
          <td style="padding:8px; text-align:center; border:none;">$${row.revenue ?? 0}</td>
        </tr>`;
      });
    }


  }
 
  // Apply
  document.getElementById('btnApply').addEventListener('click', function() {
    const dim     = document.getElementById('dimensionSelect').value;
    const station = document.getElementById('stationSelect').value;
    const start   = document.getElementById('startDateInput').value;
    const end     = document.getElementById('endDateInput').value;
    refreshAllCharts(dim, station, start, end);
    document.getElementById('labelDimension').textContent = 'Dimension: ' + dim;
    document.getElementById('labelStation').textContent   = 'Station: ' + (station==='all'?'All':station);
    document.getElementById('labelRange').textContent     = start && end ? `Range: ${start} ~ ${end}` : 'Range: All';
  });

  // Reset
  document.getElementById('btnReset').addEventListener('click', function() {
    document.getElementById('dimensionSelect').value = 'Daily';
    document.getElementById('stationSelect').value   = 'all';
    document.getElementById('startDateInput').value  = '';
    document.getElementById('endDateInput').value    = '';
    refreshAllCharts('Daily','all','','');
    document.getElementById('labelDimension').textContent = 'Dimension: Daily';
    document.getElementById('labelStation').textContent   = 'Station: All';
    document.getElementById('labelRange').textContent     = 'Range: All';
  });

  // 初始
  refreshAllCharts('Daily','all','','');
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

@verbatim
<script>
  document.getElementById('btnExportPDF').addEventListener('click', async function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    const reportElement = document.getElementById('report-content');
    if (!reportElement) { alert('找不到 report-content 區塊'); return; }

    const canvas = await html2canvas(reportElement, { scale: 2 });
    const imgData = canvas.toDataURL('image/png');
    const pageWidth = doc.internal.pageSize.getWidth();
    const imgHeight = canvas.height * pageWidth / canvas.width;

    doc.addImage(imgData, 'PNG', 0, 0, pageWidth, imgHeight);
    doc.save('report.pdf');
  });
</script>
@endverbatim


<script>
  document.getElementById('btnExportCSV').addEventListener('click', function () {
    let csv = '';

    // ===== Top Summary Row =====
    csv += 'Summary\n';
    const summaryBlocks = document.querySelectorAll('div[style*="font-weight: bold"]');
    summaryBlocks.forEach(block => {
      const label = block.textContent.trim();
      const value = block.nextElementSibling ? block.nextElementSibling.textContent.trim() : '';
      csv += `${label},${value}\n`;
    });

    // ===== Charging Trend =====
    csv += '\nCharging Trend (Date,kWh,Sessions)\n';
    rawTrendData.forEach(d => {
      csv += `${d.date},${d.kwh},${d.sessions}\n`;
    });

    // ===== Top 10 Revenue =====
    csv += '\nTop 10 Revenue (Station,Revenue)\n';
    top10Revenue.forEach(d => {
      csv += `${d.station},${d.revenue}\n`;
    });

    // ===== Member Ratio =====
    csv += '\nMember Ratio (Type,Count)\n';
    memberRatio.forEach(d => {
      csv += `${d.label},${d.value}\n`;
    });

    // ===== Payment Method =====
    csv += '\nPayment Method (Method,Count)\n';
    paymentMethod.forEach(d => {
      csv += `${d.label},${d.value}\n`;
    });

    // ===== Time Heatmap =====
    csv += '\nTime Heatmap (Weekday,Hour,Density)\n';
    heatmap.forEach(d => {
      csv += `${d.weekday},${d.hour},${d.density}\n`;
    });

    // ===== Station Summary =====
    csv += '\nStation Summary (Station,kWh,Sessions,Utilization,Revenue)\n';
    document.querySelectorAll('#station-summary-body tr').forEach(row => {
      const cols = row.querySelectorAll('td');
      const values = Array.from(cols).map(td => td.textContent.replace(/,/g, ''));
      csv += values.join(',') + '\n';
    });

    // ===== Export =====
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'report.csv';
    link.click();
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

