<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class dashboardController extends Controller
{
    public function show()
    {
        // KPI

        // 修正 in_use 判斷（去掉空格）
        $in_use = DB::table('devices')->whereRaw('LOWER(status) = ?', ['inuse'])->count();

        // 修正 disconnected 判斷（offline 且超過 10 分鐘沒連線）
        $disconnected_last_10m = DB::table('devices')
            ->whereRaw('LOWER(status) = ?', ['offline'])
            ->where('last_online_at', '<', Carbon::now()->subMinutes(10))
            ->count();

        $latest = DB::table('device_status_history as dsh')
            ->select('dsh.device_id', DB::raw('MAX(dsh.timestamp) as ts'))
            ->groupBy('dsh.device_id');

        $total_power_kw = DB::table('device_status_history as a')
            ->joinSub($latest, 'b', function ($join) {
                $join->on('a.device_id', '=', 'b.device_id')->on('a.timestamp', '=', 'b.ts');
            })
            ->sum('a.power_kw');

        $total_devices = DB::table('devices')->count();
        $utilization_pct = $total_devices > 0 ? round(($in_use / $total_devices) * 100, 1) : 0.0;

        // 地圖資料
        $stations = DB::table('stations')->get();
        // 即時裝置狀態：取每個 device_id 最新一筆紀錄
        $devices = DB::table('device_status_history as dsh')
            ->select('dsh.device_id', 'dsh.status', 'dsh.timestamp', 'dev.station_id')
            ->join(DB::raw('(SELECT device_id, MAX(timestamp) as latest_time 
                            FROM device_status_history 
                            GROUP BY device_id) latest'), function($join) {
                $join->on('dsh.device_id', '=', 'latest.device_id')
                    ->on('dsh.timestamp', '=', 'latest.latest_time');
            })
            ->join('devices as dev', 'dsh.device_id', '=', 'dev.id') // 這裡補 join devices
            ->get()
            ->map(function($row){
                return [
                    'device_id'  => $row->device_id,
                    'station_id' => $row->station_id,
                    'status'     => strtolower($row->status),
                    'timestamp'  => $row->timestamp ? \Carbon\Carbon::parse($row->timestamp)->format('Y-m-d H:i:s') : '',
                ];
            });

        $alerts = DB::table('alerts')->orderByDesc('occurred_at')->limit(100)->get();
        // 折線圖資料
        $power_history = DB::table('device_status_history')
            ->orderByDesc('timestamp')
            ->limit(60)
            ->get(['timestamp', 'power_kw']);

        // 利用率歷史
        $utilization_history = DB::table('device_status_history')
            ->select('timestamp',
                DB::raw("
                    100.0 * (
                        SELECT COUNT(*) FROM devices WHERE LOWER(status) = 'in use'
                    ) / NULLIF((SELECT COUNT(*) FROM devices),0)
                AS utilization_pct")
            )
            ->orderByDesc('timestamp')
            ->limit(60)
            ->get();

        // 最新狀態
        $latest_status = DB::table('device_status_history as dsh')
            ->select('dsh.device_id', 'dsh.current_a', 'dsh.voltage_v', 'dsh.temperature_c', 'dsh.timestamp')
            ->whereIn(DB::raw('(device_id, timestamp)'), function ($query) {
                $query->selectRaw('device_id, MAX(timestamp)')
                    ->from('device_status_history')
                    ->groupBy('device_id');
            });

        // 加入 stations 取得 address/district/city
        $devices_with_status = DB::table('devices as d')
            ->leftJoinSub($latest_status, 's', function ($join) {
                $join->on('d.device_id', '=', 's.device_id');
            })
            ->leftJoin('stations as st', 'd.station_id', '=', 'st.id')
            ->select(
                'd.device_id',
                'd.status',
                's.current_a',
                's.voltage_v',
                's.temperature_c',
                's.timestamp',
                'st.address',
                'st.city',
                'st.district'
            )
            ->get();

        // 每個 device 的事件紀錄（timestamp + status），取最近 6 筆
        $device_logs_raw = DB::table('device_status_history')
            ->select('device_id', 'timestamp', 'status')
            ->orderByDesc('timestamp')
            ->get();

        $device_logs = [];
        foreach ($device_logs_raw as $log) {
            $id = $log->device_id;
            if (!isset($device_logs[$id])) {
                $device_logs[$id] = [];
            }
            if (count($device_logs[$id]) < 6) {
                $device_logs[$id][] = [
                    'timestamp' => $log->timestamp,
                    'status' => $log->status,
                ];
            }
        }

        $avg_current_a = DB::table('device_status_history')->avg('current_a');
        $avg_voltage_v = DB::table('device_status_history')->avg('voltage_v');
        $avg_temperature_c = DB::table('device_status_history')->avg('temperature_c');

        // Charging Trend Chart
        $all_status_history = DB::table('device_status_history')
            ->select('timestamp', 'power_kw', 'current_a', 'voltage_v', 'temperature_c')
            ->orderBy('timestamp')
            ->get()
            ->map(function($row){
                return [
                    'timestamp' => $row->timestamp ? \Carbon\Carbon::parse($row->timestamp)->format('m-d') : '',
                    'power_kw'  => is_null($row->power_kw) ? 0 : (float)$row->power_kw,
                    'current_a' => is_null($row->current_a) ? 0 : (float)$row->current_a,
                    'voltage_v' => is_null($row->voltage_v) ? 0 : (float)$row->voltage_v,
                    'temperature_c' => is_null($row->temperature_c) ? 0 : (float)$row->temperature_c,
                ];
            })
            ->values();

        // Devices Online (今日在線裝置) - 依 device_status_history 最新狀態
        $devices_online = DB::table('device_status_history as dsh')
            ->select('dsh.device_id')
            ->join(DB::raw('(SELECT device_id, MAX(timestamp) as latest_time 
                            FROM device_status_history 
                            GROUP BY device_id) latest'), function($join) {
                $join->on('dsh.device_id', '=', 'latest.device_id')
                    ->on('dsh.timestamp', '=', 'latest.latest_time');
            })
            ->whereRaw('LOWER(dsh.status) = ?', ['online'])
            ->count();


        // Charges Today (今日充電次數)
        $charges_today = DB::table('charging_sessions')
            ->whereDate('start_at', Carbon::today())
            ->count();

        // Real-Time Utilization (%) - 最新狀態
        $total_devices = DB::table('device_status_history as dsh')
            ->join(DB::raw('(SELECT device_id, MAX(timestamp) as latest_time 
                            FROM device_status_history 
                            GROUP BY device_id) latest'), function($join) {
                $join->on('dsh.device_id', '=', 'latest.device_id')
                    ->on('dsh.timestamp', '=', 'latest.latest_time');
            })
            ->distinct()
            ->count('dsh.device_id');

        $in_use_devices = DB::table('device_status_history as dsh')
            ->join(DB::raw('(SELECT device_id, MAX(timestamp) as latest_time 
                            FROM device_status_history 
                            GROUP BY device_id) latest'), function($join) {
                $join->on('dsh.device_id', '=', 'latest.device_id')
                    ->on('dsh.timestamp', '=', 'latest.latest_time');
            })
            ->whereRaw('LOWER(dsh.status) = ?', ['in use'])
            ->count();

        $real_time_utilization = $total_devices > 0
            ? round(($in_use_devices / $total_devices) * 100, 2)
            : 0;

        // Total Energy (kWh) 總電量
        $total_energy_kwh = (float) DB::table('charging_sessions')
            ->where('energy_kwh', '>', 0) // 過濾掉負數
            ->selectRaw('COALESCE(SUM(energy_kwh), 0) as total_energy_kwh')
            ->value('total_energy_kwh');


        // Total CO2 Reduction (kg) 總減碳量
        $total_co2_reduction_kg = (float) DB::table('charging_sessions')
            ->selectRaw('COALESCE(SUM(co2_reduction_kg), 0) as total_co2_reduction_kg')
            ->value('total_co2_reduction_kg');

        // Total Revenue ($) 總營收
        $total_revenue_usd = (float) DB::table('payments')
            ->selectRaw('COALESCE(SUM(amount), 0) as total_revenue_usd')
            ->value('total_revenue_usd');

        // Abnormal Alerts 異常警示清單（供列表顯示）
        $abnormal_alerts = DB::table('alerts')
            ->select('id', 'device_id', 'alert_type', 'occurred_at')
            ->orderBy('occurred_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($row) {
                return [
                    'id'          => $row->id,
                    'device_id'   => $row->device_id,
                    'alert_type'  => $row->alert_type ?? '',
                    'occurred_at' => $row->occurred_at
                        ? Carbon::parse($row->occurred_at)->format('Y-m-d H:i:s')
                        : '',
                ];
            })
            ->values();

        // 取得 Maps：stations 的座標 + devices 的狀態
        $maps = DB::table('stations as st')
            ->leftJoin('devices as d', 'st.id', '=', 'd.station_id')
            ->select(
                'st.id as station_id',
                'st.name as station_name',
                'st.latitude',
                'st.longitude',
                'd.device_id',
                'd.status'
            )
            ->get();

        return view('dashboard', compact(
            'in_use',
            'disconnected_last_10m',
            'total_power_kw',
            'utilization_pct',
            'power_history',
            'utilization_history',
            'stations',
            'devices',
            'devices_with_status',
            'alerts',
            'device_logs',
            'avg_current_a',
            'avg_voltage_v',
            'avg_temperature_c',
            'all_status_history',
            // 新增（Dashboard-1/2 非地圖與非圖表項目）
            'devices_online',
            'charges_today',
            'real_time_utilization',            
            'total_energy_kwh',
            'total_revenue_usd',
            'total_co2_reduction_kg',
            'abnormal_alerts',
            'maps'
        ));


    }


}
