<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
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

        $devices_with_status = DB::table('devices as d')
            // 先算出每個 device 最新的 timestamp
            ->leftJoinSub(function ($query) {
                $query->from('device_status_history')
                    ->select('device_id', DB::raw('MAX(timestamp) as latest_ts'))
                    ->groupBy('device_id');
            }, 'latest', function ($join) {
                $join->on('d.id', '=', 'latest.device_id');
            })
            // 再把最新 timestamp join 回 device_status_history，取完整紀錄
            ->leftJoin('device_status_history as dsh', function ($join) {
                $join->on('d.id', '=', 'dsh.device_id');
            })
            // 加入 stations
            ->leftJoin('stations as st', 'd.station_id', '=', 'st.id')
            ->select(
                'd.device_id',
                'd.status',
                'dsh.current_a',
                'dsh.voltage_v',
                'dsh.temperature_c',
                'dsh.timestamp',
                'st.address',
                'st.city',
                'st.district',
                'st.code',
                'd.type',
                'd.firmware_version',
                'd.last_online_at'
            )
            ->get();            

        // 每個 device 的事件紀錄（timestamp + status）
        $device_logs_raw = DB::table('device_status_history')
            ->select('device_id', 'timestamp', 'status')
            ->orderByDesc('timestamp')
            ->get();

        $device_logs = [];
        foreach ($device_logs_raw as $log) {
            $id = $log->device_id;
            $device_logs[$id][] = [
                'timestamp' => $log->timestamp,
                'status' => $log->status,
            ];
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

        // 從 devices 表抓出所有不同的 status
        $types = DB::table('devices')
            ->select('type')
            ->distinct()
            ->pluck('type')
            ->map(fn($s) => strtolower($s)) // 統一小寫
            ->values();

        // 從 devices 表抓出所有不同的 firmware_version
        $firmwares = DB::table('devices')
            ->select('firmware_version')
            ->distinct()
            ->pluck('firmware_version')
            ->filter() // 過濾掉 null
            ->values();

        return view('Device', compact(
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
            'maps',
            'types',
            'firmwares'
        ));


    }


}
