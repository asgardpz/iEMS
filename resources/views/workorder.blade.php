<x-layouts.app :title="__('Work Order Management')">

<link rel="stylesheet" href="{{ asset('css/workorders.css') }}?v={{ time() }}">

<div class="content">

<!-- ====== Metrics Row ====== -->
<div style="display:flex; gap:24px; margin-bottom:24px;">
  <div class="metric-box">
    <div class="metric-title">Today's Faults</div>
    <div>{{ $faults_today ?? '0' }}</div>
  </div>
  <div class="metric-box">
    <div class="metric-title">Create Work Order</div>
    <div>{{ $created_today ?? '0' }}</div>
  </div>
  <div class="metric-box">
    <div class="metric-title">Mean Time To Repair</div>
    <div>{{ $mttr ?? '0' }} hrs</div>
  </div>
  <div class="metric-box">
    <div class="metric-title">Offline Devices</div>
    <div>{{ $offline_devices ?? '0' }}</div>
  </div>
</div>

<!-- ====== Work Order Form ====== -->
<section class="panel workorder-form">
  <h2 class="section-title">Work Order Management</h2>
  <form method="POST" action="{{ route('workorders.store') }}">
    @csrf
    <div class="form-row">
      <label>Work Order ID</label>
      <input type="text" name="work_order_code" value="{{ old('work_order_code') }}" required>
    </div>
    <div class="form-row">
      <label>Device ID</label>
      <select name="device_id" required>
        @foreach($devices as $d)
          <option value="{{ $d->id }}">{{ $d->id }} - {{ $d->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-row">
      <label>Segment</label>
      <select name="segment">
        <option value="Left">Left</option>
        <option value="Right" selected>Right</option>
        <option value="Both">Both</option>
      </select>
    </div>
    <div class="form-row">
      <label>Status</label>
      <select name="current_status">
        <option value="Online">Online</option>
        <option value="Offline" selected>Offline</option>
      </select>
    </div>
    <div class="form-row">
      <label>Source</label>
      <input type="text" name="source" value="Customer Support">
    </div>
    <div class="form-row">
      <label>Description</label>
      <textarea name="description"></textarea>
    </div>
    <div class="form-row">
      <label>Repair Status</label>
      <select name="repair_status">
        <option value="Pending" selected>Pending</option>
        <option value="In Progress">In Progress</option>
        <option value="Completed">Completed</option>
        <option value="Cancel">Cancel</option>
      </select>
    </div>
    <div class="form-row">
      <label>Maintenance Items</label>
      <input type="text" name="maintenance_items" value="Fan & Filter Cleaning">
    </div>
    <div class="form-row">
      <label>Assigned</label>
      <select name="assigned_user">
        @foreach($users as $u)
          <option value="{{ $u->id }}">{{ $u->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-row">
      <label>Priority</label>
      <select name="priority" class="priority-high">
        <option value="High" selected>High</option>
        <option value="Medium">Medium</option>
        <option value="Low">Low</option>
      </select>
    </div>
    <div class="form-row">
      <label>Inspection Date</label>
      <input type="date" name="inspection_date" value="{{ old('inspection_date') }}">
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="reset" class="btn btn-clear">Clear</button>
    </div>
  </form>
</section>

<!-- ====== Work Order Table ====== -->
<section class="panel">
  <table class="device-table">
    <thead>
      <tr>
        <th>Work Order Code</th>
        <th>Device ID</th>
        <th>Status</th>
        <th>Repair Status</th>
        <th>Assigned</th>
        <th>Priority</th>
        <th>Inspection Date</th>
      </tr>
    </thead>
    <tbody>
      @foreach($workorders as $w)
      <tr>
        <td>{{ $w->work_order_code }}</td>
        <td>{{ $w->device_id }}</td>
        <td><span class="status-box status-{{ strtolower($w->current_status) }}">{{ $w->current_status }}</span></td>
        <td>{{ $w->repair_status }}</td>
        <td>{{ $w->assigned->name }}</td>
        <td class="priority-{{ strtolower($w->priority) }}">{{ $w->priority }}</td>
        <td>{{ $w->inspection_date->format('Y/m/d') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</section>

</div>
</x-layouts.app>
