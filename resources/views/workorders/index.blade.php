<x-layouts.app :title="__('Work Order Management')">
  <link rel="stylesheet" href="{{ asset('css/workorder.css') }}?v={{ time() }}">
  <div id="report-content" style="padding:24px; box-sizing:border-box; width:100%; margin:0; display:flex; flex-direction:column; gap:24px; background-color:#0f172a; color:#e2e8f0;">
    <div class="content">
      <!-- ====== Metrics Row ====== -->
      <div style="display: flex; gap: 12px; margin-bottom:12px;">
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Today's Faults</div>
          <div>{{ $todays_faults }}</div>
        </div>
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Create Work Order</div>
          <div>{{ $created_work_orders }}</div>
        </div>
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Mean Time To Repair (MTTR) /hrs</div>
          <div>{{ number_format($mttr, 1) }}</div>
        </div>
        <div style="flex: 1; background-color: #1e293b; border: 1px solid #3b82f6; border-radius: 8px; padding: 16px;">
          <div style="font-weight: bold;">Offline Devices</div>
          <div>{{ $offline_devices }}</div>
        </div>
      </div>


      <!-- ====== Panels Grid ====== -->
      <div class="grid" >
        <!-- Work Order Form -->
        <section class="panel devices">
          <div >
            <!-- ====== Work Order Form ====== -->
            <section class="panel workorder-form">
              <h2 class="section-title">
                <tr>
                  <td  width="300" align="right">
                    <label>Work Order Management </label>
                  </td>

                </tr>
  
                          
              </h2>
            
              <form method="POST" action="{{ route('workorders.store') }}">
                @csrf
                <table>
                  <tr><td colspan="1">&nbsp;</td></tr> <!-- 空一行 -->

                  <tr>
                    <td width="300" align="right"><label>Work Order ID</label></td>
                    <td align="left">
                      <input id="workOrderId"
                            style="background-color:#d1d5db; color:#2563eb; border:1px solid #475569; border-radius:4px; padding:4px 8px; width:160px;"
                            type="text" name="work_order_code" value="" readonly>
                    </td>
                  </tr>

                  <tr><td colspan="1">&nbsp;</td></tr> <!-- 空一行 -->

                  <tr>
                    <td align="right"><label>Created Time</label></td>
                    <td align="left">
                      <input id="createdTime"
                            style="background-color:#d1d5db; color:#2563eb; border:1px solid #475569; border-radius:4px; padding:4px 8px; width:160px;"
                            type="text" name="created_time" value="" readonly>
                    </td>
                  </tr>
                                    
                  <tr>
                    <td align="right">
                      <label><span id="requiredMark1" style="color:red; display:none">*</span>Device ID</label>
                    </td>
                    <td align="left">
                      <input list="deviceList" name="device_id" id="deviceInput" required>
                      <datalist id="deviceList">
                        @foreach($devices as $d)
                          <option value="{{ $d->device_id }}" 
                                  data-id="{{ $d->device_pk }}" 
                                  data-address="{{ $d->address }}" 
                                  data-lat="{{ $d->latitude }}" 
                                  data-lng="{{ $d->longitude }}"
                                  data-status="{{ $d->status }}"
                                  >
                        @endforeach
                      </datalist>
                      <input type="hidden" name="device_pk" id="devicePk">
                    </td>
                  </tr>
                  
                  <tr>
                    <td align="right">
                      <label><span id="requiredMark2" style="color:red; display:none">*</span>Location</label>
                    </td>
                    <td align="left">
                      <input type="text" name="location" id="locationInput" readonly>
                      
                      <span id="mapBtn" class="pin-wrapper" data-id="${row.device_id}" style="cursor:pointer;margin-top:50px;">
                        <svg style="position:relative; top:7px;" width="17" height="24" viewBox="0 0 17 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M8.55957 0.75C12.3205 0.75 15.3699 3.79871 15.3701 7.55957C15.3701 8.42173 14.9657 9.65287 14.2646 11.0928C13.5765 12.5061 12.6515 14.0247 11.7158 15.4297C10.7819 16.8321 9.84616 18.1085 9.14355 19.0342C8.9245 19.3228 8.72569 19.575 8.55957 19.7881C8.39353 19.5751 8.19546 19.3226 7.97656 19.0342C7.27396 18.1085 6.33825 16.8321 5.4043 15.4297C4.46863 14.0246 3.54358 12.5061 2.85547 11.0928C2.15444 9.65285 1.75 8.42174 1.75 7.55957C1.75023 3.79885 4.79885 0.750232 8.55957 0.75Z" fill="#FF0000" stroke="#FF0000" stroke-width="1.5"/>
                          <path d="M1.5 23H15.5" stroke="#FF0000" stroke-width="2" stroke-linecap="round"/>
                          <circle cx="8.5" cy="7.5" r="4.5" fill="white"/>
                        </svg>
                      </span>
                    </td>
                  </tr>

                  <!-- Modal 容器 -->
                  <div id="mapModal" style="display:none; position:fixed; top:220px; right:200px;
                      width:500px; height:300px; background:#fff; border:2px solid #475569; border-radius:6px; z-index:9999; padding:10px;">
                    <div id="map" style="width:100%; height:100%;"></div>
                  </div>

                  <tr><td colspan="1">&nbsp;</td></tr> <!-- 空一行 -->
                  
                  <tr>
                    <td align="right"><label>Segment</label></td>
                    <td align="left">
                      <input type="radio" name="segment" value="Right" checked> Right
                      <input type="radio" name="segment" value="Left"> Left
                      <input type="radio" name="segment" value="Both"> Both
                    </td>
                  </tr>

                  <tr><td colspan="1">&nbsp;</td></tr> <!-- 空一行 -->
                  
                  <tr>
                    <td align="right"><label>Current Status</label></td>
                    <td align="left">
                      <input id="currentStatus"
                            style="background-color:#d1d5db; color:#2563eb; border:1px solid #475569; border-radius:4px; padding:4px 8px; width:160px;"
                            type="text" name="current_status" value="" readonly>
                    </td>
                  </tr>

                  <tr><td colspan="1">&nbsp;</td></tr> <!-- 空一行 -->
                  
                  <tr>
                    <td align="right"><label><span id="requiredMark3" style="color:red; display:none">*</span>Work Order Source</label></td>
                    <td align="left">
                      <select name="source" style="width:180px;">
                        <option value="Other">Other</option>
                        <option value="Customer Support" selected>Customer Support</option>
                      </select>

                    </td>
                    <td ><label>Description</label></td>
                    <td ><input type="text" name="source_description" placeholder="Select 'Other,' please describe the issue." size="50"></td>
                  </tr>

                  <tr><td colspan="1">&nbsp;</td></tr> <!-- 空一行 -->
                  
                  <tr>
                    <td align="right"><label>Repair Status</label></td>
                    <td align="left">
                      <input type="radio" name="repair_status" value="Pending" checked> Pending
                      <input type="radio" name="repair_status" value="In Progress"> In Progress
                      <input type="radio" name="repair_status" value="Completed"> Completed
                      <input type="radio" name="repair_status" value="Cancel"> Cancel
                    </td>
                  </tr>

                  <tr><td colspan="1">&nbsp;</td></tr> <!-- 空一行 -->
                  
                  <tr>
                    <td align="right"><label><span id="requiredMark4" style="color:red; display:none">*</span>Maintenance Items</label></td>
                    <td align="left">
                      <select name="Maintenance" style="width:180px;">
                        <option value="Other">Other</option>
                        <option value="Fan & Filter Cleaning" selected>Fan & Filter Cleaning</option>
                      </select>
                    </td>
                    <td align="right"><label>Description</label></td>
                    <td align="left"><input type="text" name="maintenance_description" placeholder="Select 'Other,' please describe the issue." size="50"></td>
                  </tr>

                  <tr><td colspan="1">&nbsp;</td></tr> <!-- 空一行 -->
                  
                  <tr>
                    <td align="right"><label><span id="requiredMark5" style="color:red; display:none">*</span>Assigned</label></td>
                    <td align="left">
                      <select name="assigned_user" 
                              style="width:180px; border:1px solid #475569; border-radius:4px; padding:0 8px;">
                        @foreach($users as $user)
                          <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                      </select>
                    </td>

                  </tr>

                  <tr><td colspan="1">&nbsp;</td></tr> <!-- 空一行 -->
                  
                  <tr>
                    <td align="right"><label>Priority</label></td>
                    <td align="left">
                      <input type="radio" name="priority" value="High" checked> High
                      <input type="radio" name="priority" value="Medium"> Medium
                      <input type="radio" name="priority" value="Low"> Low
                    </td>
                  </tr>

                  <tr><td colspan="1">&nbsp;</td></tr> <!-- 空一行 -->
                  
                  <tr>
                    <td align="right"><label><span id="requiredMark6" style="color:red; display:none">*</span>Inspection Date</label></td>
                    <td align="left">
                      <input type="date" name="inspection_date"
                            value="{{ date('Y-m-d') }}"
                            style="width:160px;  background-color:#fff; color:#000; border:1px solid #475569; border-radius:4px; padding:0 8px;">
                    </td>
                  </tr>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;               
                    <button type="button" class="btn btn-new">New</button>
                    &nbsp;
                    <button type="button" class="btn btn-search">Search</button>
                    &nbsp;
                    <button type="button" class="btn btn-reset" hidden="true">Reset</button>
                    &nbsp;
                    <button type="button" class="btn btn-clear" hidden="true">Clear</button>
                    &nbsp; 
                    <button type="submit" class="btn btn-save" hidden="true">Save</button>
                    @if(session('success'))
                    <script>
                        alert("{{ session('success') }}");
                    </script>
                    @endif

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
    </div>
  </div>

