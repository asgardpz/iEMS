<x-layouts.app :title="__('maintenance')">
  @if(session('success'))
  <script>
      alert("{{ session('success') }}");
  </script>
  @endif

  <link rel="stylesheet" href="{{ asset('css/maintenance.css') }}?v={{ time() }}">
  <div id="report-content" style="padding:24px; box-sizing:border-box; width:100%; margin:0; display:flex; flex-direction:column; gap:24px; background-color:#0f172a; color:#e2e8f0;">
    <div class="top-section">
      <!-- ====== Metrics Row ====== -->
      <div style="display:flex; gap:24px;">
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          Today's faults
          <div>{{ number_format($todays_faults ?? 0) }}</div>
        </div>
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Created Work Orders</div>
          <div>{{ number_format($created_work_orders ?? 0) }}</div>
        </div>
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Mean Time To Repair(MTTR)/hrs</div>
          <div>{{ number_format($mttr ?? 0) }}</div>
        </div>
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Offline Devices</div>
          <div>{{ number_format($offline_devices ?? 0) }}</div>
        </div>
      </div>

      <!-- Two Table -->
      <div class="middle-section">
          <!-- Today's Faults -->
          <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px; display: flex; flex-direction: column; height:230px;">
            Today's Faults
            <table class="device-table">
              <thead>
                <tr>
                  <th onclick="sortFaults('time')">Time<span class="sort-icon" id="sort-time"></span></th>
                  <th onclick="sortFaults('device_id')">Device ID<span class="sort-icon" id="sort-device_id"></span></th>
                  <th onclick="sortFaults('location')">Location<span class="sort-icon" id="sort-location"></span></th>
                  <th onclick="sortFaults('priority')">Priority<span class="sort-icon" id="sort-priority"></span></th>
                  <th onclick="sortFaults('maintenance_items')">Maintenance Items<span class="sort-icon" id="sort-maintenance_items"></span></th>
                </tr>
              </thead>
              <tbody id="todaysFaultsBody"></tbody>
            </table>
          </div>

          <!-- Maintenance Schedule -->
          <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px; display: flex; flex-direction: column; height:230px;">
            Maintenance Schedule (Weekly)
            <table class="device-table">
              <thead>
                <tr>
                  <th onclick="sortSchedule('date')">Date<span class="sort-icon" id="sort-date"></span></th>
                  <th onclick="sortSchedule('device_id')">Device ID<span class="sort-icon" id="sort-device_id"></span></th>
                  <th onclick="sortSchedule('maintenance_items')">Maintenance Items<span class="sort-icon" id="sort-maintenance_items"></span></th>
                  <th onclick="sortSchedule('assignee')">Assignee<span class="sort-icon" id="sort-assignee"></span></th>
                  <th onclick="sortSchedule('repair_status')">Status<span class="sort-icon" id="sort-repair_status"></span></th>
                </tr>
              </thead>
              <tbody id="maintenanceScheduleBody"></tbody>
            </table>
          </div>
      </div>

      <!-- 下一個可查詢 Maintenance Table -->
      <div class="grid" >
        <section class="panel devices">
          <div class="monitoring-container">
            <div class="query-section wide">
              <div class="query-main">
                <div class="query-box query-main-box">
                  Work Order Search
                  <!-- 第一行 -->
                  <div class="query-row">

                    <div class="field">
                      <label>Work Order Code</label>
                      <input type="text" id="workOrderInput" name="work_order_code" class="input" style="width:200px;" list="workOrderOptions">
                      <datalist id="workOrderOptions">
                        @foreach($work_order_code as $w)
                          <option value="{{ $w->work_order_code }}"></option>
                        @endforeach
                      </datalist>
                    </div>


                    <div class="field">
                      &nbsp;&nbsp;&nbsp;
                      &nbsp;&nbsp;&nbsp;
                      <label>Device ID</label>
                      <!-- 單一輸入框 + datalist -->
                      <input type="text" id="deviceInput" name="device_id" class="input" style="width:200px;" list="deviceOptions">

                      <datalist id="deviceOptions">
                        @foreach($device_id as $d)
                          <option value="{{ $d->device_id }}"></option>
                        @endforeach
                      </datalist>
                    </div>

                    <div class="field">
                      &nbsp;&nbsp;&nbsp;
                      &nbsp;&nbsp;&nbsp;
                      <label>Priority</label>
                      <select name="priority" class="select">
                        <option value="">All</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                      </select>
                    </div>

                    <div class="field">
                      &nbsp;&nbsp;&nbsp;
                      &nbsp;&nbsp;&nbsp;
                      <label>Assignee</label>
                      <select name="assignee" class="select">
                        <option value="">All</option>
                        @foreach($assignee as $a)
                          <option value="{{ $a->assignee }}">{{ $a->assignee }}</option>
                        @endforeach
                      </select>
                    </div>
                    &nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-primary">Apply</button>
                    <button type="button" class="btn btn-clear">Reset</button>
                  </div>
                </div>
              </div>

              <div class="query-right">
                <button id="expandBtn" class="expand-btn">
                  <svg width="41" height="34" viewBox="0 0 41 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0H35C38.3137 0 41 2.68629 41 6V34H6C2.68629 34 0 31.3137 0 28V0Z" fill="#0049B7"/>
                    <!-- 箭頭路徑加上 id -->
                    <path id="arrowPath" d="M11 23L21 11L31 23" stroke="#2CCDFE" stroke-width="4" stroke-linecap="round"/>
                  </svg>
                </button>
              </div>

            </div>
            <!-- ====== Maintenance Table ====== -->
            <table class="device-table">
              <thead>
                <tr>
                  <th onclick="sortTable('work_order_code')" data-field="Work Order ID">work_order_code<span class="sort-icon" id="sort-work_order_code"></span></th>
                  <th onclick="sortTable('created_time')" data-field="created_time">Created Time<span class="sort-icon" id="sort-created_time"></span></th>
                  <th onclick="sortTable('device_id')" data-field="device_id">Device ID<span class="sort-icon" id="sort-device_id"></span></th>
                  <th onclick="sortTable('location')" data-field="location">Location<span class="sort-icon" id="sort-location"></span></th>
                  <th onclick="sortTable('priority')" data-field="priority">Priority<span class="sort-icon" id="sort-priority"></span></th>
                  <th onclick="sortTable('assignee')" data-field="assignee">Assignee<span class="sort-icon" id="sort-assignee"></span></th>
                  <th onclick="sortTable('repair_status')" data-field="repair_status">Status<span class="sort-icon" id="sort-repair_status"></span></th>
                  <th>Update</th>
                  <th onclick="sortTable('inspection_date')" data-field="inspection_date">Last Updatedate<span class="sort-icon" id="sort-inspection_date"></span></th>
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

