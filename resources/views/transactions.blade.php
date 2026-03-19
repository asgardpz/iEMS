<x-layouts.app :title="__('transactions')">
  <link rel="stylesheet" href="{{ asset('css/transactions.css') }}?v={{ time() }}">
  <div id="report-content" style="padding:24px; box-sizing:border-box; width:100%; margin:0; display:flex; flex-direction:column; gap:24px; background-color:#0f172a; color:#e2e8f0;">
    <div class="content">
      <!-- ====== Metrics Row ====== -->
      <div style="display: flex; gap: 12px; margin-bottom:12px;">
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Today's Revenue (NTD)</div>
          <div>${{ number_format($today_revenue ?? 0) }}</div>
        </div>
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Monthly Revenue (NTD)</div>
          <div>${{ number_format($monthly_revenue ?? 0) }}</div>
        </div>
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Successful Transactions</div>
          <div>{{ number_format($successful_transactions ?? 0) }}</div>
        </div>
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Failure Rate</div>
          <div>{{ number_format($failure_rate ?? 0, 1) }}%</div>
        </div>
      </div>


      <!-- ====== Panels Grid ====== -->
      <div class="grid" >
        <!-- Transactions Table -->
        <section class="panel devices">
          
        <div class="monitoring-container">

          <div class="query-section">
            <div class="query-main">
              <div class="query-box query-main-box">
                <!-- 第一行 -->
                <div class="query-row">
                  
                  <div class="field">
                    <label>Item</label>
                    <select id="itemSelect" name="status" class="select">
                      <option value="transaction_id">Transaction ID</option>
                      <option value="device_id">Device ID</option>
                    </select>

                    <!-- 輸入框 + datalist -->
                    <input type="text" id="itemInput" name="id" class="input" style="width:200px;" list="itemOptions">

                    <datalist id="itemOptions">
                      @foreach($transaction_id as $t)
                        <option value="{{ $t->session_id }}"></option>
                      @endforeach
                      @foreach($device_id as $d)
                        <option value="{{ $d->device_id }}"></option>
                      @endforeach
                    </datalist>
                  </div>

                  <div class="field">
                    <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Location</label>
                    <select name="location" class="select" >
                      <option value="">All</option>
                        @foreach($cities ?? [] as $item)
                          <option value="{{ $item->city }}">{{ $item->city }}</option>
                        @endforeach
                    </select>
                  </div>

                  <div class="field">
                    <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Payment Methods</label>
                    <select name="payment_method" class="select">
                      <option value="">All</option>
                      <option value="CreditCard">CreditCard</option>
                      <option value="RFID">RFID</option>
                      <option value="In-App Wallet">In-App Wallet</option>
                    </select>
                  </div>
                  
                  <div class="field">
                    <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Payment Status</label>
                    <select name="payment_status" class="select" >
                      <option value="">All</option>
                      <option value="Pending">Pending</option>
                      <option value="Paid">Paid</option>
                      <option value="Failed">Failed</option>
                    </select>
                  </div>
                </div>

                <!-- 第二行 -->
                <div class="query-row">
                  <label>Time Range&nbsp;&nbsp;&nbsp;</label>
                  <button type="button" class="btn btn-today">&nbsp;&nbsp;&nbsp;Today</button>
                  <button type="button" class="btn btn-week">&nbsp;&nbsp;&nbsp;This Week</button>
                  <button type="button" class="btn btn-month">&nbsp;&nbsp;&nbsp;This Month</button>
                  <input id="startDateInput" type="date" style="width:150px; height:36px; background-color:#fff; color:#000; border:1px solid #475569; border-radius:4px; padding:0 8px;" />
                  <span style="color:#e2e8f0;">–</span>
                  <input id="endDateInput" type="date" style="width:150px; height:36px; background-color:#fff; color:#000; border:1px solid #475569; border-radius:4px; padding:0 8px;" />
                  <button type="button" class="btn btn-primary">&nbsp;&nbsp;&nbsp;Apply</button>
                  <button type="button" class="btn btn-clear">&nbsp;&nbsp;&nbsp;Reset</button>
                  <button type="button" class="btn btn-pdf">&nbsp;&nbsp;&nbsp;Export PDF</button>
                  <button type="button" class="btn btn-csv">&nbsp;&nbsp;&nbsp;Export CSV</button>

                  <!-- 自訂 Alert 視窗 -->
                  <div id="customAlert" style="display:none;
                      position:fixed; top:30%; left:50%; transform:translate(-50%, -50%);
                      background:#fff; border:1px solid #475569; border-radius:8px;
                      width:300px; padding:20px; box-shadow:0 4px 8px rgba(0,0,0,0.2); z-index:9999;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                      <span style="font-weight:bold; color:#d97706;">⚠️ ATTENTION</span>
                      <button onclick="closeAlert()" style="background:none; border:none; font-size:18px; cursor:pointer;">✖</button>
                    </div>
                    <hr style="border:0; border-top:1px solid #e5e7eb; margin-bottom:10px;">

                    <p style="margin-top:15px; color:#475569; font-weight:500;">
                      We recommend limiting the search range to 
                      <b style="color:red;">3 MONTHS</b> 
                      for optimal performance.
                    </p>

                    <div style="text-align:right; margin-top:20px;">
                      <button onclick="proceedQuery()" class="btn btn-primary">Confirm</button>
                    </div>
                  </div>


                </div>
              </div>
            </div>

            <div class="query-right">
              <div class="show-entries-box">
                <div class="show-label">Show</div>
                <select name="per_page" class="select">
                  <option value="10" selected>10</option>
                  <option value="20">20</option>
                  <option value="30">30</option>
                  <option value="40">40</option>
                  <option value="50">50</option>
                </select>
                <div class="entries-label">Entries</div>
              </div>
            </div>
          </div>

          <!-- ====== Transaction Table ====== -->
          <table class="device-table">
            <thead>
              <tr>
                <th onclick="sortTable('transaction_id')" data-field="transaction_id">Transaction ID<span class="sort-icon" id="sort-transaction_id"></span></th>
                <th onclick="sortTable('date_time')" data-field="date_time">Date & Time<span class="sort-icon" id="sort-date_time"></span></th>
                <th onclick="sortTable('device_id')" data-field="device_id">Device ID<span class="sort-icon" id="sort-device_id"></span></th>
                <th onclick="sortTable('member')" data-field="member">Member<span class="sort-icon" id="sort-member"></span></th>
                <th onclick="sortTable('location')" data-field="location">Location<span class="sort-icon" id="sort-location"></span></th>
                <th onclick="sortTable('kwh')" data-field="kwh">kWh<span class="sort-icon" id="sort-kwh"></span></th>
                <th onclick="sortTable('amount')" data-field="amount">Amount<span class="sort-icon" id="sort-amount"></span></th>
                <th onclick="sortTable('payment_method')" data-field="payment_method">Payment Method<span class="sort-icon" id="sort-payment_method"></span></th>
                <th onclick="sortTable('payment_status')" data-field="payment_status">Payment Status<span class="sort-icon" id="sort-payment_status"></span></th>
              </tr>
            </thead>
            <tbody id="deviceTableBody"></tbody>
          </table>

          <div id="paginationControls" class="pagination-controls"></div>
          
        </div>

        </section>
      </div>
    </div>
  </div>

