<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class permissionController extends Controller
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

        //Permission Management 
        $currentUser = Auth::user(); // 取得目前登入的使用者

        // 檢查是否已有 Permission 資料
        $existing = DB::table('members')->where('user_id', $currentUser->id)->first();

        $new_member_id = null;

        if ($existing) {
            // 已存在 → 直接帶出
            $new_member_id = $existing->member_id;
        } else {
            // 沒有資料 → 計算一個新的 member_id，但不寫入
            $maxId = DB::table('members')->max('id'); // 找目前最大的 id
            $nextId = $maxId ? $maxId + 1 : 1;        // 如果有值就 +1，沒有就從 1 開始
            $new_member_id = sprintf("U-%06d", $nextId);
        }

        // 撈出會員清單 (可選)
        $members = DB::table('members')
            ->join('users','members.user_id','=','users.id')
            ->select(
                'members.id',
                'members.member_id',
                'members.status',
                'members.mobile',
                'members.plate_no',
                'members.membership_segment',
                'members.rfid_no',
                'members.last_active',
                'users.name',
                'users.email'
            )
            ->where('members.user_id', $currentUser->id) 
            ->get();

        // 判斷按鈕文字
        $buttonLabel = $members->isEmpty() ? 'New' : 'Edit';

        // 1. Total Members
        $totalMembers = DB::table('members')->count();

        // 2. Daily Active Users (DAU)
        $dailyActiveUsers = DB::table('members')
            ->whereDate('last_active', now()->toDateString())
            ->count();

        // 3. Avg. Sessions/User (Last 30D)
        $avgSessionsPerUser = DB::table('charging_sessions')
            ->where('start_at', '>=', now()->subDays(30))
            ->selectRaw('COUNT(*) / COUNT(DISTINCT user_id) as avg_sessions')
            ->value('avg_sessions');

        // 4. New Sign-ups (Last 7D)
        $newSignups = DB::table('users')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $members_table = DB::table('members')
            ->join('users', 'members.user_id', '=', 'users.id')
            ->leftJoin('charging_sessions', function($join) {
                $join->on('members.user_id', '=', 'charging_sessions.user_id')
                     ->where('charging_sessions.start_at', '>=', now()->subDays(30));
            })
            ->leftJoin('payments', 'charging_sessions.session_id', '=', 'payments.session_id')
            ->select(
                'members.member_id',
                'users.name',
                'users.email',
                'members.plate_no',
                'members.status',
                'members.last_active',
                DB::raw('COUNT(DISTINCT charging_sessions.id) as sessions_last_30d'),
                DB::raw('COALESCE(SUM(charging_sessions.energy_kwh),0) as energy_last_30d'),
                DB::raw('COALESCE(SUM(payments.amount),0) as amount_last_30d')
            )
            ->whereBetween('members.created_at', [$startDate, $endDate])
            ->groupBy(
                'members.member_id',
                'users.name',
                'users.email',
                'members.plate_no',
                'members.status',
                'members.last_active'
            )
            ->get();

        $member_id = DB::table('members')
            ->select('member_id')
            ->get();

        $plate_no = DB::table('members')
            ->select('plate_no')
            ->distinct() 
            ->get();

        // 取出所有 roles
        $roles_table = DB::table('roles')
            ->select('*')
            ->get();

        return view('permission', compact(
            'currentUser', //member
            'members',
            'new_member_id',
            'buttonLabel',
            'totalMembers',
            'dailyActiveUsers',
            'avgSessionsPerUser',
            'newSignups',
            'members_table',
            'member_id',
            'plate_no',
            'roles_table'             
        ));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // 檢查是否已有該角色
        $existing = DB::table('roles')->where('role', $request->role)->first();

        if ($existing) {
            // 有資料 → Update
            DB::table('roles')->where('role', $request->role)->update([
                'realtime'    => $request->has('realtime'),
                'dashboard'   => $request->has('dashboard'),
                'transactions'=> $request->has('transactions'),
                'maintenance' => $request->has('maintenance'),
                'reports'     => $request->has('reports'),
                'device'      => $request->has('device'),
                'workorder'   => $request->has('workorder'),
                'member'      => $request->has('member'),
                'permission'  => $request->has('permission'),
                'account'     => $request->has('account')
            ]);
        } else {
            // 沒資料 → Insert
            DB::table('roles')->insert([
                'role'        => $request->role,
                'realtime'    => $request->has('realtime'),
                'dashboard'   => $request->has('dashboard'),
                'transactions'=> $request->has('transactions'),
                'maintenance' => $request->has('maintenance'),
                'reports'     => $request->has('reports'),
                'device'      => $request->has('device'),
                'workorder'   => $request->has('workorder'),
                'member'      => $request->has('member'),
                'permission'  => $request->has('permission'),
                'account'     => $request->has('account')
            ]);
        }

        return redirect()->back()->with('success', '角色與權限已成功儲存！');
    }



}