<!-- ====== Maintenance Table ====== -->
@php
  $data = $work_orders->map(function($row){
      // 安全取值（避免 null 字面量）
      $city     = $row->city     ?? '';
      $district = $row->district ?? '';
      $address  = $row->address  ?? ''; // 用 address，不用不存在的 street

      return [
          'work_order_code' => (string) $row->work_order_code,
          'created_time'      => $row->created_time 
                              ? \Carbon\Carbon::parse($row->created_time)->format('Y/m/d H:i:s') 
                              : '',
          'device_id'      => (string) $row->device_id,
          'priority'      => (string) $row->priority,
          'assignee'      => (string) $row->assignee,
          'location'       => trim(($row->district ?? '') . ' ' . ($row->city ?? '')),
          'repair_status'      => (string) $row->repair_status,
          'inspection_date'      => $row->inspection_date 
                              ? \Carbon\Carbon::parse($row->inspection_date)->format('Y/m/d H:i:s') 
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
<!-- ====== Maintenance ====== -->
<script>
  const chartData = {!! json_encode($data) !!};
  let deviceData = [...chartData];
  let filteredData = [...deviceData];
  let currentPage = 1;
  let pageSize = 4;
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
    if (s === 'completed')   return 'tag tag-completed';
    if (s === 'pending')     return 'tag tag-pending';
    if (s === 'in progress') return 'tag tag-inprogress';
    return 'tag tag-default';
  }

  function priorityClass(status) {
    const s = String(status || '').toLowerCase();
    if (s === 'high')   return 'priority priority-high';
    if (s === 'medium') return 'priority priority-medium';
    if (s === 'low')    return 'priority priority-low';
    return 'priority priority-default';
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
        <td>${row.work_order_code}</td>
        <td>${row.created_time}</td>
        <td>${row.device_id}</td>
        <td>${row.location}</td>
        <td><span class="${priorityClass(row.priority)}">${row.priority}</span></td>
        <td>${row.assignee}</td>
        <td><span class="${statusClass(row.repair_status)}">${row.repair_status}</span></td>
        <td>
          <a href="{{ route('workorders.index') }}?work_order_code=${row.work_order_code}" class="btn btn-update" style="display:inline-flex;align-items:center;gap:8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v6h6M20 20v-6h-6M5 19a9 9 0 0014-7V9l-3 3M19 5a9 9 0 00-14 7v4l3-3" />
            </svg>
          </a>
        </td>
        <td>${row.inspection_date}</td>
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
      fontSize: "14px",
      alignItems: "center"
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

    // 建立按鈕
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

    // Page + Go
    const pageWrapper = document.createElement("div");
    Object.assign(pageWrapper.style, {
      display: "flex",
      alignItems: "center",
      gap: "6px",
      marginLeft: "16px"
    });

    const label = document.createElement("span");
    label.textContent = "Page";
    label.style.color = "#ffffff";

    const input = document.createElement("input");
    input.type = "number";
    input.min = "1";
    input.max = totalPages;
    Object.assign(input.style, {
      width: "60px",
      height: "30px",
      border: "1px solid #475569",
      borderRadius: "4px",
      padding: "0 6px"
    });

    const goBtn = document.createElement("button");
    goBtn.textContent = "Go";
    Object.assign(goBtn.style, {
      backgroundColor: "#3b82f6",
      color: "#fff",
      border: "none",
      borderRadius: "4px",
      padding: "4px 10px",
      cursor: "pointer"
    });
    goBtn.disabled = true;

    // 輸入監聽
    input.addEventListener("input", () => {
      goBtn.disabled = !input.value;
    });

    // Go 按鈕事件
    goBtn.addEventListener("click", () => {
      const pageNum = parseInt(input.value, 10);
      if (!isNaN(pageNum) && pageNum >= 1 && pageNum <= totalPages) {
        currentPage = pageNum;
        renderTable(filteredData);
      }
    });

    pageWrapper.appendChild(label);
    pageWrapper.appendChild(input);
    pageWrapper.appendChild(goBtn);
    container.appendChild(pageWrapper);
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
      const workOrderVal = document.querySelector('input[name="work_order_code"]')?.value.trim() || '';
      const deviceVal    = document.querySelector('input[name="device_id"]')?.value.trim() || '';
      const priorityVal  = (document.querySelector('select[name="priority"]')?.value || '').trim().toLowerCase();
      const assigneeVal  = (document.querySelector('select[name="assignee"]')?.value || '').trim().toLowerCase();

      const startDateVal = document.getElementById('startDateInput')?.value || '';
      const endDateVal   = document.getElementById('endDateInput')?.value || '';

      // === 新增判斷：startDate 比三個月前還小就彈 ===
      if (startDateVal) {
          const startDate = new Date(startDateVal);

          // 三個月前的日期
          const threeMonthsAgo = new Date();
          threeMonthsAgo.setMonth(threeMonthsAgo.getMonth() - 3);
          // 再往前一天
          threeMonthsAgo.setDate(threeMonthsAgo.getDate() - 1);

          if (startDate < threeMonthsAgo) {
              document.getElementById('customAlert').style.display = 'block';
              return; // 停止，不繼續篩選
          }
      }

      // === 篩選邏輯 ===
      filteredData = deviceData.filter(row => {
          const matchWorkOrder = !workOrderVal || String(row.work_order_code || '').includes(workOrderVal);
          const matchDevice    = !deviceVal    || String(row.device_id || '').includes(deviceVal);
          const matchPriority  = !priorityVal  || String(row.priority || '').toLowerCase() === priorityVal;
          const matchAssignee  = !assigneeVal  || String(row.assignee || '').toLowerCase() === assigneeVal;

          let matchDate = true;
          if (startDateVal || endDateVal) {
              const rowDate = new Date(row.date_time);
              if (startDateVal) matchDate = matchDate && rowDate >= new Date(startDateVal);
              if (endDateVal)   matchDate = matchDate && rowDate <= new Date(endDateVal);
          }

          return matchWorkOrder && matchDevice && matchPriority && matchAssignee && matchDate;
      });

      currentPage = 1;
      renderTable(filteredData);
  }

  // 關閉視窗
  function closeAlert() {
      document.getElementById('customAlert').style.display = 'none';
  }

  // 繼續查詢
  function proceedQuery() {
      document.getElementById('customAlert').style.display = 'none';
      applyFilters(); // 再次執行篩選
  }


  function resetFilters() {
    // 清空所有 input / select
    document.querySelectorAll('.query-box input, .query-box select').forEach(el => el.value = '');

    // 重置資料
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

<!-- ====== Device ID ====== -->
<script>
    // 先把後端資料掛到全域變數
  window.deviceData = @json($data);        // 來自你 map 的 address/district/city 組合
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
    const device = window.deviceData?.find(d => d.device_id == id);
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

  //模糊搜尋 +自動完成
  document.getElementById('itemSelect').addEventListener('change', function() {
    const selected = this.value;
    const datalist = document.getElementById('itemOptions');
    datalist.innerHTML = ''; // 清空

    if (selected === 'transaction_id') {
      @foreach($transaction_id as $t)
        datalist.innerHTML += `<option value="{{ $t->session_id }}"></option>`;
      @endforeach
    } else if (selected === 'device_id') {
      @foreach($device_id as $d)
        datalist.innerHTML += `<option value="{{ $d->device_id }}"></option>`;
      @endforeach
    }
  });

</script>

<!-- ====== Date Time ====== -->
<script>
  // 工具函式：日期轉 yyyy-MM-dd
  function formatDate(date) {
    return date.toISOString().split('T')[0];
  }

  // 預設值：今天 & 往前 3 個月
  const today = new Date();
  const threeMonthsAgo = new Date();
  threeMonthsAgo.setMonth(today.getMonth() - 3);

  document.getElementById('endDateInput').value = formatDate(today);
  document.getElementById('startDateInput').value = formatDate(threeMonthsAgo);

  // 按鈕事件
  document.querySelector('.btn-today').addEventListener('click', () => {
    const yesterday = new Date();
    yesterday.setDate(today.getDate() - 1);
    document.getElementById('startDateInput').value = formatDate(yesterday);
    document.getElementById('endDateInput').value = formatDate(today);
  });

  document.querySelector('.btn-week').addEventListener('click', () => {
    const weekAgo = new Date();
    weekAgo.setDate(today.getDate() - 7);
    document.getElementById('startDateInput').value = formatDate(weekAgo);
    document.getElementById('endDateInput').value = formatDate(today);
  });

  document.querySelector('.btn-month').addEventListener('click', () => {
    const monthAgo = new Date();
    monthAgo.setMonth(today.getMonth() - 1);
    document.getElementById('startDateInput').value = formatDate(monthAgo);
    document.getElementById('endDateInput').value = formatDate(today);
  });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<!-- ====== CSV/PDF ====== -->
<script>
  // 匯出 CSV
  function exportCSV() {
    if (!filteredData || filteredData.length === 0) {
      alert("沒有資料可以匯出");
      return;
    }

    const headers = ["Transaction ID","Date & Time","Device ID","Member","Location","kWh","Amount","Payment Method","Payment Status"];
    const rows = filteredData.map(row => [
      row.transaction_id,
      row.date_time,
      row.device_id,
      row.member,
      row.location,
      row.kwh,
      row.amount,
      row.payment_method,
      row.payment_status
    ]);

    let csvContent = "data:text/csv;charset=utf-8," 
      + headers.join(",") + "\n"
      + rows.map(e => e.join(",")).join("\n");

    const link = document.createElement("a");
    link.setAttribute("href", encodeURI(csvContent));
    link.setAttribute("download", "transactions.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  // 匯出 PDF（改成畫面截圖方式）
  async function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    const reportElement = document.getElementById('report-content'); // 要截圖的區塊

    if (!reportElement) {
      alert('找不到 report-content 區塊');
      return;
    }

    const canvas = await html2canvas(reportElement, { scale: 2 });
    const imgData = canvas.toDataURL('image/png');
    const pageWidth = doc.internal.pageSize.getWidth();
    const imgHeight = canvas.height * pageWidth / canvas.width;

    doc.addImage(imgData, 'PNG', 0, 0, pageWidth, imgHeight);
    doc.save('transactions.pdf');
  }

  // 綁定按鈕
  document.querySelector('.btn-csv')?.addEventListener('click', exportCSV);
  document.querySelector('.btn-pdf')?.addEventListener('click', exportPDF);
</script>

<!-- ====== 超過三個月 ====== -->
<script>
  function doNewQuery() {
      const startDate = document.getElementById('startDateInput').value;
      const endDate   = document.getElementById('endDateInput').value;

      // 改成帶 public 的路徑
      window.location.href = `/iEMS/public/maintenance?startDate=${startDate}&endDate=${endDate}`;
  }

  function doNewQuery() {
      const startDateStr = document.getElementById('startDateInput').value;
      const endDateStr   = document.getElementById('endDateInput').value;

      if (!startDateStr || !endDateStr) {
          alert("請先選擇日期範圍");
          return;
      }

      const startDate = new Date(startDateStr);
      const endDate   = new Date(endDateStr);

      // 計算差距天數
      const diffTime = endDate - startDate;
      const diffDays = diffTime / (1000 * 60 * 60 * 24);

      // 判斷是否超過三個月又一天 (約 91 天)
      if (diffDays > 91) {
          document.getElementById('customAlert').style.display = 'block';
      } else {
          // 正常查詢
          window.location.href = `/iEMS/public/maintenance?startDate=${startDateStr}&endDate=${endDateStr}`;
      }
  }

  function closeAlert() {
      document.getElementById('customAlert').style.display = 'none';
  }

  function proceedQuery() {
      const startDateStr = document.getElementById('startDateInput').value;
      const endDateStr   = document.getElementById('endDateInput').value;
      window.location.href = `/iEMS/public/maintenance?startDate=${startDateStr}&endDate=${endDateStr}`;
  }


</script>

@php
$todaysFaultsData = $todays_faults_table->map(function($row){
    return [
        'time' => $row->time,
        'device_id' => $row->device_id,
        'location' => $row->location,
        'priority' => $row->priority,
        'maintenance_items' => $row->maintenance_items,
    ];
});

$maintenanceScheduleData = $maintenance_schedule->map(function($row){
    return [
        'date' => \Carbon\Carbon::parse($row->date)->format('m/d'),
        'device_id' => $row->device_id,
        'maintenance_items' => $row->maintenance_items,
        'assignee' => $row->assignee,
        'repair_status' => $row->repair_status,
    ];
});
@endphp
<script>
const todaysFaultsData = {!! json_encode($todaysFaultsData) !!};
const maintenanceScheduleData = {!! json_encode($maintenanceScheduleData) !!};

let faultsSortField = null, faultsSortAsc = true;
let scheduleSortField = null, scheduleSortAsc = true;

function priorityClass(status) {
  const s = String(status || '').toLowerCase();
  if (s === 'high') return 'priority priority-high';
  if (s === 'medium') return 'priority priority-medium';
  if (s === 'low') return 'priority priority-low';
  return 'priority priority-default';
}

function statusClass(status) {
  const s = String(status || '').toLowerCase();
  if (s === 'completed') return 'tag tag-completed';
  if (s === 'pending') return 'tag tag-pending';
  if (s === 'in progress') return 'tag tag-inprogress';
  return 'tag tag-default';
}

// 排序 Today's Faults
function sortFaults(field) {
  if (faultsSortField === field) faultsSortAsc = !faultsSortAsc;
  else { faultsSortField = field; faultsSortAsc = true; }
  todaysFaultsData.sort((a,b)=>{
    let A = a[field], B = b[field];
    A = String(A).toLowerCase(); B = String(B).toLowerCase();
    if (A < B) return faultsSortAsc ? -1 : 1;
    if (A > B) return faultsSortAsc ? 1 : -1;
    return 0;
  });
  renderFaults();
  updateSortIcons('faults', field, faultsSortAsc);
}

// 排序 Maintenance Schedule
function sortSchedule(field) {
  if (scheduleSortField === field) scheduleSortAsc = !scheduleSortAsc;
  else { scheduleSortField = field; scheduleSortAsc = true; }
  maintenanceScheduleData.sort((a,b)=>{
    let A = a[field], B = b[field];
    A = String(A).toLowerCase(); B = String(B).toLowerCase();
    if (A < B) return scheduleSortAsc ? -1 : 1;
    if (A > B) return scheduleSortAsc ? 1 : -1;
    return 0;
  });
  renderSchedule();
  updateSortIcons('schedule', field, scheduleSortAsc);
}

// 更新排序箭頭
function updateSortIcons(type, field, asc) {
  const prefix = type === 'faults' ? 'sort-' : 'sort-';
  document.querySelectorAll('.sort-icon').forEach(el => el.textContent = '');
  const icon = document.getElementById(prefix + field);
  if (icon) icon.textContent = asc ? '▲' : '▼';
}

// Render Today's Faults
function renderFaults() {
  const tbody = document.getElementById("todaysFaultsBody");
  tbody.innerHTML = "";
  todaysFaultsData.forEach(row=>{
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${row.time}</td>
      <td style="text-align:left;">
        <span class="pin-wrapper" data-id="${row.device_id}">
          <!-- SVG pin -->
          <svg width="17" height="24" viewBox="0 0 17 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8.55957 0.75C12.3205 0.75 15.3699 3.79871 15.3701 7.55957C15.3701 8.42173 14.9657 9.65287 14.2646 11.0928C13.5765 12.5061 12.6515 14.0247 11.7158 15.4297C10.7819 16.8321 9.84616 18.1085 9.14355 19.0342C8.9245 19.3228 8.72569 19.575 8.55957 19.7881C8.39353 19.5751 8.19546 19.3226 7.97656 19.0342C7.27396 18.1085 6.33825 16.8321 5.4043 15.4297C4.46863 14.0246 3.54358 12.5061 2.85547 11.0928C2.15444 9.65285 1.75 8.42174 1.75 7.55957C1.75023 3.79885 4.79885 0.750232 8.55957 0.75Z" fill="#FF0000" stroke="#FF0000" stroke-width="1.5"/>
            <path d="M1.5 23H15.5" stroke="#FF0000" stroke-width="2" stroke-linecap="round"/>
            <circle cx="8.5" cy="7.5" r="4.5" fill="white"/>
          </svg>
        </span>
        ${row.device_id}
      </td>
      <td>${row.location}</td>
      <td><span style="width:120px;" class="${priorityClass(row.priority)}">${row.priority}</span></td>
      <td>${row.maintenance_items}</td>
    `;
    tbody.appendChild(tr);
  });
}

// Render Maintenance Schedule
function renderSchedule() {
  const tbody = document.getElementById("maintenanceScheduleBody");
  tbody.innerHTML = "";
  maintenanceScheduleData.forEach(row=>{
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${row.date}</td>
      <td style="text-align:left;">
        <span class="pin-wrapper" data-id="${row.device_id}">
          <!-- SVG pin -->
          <svg width="17" height="24" viewBox="0 0 17 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8.55957 0.75C12.3205 0.75 15.3699 3.79871 15.3701 7.55957C15.3701 8.42173 14.9657 9.65287 14.2646 11.0928C13.5765 12.5061 12.6515 14.0247 11.7158 15.4297C10.7819 16.8321 9.84616 18.1085 9.14355 19.0342C8.9245 19.3228 8.72569 19.575 8.55957 19.7881C8.39353 19.5751 8.19546 19.3226 7.97656 19.0342C7.27396 18.1085 6.33825 16.8321 5.4043 15.4297C4.46863 14.0246 3.54358 12.5061 2.85547 11.0928C2.15444 9.65285 1.75 8.42174 1.75 7.55957C1.75023 3.79885 4.79885 0.750232 8.55957 0.75Z" fill="#FF0000" stroke="#FF0000" stroke-width="1.5"/>
            <path d="M1.5 23H15.5" stroke="#FF0000" stroke-width="2" stroke-linecap="round"/>
            <circle cx="8.5" cy="7.5" r="4.5" fill="white"/>
          </svg>
        </span>
        ${row.device_id}
      </td>
      <td>${row.maintenance_items}</td>
      <td>${row.assignee}</td>
      <td><span style="width:120px;" class="${statusClass(row.repair_status)}">${row.repair_status}</span></td>
    `;
    tbody.appendChild(tr);
  });
}

// 初始渲染
renderFaults();
renderSchedule();
</script>

<script>
  const expandBtn = document.getElementById('expandBtn');
  const tableSection = document.querySelector('.grid'); // Work Order Table 外層

  let expanded = false;

  expandBtn.addEventListener('click', () => {
    expanded = !expanded;
    if (expanded) {
      tableSection.classList.add('table-expanded');
      pageSize = 10; // 展開時顯示 10 筆
      // 改成往下箭頭
      document.getElementById('arrowPath')
        .setAttribute('d', 'M11 11L21 23L31 11');
    } else {
      tableSection.classList.remove('table-expanded');
      pageSize = 5; // 收合時顯示 5 筆
      // 改回往上箭頭
      document.getElementById('arrowPath')
        .setAttribute('d', 'M11 23L21 11L31 23');
    }
    currentPage = 1;
    renderTable(filteredData);
  });

</script>
