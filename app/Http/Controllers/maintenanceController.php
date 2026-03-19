<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class maintenanceController extends Controller
{
    public function show()
    {
        
        // 直接用 $_GET 取值，不用 Request
        $startDate = $_GET['startDate'] ?? null;
        $endDate   = $_GET['endDate'] ?? null;

        // 如果沒傳，預設三個月
        if (!$startDate || !$endDate) {
            $endDate   = now()->addDay(); // 改成「今天 +1 天」;
            $startDate = now()->subMonths(3);
        }

        // 今日營收
        $today_revenue = DB::table('payments')
            ->whereDate('transaction_time', DB::raw('CURDATE()'))
            ->sum('amount');

        // 當月營收
        $monthly_revenue = DB::table('payments')
            ->whereMonth('transaction_time', DB::raw('MONTH(CURDATE())'))
            ->whereYear('transaction_time', DB::raw('YEAR(CURDATE())'))
            ->sum('amount');

        // 成功交易數
        $successful_transactions = DB::table('charging_sessions')
            ->where('status', 'paid')
            ->count();

        // 失敗率 (charging_sessions.status = failed)
        $total_sessions = DB::table('charging_sessions')->count();
        $failed_sessions = DB::table('charging_sessions')
            ->where('status', 'failed')
            ->count();
        $failure_rate = $total_sessions > 0 ? round(($failed_sessions / $total_sessions) * 100, 2) : 0;

        // 交易表格資料 (JOIN 多個 Table)
        $transactions = DB::table('charging_sessions as cs')
            ->leftJoin('payments as p', 'cs.session_id', '=', 'p.session_id')
            ->leftJoin('devices as d', 'cs.device_id', '=', 'd.id')
            ->leftJoin('stations as s', 'd.station_id', '=', 's.id')
            ->leftJoin('users as u', 'cs.user_id', '=', 'u.id')
            ->select(
                'cs.session_id as transaction_id',
                'cs.start_at as date_time',
                'd.device_id',
                'u.name as member',
                's.city',
                's.district',
                'cs.energy_kwh as kWh',
                'p.amount',
                'p.method as payment_method',
                's.address',
                's.city',
                's.district',
                'cs.status as payment_status'
            )
            ->whereBetween('cs.start_at', [$startDate, $endDate])
            ->orderBy('cs.start_at', 'desc')
            ->get();




        // 今日故障數 (work_orders 當日 Pending/In Progress)
        $todays_faults = DB::table('work_orders')
            ->whereDate('created_time', DB::raw('CURDATE()'))
            ->whereIn('repair_status', ['Pending','In Progress'])
            ->count();

        // 今日建立工單數
        $created_work_orders = DB::table('work_orders')
            ->whereDate('created_time', DB::raw('CURDATE()'))
            ->count();

        // MTTR (Mean Time To Repair) = 平均 Completed 工單耗時
        $mttr = DB::table('work_orders')
            ->where('repair_status', 'Completed')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_time, inspection_date)) as avg_hours')
            ->value('avg_hours');

        // 當前離線設備數
        $offline_devices = DB::table('devices')
            ->where('status', 'offline')
            ->count();

 
        // 工單清單 (JOIN devices, stations, users)
        $work_orders = DB::table('work_orders as wo')
            ->leftJoin('devices as d', 'wo.device_id', '=', 'd.id')
            ->leftJoin('stations as s', 'd.station_id', '=', 's.id')
            ->leftJoin('users as u', 'wo.assigned_user', '=', 'u.id')
            ->select(
                'wo.work_order_code',
                'wo.created_time',
                'd.device_id',
                's.name as station_name',
                'wo.priority',
                'u.name as assignee',
                'wo.repair_status',
                'wo.inspection_date',
                's.address',
                's.city',
                's.district'
            )
            ->whereBetween('wo.created_time', [$startDate, $endDate])
            ->orderBy('wo.created_time', 'desc')
            ->get();

        // 今日故障數 (work_orders 當日 Pending/In Progress)
        $todays_faults_table = DB::table('work_orders as wo')
            ->leftJoin('devices as d', 'wo.device_id', '=', 'd.id')
            ->leftJoin('stations as s', 'd.station_id', '=', 's.id')
            ->select(
                DB::raw('TIME(wo.created_time) as time'),
                'd.device_id',
                's.city as location',
                'wo.priority',
                'wo.maintenance_items'
            )
            ->whereDate('wo.created_time', DB::raw('CURDATE()'))
            ->whereIn('wo.repair_status', ['Pending','In Progress'])
            ->orderBy('wo.created_time', 'asc')
            ->limit(4)
            ->get();

        // 維護排程 (Weekly)
        $maintenance_schedule = DB::table('work_orders as wo')
            ->leftJoin('devices as d', 'wo.device_id', '=', 'd.id')
            ->leftJoin('users as u', 'wo.assigned_user', '=', 'u.id')
            ->select(
                DB::raw('DATE(wo.inspection_date) as date'),
                'd.device_id',
                'wo.maintenance_items',
                'u.name as assignee',
                'wo.repair_status'
            )
            ->whereBetween('wo.inspection_date', [$startDate, $endDate])
            ->orderBy('wo.inspection_date', 'asc')
            ->limit(4)
            ->get();


        // 篩選用的清單
        $cities = DB::table('charging_sessions as cs')
            ->leftJoin('payments as p', 'cs.session_id', '=', 'p.session_id')
            ->leftJoin('devices as d', 'cs.device_id', '=', 'd.id')
            ->leftJoin('stations as s', 'd.station_id', '=', 's.id')
            ->leftJoin('users as u', 'cs.user_id', '=', 'u.id')
            ->select(
                's.city'
            )
            ->whereBetween('cs.start_at', [$startDate, $endDate])
            ->orderBy('cs.start_at', 'desc')
            ->get();        

        $districts = DB::table('charging_sessions as cs')
            ->leftJoin('payments as p', 'cs.session_id', '=', 'p.session_id')
            ->leftJoin('devices as d', 'cs.device_id', '=', 'd.id')
            ->leftJoin('stations as s', 'd.station_id', '=', 's.id')
            ->leftJoin('users as u', 'cs.user_id', '=', 'u.id')
            ->select(
                's.district'
            )
            ->whereBetween('cs.start_at', [$startDate, $endDate])
            ->orderBy('cs.start_at', 'desc')
            ->get();

        $transaction_id = DB::table('charging_sessions as cs')
            ->leftJoin('payments as p', 'cs.session_id', '=', 'p.session_id')
            ->leftJoin('devices as d', 'cs.device_id', '=', 'd.id')
            ->leftJoin('stations as s', 'd.station_id', '=', 's.id')
            ->leftJoin('users as u', 'cs.user_id', '=', 'u.id')
            ->select(
                'cs.session_id'
            )
            ->whereBetween('cs.start_at', [$startDate, $endDate])
            ->orderBy('cs.start_at', 'desc')
            ->get();

        $device_id  = DB::table('work_orders as wo')
            ->leftJoin('devices as d', 'wo.device_id', '=', 'd.id')
            ->leftJoin('stations as s', 'd.station_id', '=', 's.id')
            ->leftJoin('users as u', 'wo.assigned_user', '=', 'u.id')
            ->select(
                'd.device_id'
            )
            ->whereBetween('wo.created_time', [$startDate, $endDate])
            ->distinct() // 加這行去除重複
            ->get();
 
        $work_order_code = DB::table('work_orders as wo')
            ->leftJoin('devices as d', 'wo.device_id', '=', 'd.id')
            ->leftJoin('stations as s', 'd.station_id', '=', 's.id')
            ->leftJoin('users as u', 'wo.assigned_user', '=', 'u.id')
            ->select(
                'wo.work_order_code'
            )
            ->whereBetween('wo.created_time', [$startDate, $endDate])
            ->distinct() // 加這行去除重複
            ->get();

        $assignee = DB::table('work_orders as wo')
            ->leftJoin('devices as d', 'wo.device_id', '=', 'd.id')
            ->leftJoin('stations as s', 'd.station_id', '=', 's.id')
            ->leftJoin('users as u', 'wo.assigned_user', '=', 'u.id')
            ->select(
                'u.name as assignee'
            )
            ->whereBetween('wo.created_time', [$startDate, $endDate])
            ->distinct() // 加這行去除重複
            ->get();

        return view('maintenance', compact(
            'today_revenue',
            'monthly_revenue',
            'successful_transactions',
            'failure_rate',
            'transactions',
            'cities',
            'transaction_id',
            'device_id',
            'todays_faults',
            'created_work_orders',
            'mttr',
            'offline_devices',
            'today_revenue',
            'monthly_revenue',
            'successful_transactions',
            'failure_rate',
            'work_orders',
            'maintenance_schedule',
            'todays_faults_table',
            'work_order_code',
            'assignee'
        ));
    }
}
