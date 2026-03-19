<x-layouts.app :title="__('Account Management')">
  @if(session('success'))
  <script>
      alert("{{ session('success') }}");
  </script>
  @endif
  <link rel="stylesheet" href="{{ asset('css/account.css') }}?v={{ time() }}">
  <div id="report-content" style="padding:24px; box-sizing:border-box; width:100%; margin:0; display:flex; flex-direction:column; gap:24px; background-color:#0f172a; color:#e2e8f0;">
    <div class="top-section">
      <!-- ====== Account Edit ====== -->
      <div  >
        <section class="panel devices member-block">
          <div >
            <section >
           
              <form method="POST" action="{{ route('account.store') }}">
                @csrf
                <table>
                  <tr>
                    <td width="300" align="right"><span style="color:red">*</span><label>Staff ID</label></td>
                    <td align="left">
                      <input id="staffidInput"
                            style="background-color:#d1d5db; color:#2563eb; border:1px solid #475569; border-radius:4px; padding:4px 8px; width:160px;"
                            type="text" name="staffid" value="{{ $members->first()->staff_id ?? '' }}"  readonly>
                    </td>
                    
                    <td width="300" align="right"><span style="color:red">*</span><label>Old Password</label></td>
                    <td align="left">
                      <input id="o_passInput" type="password" name="o_pass" value="" >
                    </td>
                  
                  </tr>

                  <tr>
                    <td width="300" align="right"><span style="color:red">*</span><label>Name</label></td>
                    <td align="left">
                      <input id="nameInput" type="text" name="name" 
                            value="{{ $members->first()->name ?? '' }}">
                    </td>

                    <td align="right"><span style="color:red">*</span><label>New Password</label></td>
                    <td align="left">
                      <input id="n_passInput" type="password" name="n_pass" value="" >
                    </td>
                  </tr>

                  <tr>
                    <td align="right">
                      <label><span style="color:red">*</span>Email</label>
                    </td>
                    <td align="left">
                      <input type="text" name="email" id="emailInput" 
                            value="{{ $members->first()->email ?? '' }}">
                    </td>

                    <td width="300" align="right"><span style="color:red">*</span><label>Repeat New Password</label></td>
                    <td align="left">
                      <input id="rn_passInput" type="password" name="rn_pass" value="" >
                    </td>

                  </tr>

                  <tr>
                    <td align="right">
                      <label><span id="requiredMark2" style="color:red">*</span>Mobile</label>
                    </td>
                    <td align="left">
                      <input type="text" name="mobile" id="mobileInput" value="{{ $members->first()->mobile ?? '' }}">
                    </td>

                    <td align="right"><label>Status</label></td>
                    <td align="left">
                      <input type="radio" name="status" value="active" 
                            {{ (($existing->status ?? ($members->first()->status ?? 'active')) === 'active') ? 'checked' : '' }}> Active
                      <input type="radio" name="status" value="inactive" 
                            {{ (($existing->status ?? ($members->first()->status ?? 'active')) === 'inactive') ? 'checked' : '' }}> Inactive
                    </td>                    
                  </tr>
                  
                  <tr>
                    <td width="300" align="right"><span style="color:red">*</span><label>Assigned Role</label></td>
                    <td align="left">
                      <select id="roleInput" name="role_id"
                              style="width:176px;">
                        @foreach($roles_table as $role)
                          <option value="{{ $role->id }}"
                            {{ (($members->first()->role_id ?? '') == $role->id) ? 'selected' : '' }}>
                            {{ $role->role }}
                          </option>
                        @endforeach
                      </select>
                    </td>

                  </tr>
                  <b>Account Edit</b>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                  
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                  <button type="button" class="btn btn-new" >Edit</button>
                  &nbsp;
                  <button id="btnReload" type="button" class="btn btn-reload">Clear</button>
                  <script>
                    const btnReload = document.getElementById("btnReload");
                    btnReload.addEventListener("click", function() {
                      location.reload(); // 重新載入目前頁面
                    });
                  </script>

                  &nbsp;
                  <button type="submit" class="btn btn-save">Save</button>

                </table>
              </form>

              <style>
              input[type="date"]::-webkit-calendar-picker-indicator {
                filter: invert(32%) sepia(98%) saturate(7483%) hue-rotate(210deg) brightness(95%) contrast(105%);
                cursor: pointer;
              }
              </style>


            </section>
          </div>

        </section>
      </div>

      <!-- Account Table -->
      <div class="grid" >
        <section class="panel devices">
          <div class="monitoring-container">
            <div class="query-section wide">
              <div class="query-main">
                <div class="query-box query-main-box">
                  <div class="member-search">
                    <div class="label">Account Search</div>
                    <div class="field">
                      <label>Status</label>
                      <select name="q_status" class="select">
                        <option value="">All</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                      </select>
                    </div>

                    <div class="field">
                      <label>Last Login</label>
                      <input id="startDateInput" type="date" style="width:150px; height:36px; background-color:#fff; color:#000; border:1px solid #475569; border-radius:4px; padding:0 8px;" />
                      <span style="color:#e2e8f0;">–</span>
                      <input id="endDateInput" type="date" style="width:150px; height:36px; background-color:#fff; color:#000; border:1px solid #475569; border-radius:4px; padding:0 8px;" />
                    </div>

                    <div class="actions">
                      <button id="btnApply" class="btn btn-Apply">Apply</button>
                      <button id="btnReset" class="btn btn-Reset">Reset</button>
                      <button id="btnExportPDF" class="btn btn-PDF">Export PDF</button>
                      <button id="btnExportCSV" class="btn btn-CSV">Export CSV</button>
                    </div>
                  </div>

                  <!-- 第一行 -->
                  <div class="query-row">

                    <div class="field">
                      <label>Staff ID</label>
                      <input type="text" id="staff_idInput" name="q_staff_id" class="input" style="width:200px;" list="staff_idOptions">
                      <datalist id="staff_idOptions">
                        @foreach($staff_id as $s)
                          <option value="{{ $s->staff_id }}"></option>
                        @endforeach
                      </datalist>
                    </div>
                    <div class="field">
                      <label>Name</label>
                      <input type="text" id="nameInput" name="q_name" class="input" style="width:200px;" list="nameOptions">
                      <datalist id="nameOptions">
                        @foreach($names as $n)
                          <option value="{{ $n->name }}"></option>
                        @endforeach
                      </datalist>
                    </div>
                    <div class="field">
                      <label>Email</label>
                      <input type="text" id="emailInput" name="q_email" class="input" style="width:200px;" list="emailOptions">
                      <datalist id="emailOptions">
                        @foreach($emails as $e)
                          <option value="{{ $e->email }}"></option>
                        @endforeach
                      </datalist>
                    </div>
                    <!-- 
                    <div class="field">
                      <label>Mobile</label>
                      <select id="mobileInput" name="q_mobile" class="input" style="width:200px;">
                        <option value="">All</option>
                        @foreach($mobiles as $m)
                          <option value="{{ $m->mobile }}">{{ $m->mobile }}</option>
                        @endforeach
                      </select>
                    </div>
                    -->
                    <div class="field">
                      <label>Assigned Role</label>
                      <select id="roleInput" name="q_role" class="input" style="width:200px;">
                        <option value="">All</option>
                        @foreach($roles as $r)
                          <option value="{{ $r->role }}">{{ $r->role }}</option>
                        @endforeach
                      </select>
                    </div>
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
                <button id="expandBtn" class="expand-btn">
                  <svg width="41" height="34" viewBox="0 0 41 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0H35C38.3137 0 41 2.68629 41 6V34H6C2.68629 34 0 31.3137 0 28V0Z" fill="#0049B7"/>
                    <!-- 箭頭路徑加上 id -->
                    <path id="arrowPath" d="M11 23L21 11L31 23" stroke="#2CCDFE" stroke-width="4" stroke-linecap="round"/>
                  </svg>
                </button>
              </div>

            </div>
            <!-- ====== Member Table ====== -->
            <table class="device-table">
              <thead>
              <tr>
                <th onclick="sortTable('staff_id')" data-field="Staff ID">
                  Staff ID<span class="sort-icon" id="sort-staff_id"></span>
                </th>
                <th onclick="sortTable('name')" data-field="Name">
                  Name<span class="sort-icon" id="sort-name"></span>
                </th>
                <th onclick="sortTable('email')" data-field="Email">
                  Email<span class="sort-icon" id="sort-email"></span>
                </th>
                <th onclick="sortTable('assigned_role')" data-field="Assigned Role">
                  Assigned Role<span class="sort-icon" id="sort-assigned_role"></span>
                </th>
                <th onclick="sortTable('status')" data-field="Status">
                  Status<span class="sort-icon" id="sort-status"></span>
                </th>
                <th onclick="sortTable('active_date')" data-field="Active Date">
                  Active Date<span class="sort-icon" id="sort-active_date"></span>
                </th>
                <th onclick="sortTable('last_login')" data-field="Last Login">
                  Last Login<span class="sort-icon" id="sort-last_login"></span>
                </th>
                <th data-field="Delete">Delete</th>
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

