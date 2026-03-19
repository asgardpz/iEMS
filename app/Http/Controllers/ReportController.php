<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function show()
    {
        // KPI
        $devices_online = DB::table('devices')->whereRaw('LOWER(status) = ?', ['online'])->count();
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
        $devices = DB::table('devices')->get();
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
                    'timestamp' => $row->timestamp ? \Carbon\Carbon::parse($row->timestamp)->format('Y-m-d H:i:s') : '',
                    'power_kw'  => is_null($row->power_kw) ? 0 : (float)$row->power_kw,
                    'current_a' => is_null($row->current_a) ? 0 : (float)$row->current_a,
                    'voltage_v' => is_null($row->voltage_v) ? 0 : (float)$row->voltage_v,
                    'temperature_c' => is_null($row->temperature_c) ? 0 : (float)$row->temperature_c,
                ];
            })
            ->values();
            
        // Page 15
        $total_charging_sessions = DB::table('charging_sessions')->count();
        $total_energy_consumption = DB::table('charging_sessions')->sum('energy_kwh');
        $total_revenue = DB::table('payments')->sum('amount');
        $avg_utilization_rate = DB::table('device_status_history')->avg('power_kw');

        $charging_sessions_daily = DB::table('charging_sessions')
            ->select(DB::raw('DATE(start_at) as date'), DB::raw('COUNT(id) as sessions'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $energy_consumption_daily = DB::table('charging_sessions')
            ->select(
                DB::raw('DATE(start_at) as date'),
                DB::raw('SUM(CASE WHEN energy_kwh < 0 THEN 0 ELSE CAST(energy_kwh AS DECIMAL(12,2)) END) as total_kwh')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();


        $top10_revenue = DB::table('payments as p')
            ->join('charging_sessions as cs', 'p.session_id', '=', 'cs.session_id') // 用 session_id 對 session_id
            ->join('devices as d', 'cs.device_id', '=', 'd.id')
            ->join('stations as s', 'd.station_id', '=', 's.id')
            ->whereRaw("p.amount REGEXP '^[0-9]+(\\.[0-9]+)?$'")
            ->select('s.name as station', DB::raw('SUM(p.amount) as total_revenue'))
            ->groupBy('s.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $member_ratio = DB::table('charging_sessions')
            ->select(DB::raw("CASE WHEN user_id IS NULL THEN 'non_member' ELSE 'member' END as type"),
                    DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get();

        $heatmap_data = DB::table('charging_sessions')
            ->select(DB::raw('WEEKDAY(start_at) as weekday'), DB::raw('HOUR(start_at) as hour'), DB::raw('COUNT(*) as density'))
            ->groupBy('weekday','hour')
            ->get();

        $station_summary = DB::table('stations')
            ->join('devices', 'stations.id', '=', 'devices.station_id')
            ->join('charging_sessions', 'devices.id', '=', 'charging_sessions.device_id')
            ->leftJoin('payments', 'charging_sessions.id', '=', 'payments.session_id')
            ->leftJoin('device_status_history', 'devices.id', '=', 'device_status_history.device_id')
            ->select(
                'stations.name as station',
                DB::raw('SUM(CASE WHEN charging_sessions.energy_kwh < 0 THEN 0 ELSE charging_sessions.energy_kwh END) as kwh'),
                DB::raw('COUNT(charging_sessions.id) as sessions'),
                DB::raw('ROUND(AVG(device_status_history.power_kw),2) as utilization'),
                DB::raw('SUM(payments.amount) as revenue')
            )
            ->groupBy('stations.name')
            ->orderByDesc('revenue')
            ->get();

        // 16頁 3：每日充電次數
        $charging_sessions_daily = DB::table('charging_sessions')
            ->selectRaw('DATE(start_at) as date, COUNT(id) as sessions')
            ->groupBy(DB::raw('DATE(start_at)'))
            ->orderBy(DB::raw('DATE(start_at)'))
            ->get();

        // 16頁 3：每日電量總計（防止負數）
        $energy_consumption_daily = DB::table('charging_sessions')
            ->selectRaw('DATE(start_at) as date, SUM(CASE WHEN energy_kwh < 0 THEN 0 ELSE energy_kwh END) as total_kwh')
            ->groupBy(DB::raw('DATE(start_at)'))
            ->orderBy(DB::raw('DATE(start_at)'))
            ->get();

        // 17頁 7：付款方式佔比
        $payment_method_distribution = DB::table('payments')
            ->select('method', DB::raw('COUNT(*) as count'))
            ->groupBy('method')
            ->orderByDesc('count')
            ->get();


        return view('report', compact(
            'devices_online',
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

            // Page 15
            'total_charging_sessions',
            'total_energy_consumption',
            'total_revenue',
            'avg_utilization_rate',

            // Page 16
            'charging_sessions_daily',
            'energy_consumption_daily',
            'top10_revenue',
            'member_ratio',
            'heatmap_data',
            'payment_method_distribution',

            // Page 17
            'station_summary'
        ));



    }


}