</x-layouts.app>

<!-- ====== Transactions Table ====== -->
@php
  $data = $transactions->map(function($row){
      // 安全取值（避免 null 字面量）
      $city     = $row->city     ?? '';
      $district = $row->district ?? '';
      $address  = $row->address  ?? ''; // 用 address，不用不存在的 street

      return [
          'transaction_id' => (string) $row->transaction_id,
          'date_time'      => $row->date_time 
                              ? \Carbon\Carbon::parse($row->date_time)->format('Y/m/d H:i:s') 
                              : '',
          'device_id'      => (string) $row->device_id,
          'member'         => $row->member ?? '',
          'location'       => (string) $row->city,
          'kwh'            => is_null($row->kWh) ? 0 : $row->kWh,
          'amount'         => is_null($row->amount) ? 0 : $row->amount,
          'payment_method' => $row->payment_method ?? '',
          'payment_status' => $row->payment_status ?? '',
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
<!-- ====== Transactions ====== -->
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
    if (s === 'paid')      return 'status-box status-online';
    if (s === 'failed')     return 'status-box status-offline';
    if (s === 'pending') return 'status-box status-maintenance';
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
        <td>${row.transaction_id}</td>
        <td>${row.date_time}</td>
        <td style="text-align:left;">
          <span class="pin-wrapper" data-id="${row.device_id}">
          <svg width="17" height="24" viewBox="0 0 17 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M8.55957 0.75C12.3205 0.75 15.3699 3.79871 15.3701 7.55957C15.3701 8.42173 14.9657 9.65287 14.2646 11.0928C13.5765 12.5061 12.6515 14.0247 11.7158 15.4297C10.7819 16.8321 9.84616 18.1085 9.14355 19.0342C8.9245 19.3228 8.72569 19.575 8.55957 19.7881C8.39353 19.5751 8.19546 19.3226 7.97656 19.0342C7.27396 18.1085 6.33825 16.8321 5.4043 15.4297C4.46863 14.0246 3.54358 12.5061 2.85547 11.0928C2.15444 9.65285 1.75 8.42174 1.75 7.55957C1.75023 3.79885 4.79885 0.750232 8.55957 0.75Z" fill="#FF0000" stroke="#FF0000" stroke-width="1.5"/>
          <path d="M1.5 23H15.5" stroke="#FF0000" stroke-width="2" stroke-linecap="round"/>
          <circle cx="8.5" cy="7.5" r="4.5" fill="white"/>
          </svg>
          </span>
          ${row.device_id}
        </td>
        <td>${row.member}</td>
        <td>${row.location}</td>
        <td>${parseFloat(row.kwh).toFixed(1)}</td>
        <td>${parseFloat(row.amount).toFixed(1)}</td>
        <td>${row.payment_method}</td>
        <td ><span style="width:120px;" class="${statusClass(row.payment_status)}">${row.payment_status}</span></td>
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
      const idVal = document.querySelector('input[name="id"]')?.value.trim() || '';
      const itemType = (document.querySelector('#itemSelect')?.value || '').trim().toLowerCase();
      const locationVal = (document.querySelector('select[name="location"]')?.value || '').trim().toLowerCase();
      const paymentMethodVal = (document.querySelector('select[name="payment_method"]')?.value || '').trim().toLowerCase();
      const paymentStatusVal = (document.querySelector('select[name="payment_status"]')?.value || '').trim().toLowerCase();

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

      // 原本的篩選邏輯
      filteredData = deviceData.filter(row => {
          const matchID = !idVal || (
              itemType === 'transaction_id'
                  ? String(row.transaction_id).includes(idVal)
                  : itemType === 'device_id'
                      ? String(row.device_id).includes(idVal)
                      : true
          );

          const matchLocation = !locationVal || String(row.location || '').toLowerCase().includes(locationVal);
          const matchPaymentMethod = !paymentMethodVal || String(row.payment_method || '').toLowerCase().includes(paymentMethodVal);
          const matchPaymentStatus = !paymentStatusVal || String(row.payment_status || '').toLowerCase().includes(paymentStatusVal);

          let matchDate = true;
          if (startDateVal || endDateVal) {
              const rowDate = new Date(row.date_time);
              if (startDateVal) matchDate = matchDate && rowDate >= new Date(startDateVal);
              if (endDateVal) matchDate = matchDate && rowDate <= new Date(endDateVal);
          }

          return matchID && matchLocation && matchPaymentMethod && matchPaymentStatus && matchDate;
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

    // 設定日期範圍
    const today = new Date();
    const threeMonthsAgo = new Date();
    threeMonthsAgo.setMonth(today.getMonth() - 3);

    // 格式化 yyyy-MM-dd
    function formatDate(date) {
      return date.toISOString().split('T')[0];
    }

    document.getElementById('startDateInput').value = formatDate(threeMonthsAgo);
    document.getElementById('endDateInput').value = formatDate(today);

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
      window.location.href = `/iEMS/public/transactions?startDate=${startDate}&endDate=${endDate}`;
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
          window.location.href = `/iEMS/public/transactions?startDate=${startDateStr}&endDate=${endDateStr}`;
      }
  }

  function closeAlert() {
      document.getElementById('customAlert').style.display = 'none';
  }

  function proceedQuery() {
      const startDateStr = document.getElementById('startDateInput').value;
      const endDateStr   = document.getElementById('endDateInput').value;
      window.location.href = `/iEMS/public/transactions?startDate=${startDateStr}&endDate=${endDateStr}`;
  }


</script>

