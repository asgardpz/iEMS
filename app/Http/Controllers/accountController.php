<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class accountController extends Controller
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

        //Member Management 
        $currentUser = Auth::user(); // 取得目前登入的使用者


        // 撈出會員清單 (可選)
        $members = DB::table('members')
            ->join('users', 'members.user_id', '=', 'users.id')
            ->leftjoin('roles', 'members.role_id', '=', 'roles.id')
            ->select(
                'members.id as staff_id',          // Staff ID
                'users.name',                      // Name
                'users.email',                     // Email
                'members.role_id',                 // 加這行
                'roles.role as assigned_role',     // Assigned Role
                'members.status',                  // Status
                'members.mobile',                  // mobile
                'users.password',                  // password
                'members.created_at as active_date', // Active Date
                'members.last_active as last_login'  // Last Login
            )
            ->where('members.user_id', $currentUser->id) 
            ->get();

            
        $members_table = DB::table('members')
            ->join('users', 'members.user_id', '=', 'users.id')
            ->leftjoin('roles', 'members.role_id', '=', 'roles.id')
            ->select(
                'members.id as staff_id',          // Staff ID
                'users.name',                      // Name
                'users.email',                     // Email
                'roles.role as assigned_role',     // Assigned Role
                'members.status',                  // Status
                'members.created_at as active_date', // Active Date
                'members.last_active as last_login'  // Last Login
            )
            ->whereBetween('members.created_at', [$startDate, $endDate])
            ->groupBy(
                'members.id',
                'users.name',
                'users.email',
                'roles.role',
                'members.status',
                'members.created_at',
                'members.last_active'
            )
            ->get();

        // StaffID 候選值 (members.id)
        $staff_id = DB::table('members')
            ->select('id as staff_id')
            ->distinct()
            ->get();

        // Name 候選值 (users.name)
        $names = DB::table('users')
            ->select('name')
            ->distinct()
            ->get();

        // Email 候選值 (users.email)
        $emails = DB::table('users')
            ->select('email')
            ->distinct()
            ->get();

        // Mobile 下拉選單 (members.mobile)
        $mobiles = DB::table('members')
            ->select('mobile')
            ->distinct()
            ->get();

        // Assigned Role 下拉選單 (roles.role)
        $roles = DB::table('roles')
            ->select('role')
            ->distinct()
            ->get();

        $roles_table = DB::table('roles')
            ->select('id','role')
            ->get();

        return view('account', compact(
            'currentUser', //member
            'members',
            'members_table',
            'staff_id',
            'names',
            'emails',
            'mobiles',
            'roles',
            'roles_table'      
        ));
    }


    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // 撈出所有角色給下拉選單
        $roles_table = DB::table('roles')->select('id','role')->get();

        // 只更新，不做新增
        $lastActiveValue = ($request->status === 'active') ? now() : null;

        DB::table('members')->where('user_id', $currentUser->id)->update([
            'status'      => $request->status,
            'mobile'      => $request->mobile,
            'role_id'     => $request->role_id,   // 更新角色 id
            'last_active' => $lastActiveValue,
            'updated_at'  => now(),
        ]);

        // 更新 users 表 (name, email)
        DB::table('users')->where('id', $currentUser->id)->update([
            'email'      => $request->email,
            'name'       => $request->name,
            'updated_at' => now(),
        ]);

        // 更新密碼 (如果有輸入新密碼)
        if ($request->filled('o_pass') && $request->filled('n_pass')) {
            if (!Hash::check($request->o_pass, $currentUser->password)) {
                return back()->withErrors(['o_pass' => '舊密碼錯誤']);
            }

            DB::table('users')->where('id', $currentUser->id)->update([
                'password'   => Hash::make($request->n_pass),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', '會員資料已成功更新！')
            ->with('roles_table', $roles_table); // 帶回 roles_table
    }    

    public function destroy($id)
    {
        // 先找 members
        $member = DB::table('members')->where('id', $id)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => '帳號不存在'
            ], 404);
        }

        // 刪 members
        DB::table('members')->where('id', $id)->delete();

        return redirect()->back()->with('success', '會員資料刪除成功！');
    }
    
}
