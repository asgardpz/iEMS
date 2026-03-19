<x-layouts.app :title="__('Permission Management')">
  @if(session('success'))
  <script>
      alert("{{ session('success') }}");
  </script>
  @endif
  <link rel="stylesheet" href="{{ asset('css/permission.css') }}?v={{ time() }}">
  <div id="report-content" style="padding:24px; box-sizing:border-box; width:100%; margin:0; display:flex; flex-direction:column; gap:24px; background-color:#0f172a; color:#e2e8f0;">
      <!-- ====== Permission Edit ====== -->
      <div  >
        <section >
          <div >
            <section class="roles-layout">
              <form method="POST" action="{{ route('permission.store') }}">
                @csrf
                <table>
                  <tr>
                    <td ><input type="text" name="role" placeholder="Please write a role name" ></td>
                    <tr class="permissions-row">
                      <td>
                        <label><input type="checkbox" name="realtime" {{ old('realtime') ? 'checked' : '' }}> Real-Time Monitoring</label>
                      </td>
                      <td>
                        <label><input type="checkbox" name="dashboard" {{ old('dashboard') ? 'checked' : '' }}> Dashboard</label>
                      </td>
                      
                      <td>
                        <label><input type="checkbox" name="transactions" {{ old('transactions') ? 'checked' : '' }}> Transactions & Billing</label>
                      </td>
                      <td>
                        <label><input type="checkbox" name="maintenance" {{ old('maintenance') ? 'checked' : '' }}> Maintenance Mgmt.</label>
                      </td>
                      <td>
                        <label><input type="checkbox" name="reports" {{ old('reports') ? 'checked' : '' }}> Reports & Analytics</label>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <label><input type="checkbox" name="device" {{ old('device') ? 'checked' : '' }}> Device Mgmt.</label>
                      </td>
                      <td>
                        <label><input type="checkbox" name="workorder" {{ old('workorder') ? 'checked' : '' }}> Work Order Mgmt.</label>
                      </td>
                      <td>
                        <label><input type="checkbox" name="member" {{ old('member') ? 'checked' : '' }}> Member Mgmt.</label>
                      </td>
                      <td>
                        <label><input type="checkbox" name="permission" {{ old('permission') ? 'checked' : '' }}> Permission Settings</label>
                      </td>
                      <td>
                        <label><input type="checkbox" name="account" {{ old('account') ? 'checked' : '' }}> Account Mgmt.</label>
                      </td>
                    </tr>

                  <b>Role & Permissions Settings</b>
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
                                                                      
                  <button type="button" class="btn btn-new" >New</button>
                  &nbsp;
                  <button type="button" class="btn btn-clear">Clear</button>
                  &nbsp;
                  <button type="submit" class="btn btn-save">Save</button>

                </table>
              </form>
            </section>
          </div>

        </section>
      </div>

      <!-- Permission Table -->
      <div class="grid" >
        <section class="panel devices">
          <div >
            <!-- ====== Permission Table ====== -->
            <table class="device-table">
                <thead>
                  <tr>
                  <th onclick="sortTable('role')" data-field="Role">
                    Assigned Role<span class="sort-icon" id="sort-role"></span>
                  </th>
                  <th onclick="sortTable('realtime')" data-field="Real-Time Monitoring">
                    Real-Time Monitoring<span class="sort-icon" id="sort-realtime"></span>
                  </th>
                  <th onclick="sortTable('dashboard')" data-field="Dashboard">
                    Dashboard<span class="sort-icon" id="sort-dashboard"></span>
                  </th>
                  <th onclick="sortTable('transactions')" data-field="Transactions">
                    Transactions<br>& Billing<span class="sort-icon" id="sort-transactions"></span>
                  </th>
                  <th onclick="sortTable('maintenance')" data-field="Maintenance">
                    Maintenance Mgmt.<span class="sort-icon" id="sort-maintenance"></span>
                  </th>
                  <th onclick="sortTable('reports')" data-field="Reports">
                    Reports<br>& Analytics<span class="sort-icon" id="sort-reports"></span>
                  </th>
                  <th onclick="sortTable('device')" data-field="Device">
                    Device Mgmt.<span class="sort-icon" id="sort-device"></span>
                  </th>
                  <th onclick="sortTable('workorder')" data-field="Work Order">
                    Work Order Mgmt.<span class="sort-icon" id="sort-workorder"></span>
                  </th>
                  <th onclick="sortTable('member')" data-field="Member">
                    Member Mgmt.<span class="sort-icon" id="sort-member"></span>
                  </th>
                  <th onclick="sortTable('permission')" data-field="Permission">
                    Permission<br>Settings<span class="sort-icon" id="sort-permission"></span>
                  </th>
                  <th onclick="sortTable('account')" data-field="Account">
                    Account<br>Mgmt.<span class="sort-icon" id="sort-account"></span>
                  </th>

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