</x-layouts.app>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const btnNew = document.querySelector(".btn-new");
    const btnSearch = document.querySelector(".btn-search");
    const btnReset = document.querySelector(".btn-reset");
    const btnClear = document.querySelector(".btn-clear");
    const btnSave = document.querySelector(".btn-save");

    // 按下 New
    btnNew.addEventListener("click", function() {
      btnClear.hidden = false;
      btnSave.hidden = false;
      btnReset.hidden = true;
      requiredMark1.style.display = "inline";
      requiredMark2.style.display = "inline";
      requiredMark3.style.display = "inline";
      requiredMark4.style.display = "inline";
      requiredMark5.style.display = "inline";
      requiredMark6.style.display = "inline";

      // 按下 New 時才帶出 Work Order ID 和 Created Time
      document.getElementById("workOrderId").value = "{{ $new_order }}";
      document.getElementById("createdTime").value = new Date().toLocaleString();
    });

    // 按下 Search
    btnSearch.addEventListener("click", function() {
      btnReset.hidden = false;
      btnSave.hidden = false;
      btnClear.hidden = true;
      requiredMark1.style.display = "none";
      requiredMark2.style.display = "none";
      requiredMark3.style.display = "none";
      requiredMark4.style.display = "none";
      requiredMark5.style.display = "none";
      requiredMark6.style.display = "none";
    });

    // 按下 Reset → 刷新頁面
    btnReset.addEventListener("click", function() {
      location.reload();
    });

    // 按下 Clear → 刷新頁面
    btnClear.addEventListener("click", function() {
      location.reload();
    });
  });