<!-- ====== Member Table ====== -->
@php
  $data = $members_table->map(function($row){
      // 安全取值（避免 null 字面量）
    return [
        'staff_id'     => (string) $row->staff_id,
        'name'         => (string) $row->name,
        'email'        => (string) $row->email,
        'assigned_role'=> (string) $row->assigned_role,
        'status'       => (string) $row->status,
        'active_date'  => $row->active_date 
                          ? \Carbon\Carbon::parse($row->active_date)->format('Y/m/d') 
                          : '',
        'last_login'   => $row->last_login 
                          ? \Carbon\Carbon::parse($row->last_login)->format('Y/m/d H:i:s') 
                          : '',
      ];
  })->values();
@endphp
<!-- ====== Member ====== -->
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
    if (s === 'active') return 'status-active';
    if (s === 'inactive') return 'status-inactive';
    return 'tag tag-default';
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
        <td>${row.staff_id}</td>        <!-- Staff ID -->
        <td>${row.name}</td>            <!-- Name -->
        <td>${row.email}</td>           <!-- Email -->
        <td>${row.assigned_role}</td>   <!-- Assigned Role -->
        <td>
          <span class="${statusClass(row.status)}">
            ${row.status.toLowerCase() === 'active' ? 'Active' :
              (row.status.toLowerCase() === 'inactive' ? 'Inactive' : row.status)}
          </span>
        </td>                          <!-- Status -->
        <td>${row.active_date}</td>     <!-- Active Date -->
        <td>${row.last_login}</td>      <!-- Last Login -->
        <td>
          <button class="stream-btn" data-id="${row.staff_id}" title="Delete">
          <svg width="25" height="29" viewBox="0 0 25 29" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M22.6201 3.12C23.9124 3.12006 24.9599 4.1676 24.96 5.45984C24.96 6.75215 23.9124 7.79962 22.6201 7.79968H21.8398V23.4003C21.8397 25.9847 19.7446 28.0798 17.1602 28.08H7.7998C5.21525 28.0799 3.12028 25.9848 3.12012 23.4003V7.79968H2.33984C1.04757 7.7996 0 6.75213 0 5.45984C8.4519e-05 4.16762 1.04762 3.12008 2.33984 3.12H22.6201Z" fill="#ED0008"/>
          <path d="M12.4801 10.92V22.62M16.3801 10.92V22.62M8.58008 10.92V22.62" stroke="white" stroke-width="1.56" stroke-linecap="round"/>
          <path d="M12.4805 0C14.2035 4.74462e-05 15.6005 1.0476 15.6006 2.33984C15.6006 3.63216 14.2035 4.67964 12.4805 4.67969C10.7573 4.67969 9.36035 3.63219 9.36035 2.33984C9.36046 1.04757 10.7574 0 12.4805 0Z" fill="#ED0008"/>
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

    container.appendChild(makeBtn("◀︎", false, currentPage <= 1, () => {
      currentPage--;
      renderTable(filteredData);
    }, "#3b82f6"));

    for (let i = startPage; i <= endPage; i++) {
      container.appendChild(makeBtn(String(i), i === currentPage, false, () => {
        currentPage = i;
        renderTable(filteredData);
      }));
    }

    if (showEllipsis && endPage < totalPages) {
      container.appendChild(makeEllipsis());
      container.appendChild(makeBtn(String(totalPages), totalPages === currentPage, false, () => {
        currentPage = totalPages;
        renderTable(filteredData);
      }));
    }

    container.appendChild(makeBtn("▶︎", false, currentPage >= totalPages, () => {
      currentPage++;
      renderTable(filteredData);
    }, "#3b82f6"));

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

    input.addEventListener("input", () => {
      goBtn.disabled = !input.value;
    });

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
      if (field === 'last_active') {
        A = new Date(A);
        B = new Date(B);
      } else if (['energy_kwh','amount','sessions'].includes(field)) {
        A = parseFloat(A);
        B = parseFloat(B);
      } else {
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
  const staffIdVal   = document.querySelector('input[name="q_staff_id"]')?.value.trim() || '';
  const nameVal      = document.querySelector('input[name="q_name"]')?.value.trim() || '';
  const emailVal     = document.querySelector('input[name="q_email"]')?.value.trim() || '';
  const mobileVal    = document.querySelector('select[name="q_mobile"]')?.value.trim() || '';
  const roleVal      = document.querySelector('select[name="q_role"]')?.value.trim() || '';
  const statusVal    = (document.querySelector('select[name="q_status"]')?.value || '').trim().toLowerCase();
  const startDateVal = document.getElementById('startDateInput')?.value || '';
  const endDateVal   = document.getElementById('endDateInput')?.value || '';

  // === 新增判斷：startDate 比三個月前還小就彈 ===
  if (startDateVal) {
    const startDate = new Date(startDateVal);
    const threeMonthsAgo = new Date();
    threeMonthsAgo.setMonth(threeMonthsAgo.getMonth() - 3);
    threeMonthsAgo.setDate(threeMonthsAgo.getDate() - 1);

    if (startDate < threeMonthsAgo) {
      document.getElementById('customAlert').style.display = 'block';
      return; // 停止，不繼續篩選
    }
  }

  // === 篩選邏輯 ===
  // === 篩選邏輯 ===
  filteredData = deviceData.filter(row => {
    const matchStaffId = !staffIdVal || String(row.staff_id || '').includes(staffIdVal);
    const matchName    = !nameVal    || String(row.name || '').includes(nameVal);
    const matchEmail   = !emailVal   || String(row.email || '').includes(emailVal);
    const matchMobile  = !mobileVal  || String(row.mobile || '') === mobileVal;
    const matchRole    = !roleVal    || String(row.assigned_role || '') === roleVal;
    const matchStatus  = !statusVal  || String(row.status || '').toLowerCase() === statusVal;

    let matchDate = true;
    if (startDateVal || endDateVal) {
        const rowDate = new Date(row.last_login);
        if (startDateVal) matchDate = matchDate && rowDate >= new Date(startDateVal);
        if (endDateVal) matchDate = matchDate && rowDate <= new Date(endDateVal);
    }

    //return matchStaffId && matchName && matchEmail && matchMobile && matchRole && matchStatus && matchDate;
    return matchStaffId && matchName && matchEmail && matchRole && matchStatus && matchDate;
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

    renderTable(filteredData);
  }

  function changePageSize() {
    const per = document.querySelector('select[name="per_page"]');
    pageSize = parseInt(per ? per.value : 10);
    currentPage = 1;
    renderTable(filteredData);
  }

  // === 綁定事件 ===
  document.getElementById('btnApply')?.addEventListener('click', function(e) {
    e.preventDefault();
    applyFilters();
  });

  document.getElementById('btnReset')?.addEventListener('click', function(e) {
    e.preventDefault();
    resetFilters();
  });

  document.querySelector('select[name="per_page"]')?.addEventListener('change', changePageSize);

  // 初始渲染
  renderTable(filteredData);
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

    // 改成符合Account Table 的欄位
    const headers = [
      "Staff ID",
      "Name",
      "Email",
      "Assigned Role",
      "Status",
      "Active Date",
      "Last Login"
    ];

    const rows = filteredData.map(row => [
      row.staff_id,        // Staff ID
      row.name,            // Name
      row.email,           // Email
      row.assigned_role,   // Assigned Role
      row.status,          // Status
      row.active_date,     // Active Date
      row.last_login       // Last Login
    ]);


    let csvContent = "data:text/csv;charset=utf-8," 
      + headers.join(",") + "\n"
      + rows.map(e => e.join(",")).join("\n");

    const link = document.createElement("a");
    link.setAttribute("href", encodeURI(csvContent));
    link.setAttribute("download", "Account.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  // 匯出 PDF（畫面截圖方式）
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
    doc.save('Account.pdf');
  }

  // 綁定按鈕 (改成對應 id)
  document.getElementById('btnExportCSV')?.addEventListener('click', exportCSV);
  document.getElementById('btnExportPDF')?.addEventListener('click', exportPDF);
</script>


<!-- ====== 超過三個月 ====== -->
<script>
  function doNewQuery() {
      const startDate = document.getElementById('startDateInput').value;
      const endDate   = document.getElementById('endDateInput').value;

      // 改成帶 public 的路徑
      window.location.href = `/iEMS/public/account?startDate=${startDate}&endDate=${endDate}`;
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
          window.location.href = `/iEMS/public/account?startDate=${startDateStr}&endDate=${endDateStr}`;
      }
  }

  function closeAlert() {
      document.getElementById('customAlert').style.display = 'none';
  }

  function proceedQuery() {
      const startDateStr = document.getElementById('startDateInput').value;
      const endDateStr   = document.getElementById('endDateInput').value;
      window.location.href = `/iEMS/public/account?startDate=${startDateStr}&endDate=${endDateStr}`;
  }


</script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const btnNew = document.querySelector(".btn-new");
    const btnSearch = document.querySelector(".btn-search");
    const btnReset = document.querySelector(".btn-reset");
    const btnClear = document.querySelector(".btn-clear");
    const btnSave = document.querySelector(".btn-save");

    btnNew.addEventListener("click", function() {
      btnClear.hidden = false;
      btnSave.hidden = false;
      btnReset.hidden = true;

      // 直接帶出 Controller 傳來的 new_member_id
      document.getElementById("memberid").value = "{{ $new_member_id ?? '' }}";
      document.getElementById("nameInput").value = "{{ $currentUser->name ?? '' }}";
      document.getElementById("emailInput").value = "{{ $currentUser->email ?? '' }}";
      document.getElementById("createdTime").value = new Date().toLocaleString();
    });

    btnSearch.addEventListener("click", function() {
      btnReset.hidden = false;
      btnSave.hidden = false;
      btnClear.hidden = true;
    });

    btnReset.addEventListener("click", function() {
      location.reload();
    });

    btnClear.addEventListener("click", function() {
      location.reload();
    });
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const btnSave = document.querySelector(".btn-save");

    btnSave.addEventListener("click", function(event) {
      const mobileInput = document.getElementById("mobileInput").value.trim();
      const emailInput  = document.getElementById("emailInput").value.trim();

      if (!mobileInput) {
        event.preventDefault(); // 阻止表單送出
        alert("請輸入 Mobile 值再儲存！");
        return false;
      }

      if (!emailInput) {
        event.preventDefault(); // 阻止表單送出
        alert("請輸入 Email 值再儲存！");
        return false;
      }
    });
  });
</script>

<!-- ====== 上方收合功能 ====== -->
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
<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.stream-btn');
    if (!btn) return;

    const staffId = btn.dataset.id;

    if (confirm(`確定要刪除這筆帳號資料嗎？（ID: ${staffId}）`)) {
        window.location.href = `{{ url('account/delete') }}/${staffId}`;
    }
});

</script>