<!-- ====== Permission Table ====== -->
@php
  $data = $roles_table->map(function($row){
      // 安全取值（避免 null 字面量）
      return [
          'id'          => (string) $row->id,
          'role'        => (string) $row->role,
          'realtime'    => (bool) $row->realtime,
          'dashboard'   => (bool) $row->dashboard,
          'transactions'=> (bool) $row->transactions,
          'maintenance' => (bool) $row->maintenance,
          'reports'     => (bool) $row->reports,
          'device'      => (bool) $row->device,
          'workorder'   => (bool) $row->workorder,
          'member'      => (bool) $row->member,
          'permission'  => (bool) $row->permission,
          'account'     => (bool) $row->account,
      ];
  })->values();
@endphp


<!-- ====== Permission ====== -->
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
      <td><a href="#" class="role-link" data-role='${row.role}'
            data-realtime='${row.realtime}'
            data-dashboard='${row.dashboard}'
            data-transactions='${row.transactions}'
            data-maintenance='${row.maintenance}'
            data-reports='${row.reports}'
            data-device='${row.device}'
            data-workorder='${row.workorder}'
            data-member='${row.member}'
            data-permission='${row.permission}'
            data-account='${row.account}'>${row.role}</a></td>
      <td>${
        row.realtime 
          ? `<svg width="29" height="28" viewBox="0 0 29 28" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 11.5002L11.3137 24.0002L27 2.00015" stroke="#A2F04C" stroke-width="4" stroke-linecap="round"/>
            </svg>` 
          : `<svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20.0852 2.00001L1.99963 20.1359" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
              <path d="M20.4343 20.1596L2.30015 2.07227" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
            </svg>`
      }</td>
      <td>${
        row.dashboard 
          ? `<svg width="29" height="28" viewBox="0 0 29 28" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 11.5002L11.3137 24.0002L27 2.00015" stroke="#A2F04C" stroke-width="4" stroke-linecap="round"/>
            </svg>` 
          : `<svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20.0852 2.00001L1.99963 20.1359" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
              <path d="M20.4343 20.1596L2.30015 2.07227" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
            </svg>`
      }</td>
      <td>${
        row.transactions 
          ? `<svg width="29" height="28" viewBox="0 0 29 28" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 11.5002L11.3137 24.0002L27 2.00015" stroke="#A2F04C" stroke-width="4" stroke-linecap="round"/>
            </svg>` 
          : `<svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20.0852 2.00001L1.99963 20.1359" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
              <path d="M20.4343 20.1596L2.30015 2.07227" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
            </svg>`
      }</td>
      <td>${
        row.maintenance 
          ? `<svg width="29" height="28" viewBox="0 0 29 28" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 11.5002L11.3137 24.0002L27 2.00015" stroke="#A2F04C" stroke-width="4" stroke-linecap="round"/>
            </svg>` 
          : `<svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20.0852 2.00001L1.99963 20.1359" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
              <path d="M20.4343 20.1596L2.30015 2.07227" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
            </svg>`
      }</td>
      <td>${
        row.reports 
          ? `<svg width="29" height="28" viewBox="0 0 29 28" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 11.5002L11.3137 24.0002L27 2.00015" stroke="#A2F04C" stroke-width="4" stroke-linecap="round"/>
            </svg>` 
          : `<svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20.0852 2.00001L1.99963 20.1359" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
              <path d="M20.4343 20.1596L2.30015 2.07227" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
            </svg>`
      }</td>
      <td>${
        row.device 
          ? `<svg width="29" height="28" viewBox="0 0 29 28" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 11.5002L11.3137 24.0002L27 2.00015" stroke="#A2F04C" stroke-width="4" stroke-linecap="round"/>
            </svg>` 
          : `<svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20.0852 2.00001L1.99963 20.1359" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
              <path d="M20.4343 20.1596L2.30015 2.07227" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
            </svg>`
      }</td>
      <td>${
        row.workorder 
          ? `<svg width="29" height="28" viewBox="0 0 29 28" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 11.5002L11.3137 24.0002L27 2.00015" stroke="#A2F04C" stroke-width="4" stroke-linecap="round"/>
            </svg>` 
          : `<svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20.0852 2.00001L1.99963 20.1359" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
              <path d="M20.4343 20.1596L2.30015 2.07227" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
            </svg>`
      }</td>
      <td>${
        row.member 
          ? `<svg width="29" height="28" viewBox="0 0 29 28" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 11.5002L11.3137 24.0002L27 2.00015" stroke="#A2F04C" stroke-width="4" stroke-linecap="round"/>
            </svg>` 
          : `<svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20.0852 2.00001L1.99963 20.1359" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
              <path d="M20.4343 20.1596L2.30015 2.07227" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
            </svg>`
      }</td>
      <td>${
        row.permission 
          ? `<svg width="29" height="28" viewBox="0 0 29 28" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 11.5002L11.3137 24.0002L27 2.00015" stroke="#A2F04C" stroke-width="4" stroke-linecap="round"/>
            </svg>` 
          : `<svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20.0852 2.00001L1.99963 20.1359" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
              <path d="M20.4343 20.1596L2.30015 2.07227" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
            </svg>`
      }</td>
      <td>${
        row.account 
          ? `<svg width="29" height="28" viewBox="0 0 29 28" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 11.5002L11.3137 24.0002L27 2.00015" stroke="#A2F04C" stroke-width="4" stroke-linecap="round"/>
            </svg>` 
          : `<svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20.0852 2.00001L1.99963 20.1359" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
              <path d="M20.4343 20.1596L2.30015 2.07227" stroke="#ED0008" stroke-width="4" stroke-linecap="round"/>
            </svg>`
      }</td>
    `;
    tbody.appendChild(tr);
  });

  // 綁定事件：點擊角色名稱時，把值帶到上面欄位
  document.addEventListener("click", function(e) {
    if (e.target.classList.contains("role-link")) {
      e.preventDefault();
      const role = e.target.dataset.role;
      document.querySelector("input[name='role']").value = role;

      // 更新 checkbox 狀態
      const permissions = ["realtime","dashboard","transactions","maintenance","reports","device","workorder","member","permission","account"];
      permissions.forEach(p => {
        const checkbox = document.querySelector(`input[name='${p}']`);
        if (checkbox) {
          checkbox.checked = (e.target.dataset[p] === "true");
        }
      });
    }
  });

  // 點擊角色連結時帶出值
  document.addEventListener("click", function(e) {
    if (e.target.classList.contains("role-link")) {
      e.preventDefault();
      const role = e.target.dataset.role;
      document.querySelector("input[name='role']").value = role;

      // 更新 checkbox 狀態
      const permissions = ["realtime","dashboard","transactions","maintenance","reports","device","workorder","member","permission","account"];
      permissions.forEach(p => {
        const checkbox = document.querySelector(`input[name='${p}']`);
        if (checkbox) {
          checkbox.checked = (e.target.dataset[p] === "true");
        }
      });

      // 把 New 按鈕文字改成 Edit
      const newBtn = document.querySelector(".btn-new");
      if (newBtn) {
        newBtn.textContent = "Edit";
      }
    }
  });

  // Clear 按鈕：刷新頁面
  document.querySelector(".btn-clear").addEventListener("click", function() {
    location.reload();
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
    const memberIdVal = document.querySelector('input[name="member_id"]')?.value.trim() || '';
    const plateNoVal = document.querySelector('input[name="plate_no"]')?.value.trim() || '';
    const statusVal = (document.querySelector('select[name="status"]')?.value || '').trim().toLowerCase();
    const startDateVal = document.getElementById('startDateInput')?.value || '';
    const endDateVal = document.getElementById('endDateInput')?.value || '';

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
    filteredData = deviceData.filter(row => {
      const matchMemberId = !memberIdVal || String(row.member_id || '').includes(memberIdVal);
      const matchPlateNo  = !plateNoVal  || String(row.plate_no || '').includes(plateNoVal);
      const matchStatus   = !statusVal   || String(row.status || '').toLowerCase() === statusVal;

      let matchDate = true;
      if (startDateVal || endDateVal) {
        const rowDate = new Date(row.last_active);
        if (startDateVal) matchDate = matchDate && rowDate >= new Date(startDateVal);
        if (endDateVal)   matchDate = matchDate && rowDate <= new Date(endDateVal);
      }

      return matchMemberId && matchPlateNo && matchStatus && matchDate;
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

