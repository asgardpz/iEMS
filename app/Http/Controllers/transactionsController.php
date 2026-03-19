<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class transactionsController extends Controller
{
    public function show()
    {
        
        // 直接用 $_GET 取值，不用 Request
        $startDate = $_GET['startDate'] ?? null;
        $endDate   = $_GET['endDate'] ?? null;

        // 如果沒傳，預設三個月
        if (!$startDate || !$endDate) {
            $endDate   = now()->addDay(); // 改成「今天 +1 天」
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
            ->distinct() // 加這行去除重複
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

        $device_id = DB::table('charging_sessions as cs')
            ->leftJoin('payments as p', 'cs.session_id', '=', 'p.session_id')
            ->leftJoin('devices as d', 'cs.device_id', '=', 'd.id')
            ->leftJoin('stations as s', 'd.station_id', '=', 's.id')
            ->leftJoin('users as u', 'cs.user_id', '=', 'u.id')
            ->select(
                'd.device_id'
            )
            ->whereBetween('cs.start_at', [$startDate, $endDate])
            ->orderBy('cs.start_at', 'desc')
            ->get();


        return view('transactions', compact(
            'today_revenue',
            'monthly_revenue',
            'successful_transactions',
            'failure_rate',
            'transactions',
            'cities',
            'transaction_id',
            'device_id'
        ));
    }


}