</script>


<script>
  document.addEventListener("DOMContentLoaded", function() {
    const deviceInput = document.getElementById("deviceInput");
    const devicePk = document.getElementById("devicePk");
    const locationInput = document.getElementById("locationInput");
    const mapBtn = document.getElementById("mapBtn");
    const mapModal = document.getElementById("mapModal");
    let map; 
    let marker;

    deviceInput.addEventListener("input", function() {
      const val = deviceInput.value;
      const option = [...document.querySelectorAll("#deviceList option")]
        .find(o => o.value === val);

      if (option) {
        devicePk.value = option.dataset.id;
        locationInput.value = option.dataset.address;
        mapBtn.dataset.lat = option.dataset.lat;
        mapBtn.dataset.lng = option.dataset.lng;
        document.getElementById("currentStatus").value = option.dataset.status; // 新增這行
      }
    });

    mapBtn.addEventListener("click", function() {
      if (mapModal.style.display === "none") {
        // 顯示 modal
        mapModal.style.display = "block";
        const lat = parseFloat(mapBtn.dataset.lat);
        const lng = parseFloat(mapBtn.dataset.lng);

        if (!map) {
          map = L.map('map').setView([lat, lng], 15);
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
          }).addTo(map);
        } else {
          map.setView([lat, lng], 15);
          if (marker) marker.remove();
        }
        marker = L.marker([lat, lng]).addTo(map);
      } else {
        // 隱藏 modal
        mapModal.style.display = "none";
      }
    });
  });
</script>



