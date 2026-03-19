<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeLoginController extends Controller
{
    public function generate($device_id)
    {
        $sessionId = bin2hex(random_bytes(16));

        DB::table('qrcode_sessions')->insert([
            'session_id' => $sessionId,
            'device_id' => $device_id,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        $url = route('qrcode.inuse', ['session_id' => $sessionId, 'device_id' => $device_id]);

        return response()->json([
            'result' => 'WAIT',
            'session_id' => $sessionId,
            'qr_url' => $url
        ]);
    }

    public function inuse($session_id, $device_id)
    {
        // 判斷是否登入
        if (!Auth::check()) {
            return view('success', [
                'result' => 'FAIL',
                'message' => 'User not logged in'
            ]);
        }

        $email = Auth::user()->email;
        $user = DB::table('users')->where('email', $email)->first();

        // 如果查不到使用者
        if (!$user) {
            return view('success', [
                'result' => 'FAIL',
                'message' => 'No user found for email: ' . $email
            ]);
        }

        DB::table('qrcode_sessions')
            ->where('session_id', $session_id)
            ->update([
                'status' => 'confirmed',
                'user_id' => $user->id,
                'confirmed_at' => now(),
            ]);

        return view('success', [
            'result' => 'OK',
            'message' => 'name:' . $user->name . ', id:' . (string)$user->id
        ]);
    }

    public function poll($session_id)
    {
        $row = DB::table('qrcode_sessions')->where('session_id', $session_id)->first();

        if (!$row) {
            return response()->json(['result' => 'FAIL', 'message' => '無效 session']);
        }

        if ($row->status === 'confirmed') {
            $user = DB::table('users')->where('id', $row->user_id)->first();
            return response()->json(['result' => 'OK', 'message' => $user->name ?? '']);
        }

        if ($row->status === 'expired') {
            return response()->json(['result' => 'FAIL', 'message' => '逾時']);
        }

        return response()->json(['result' => 'WAIT']);
    }

    public function send($device_id)
    {
        DB::table('qrcode_sessions')->insert([
            'session_id' => bin2hex(random_bytes(16)),
            'device_id' => $device_id,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return response()->json(['result' => 'WAIT', 'device_id' => $device_id]);
    }

    public function recive($device_id)
    {
        $row = DB::table('qrcode_sessions')
            ->where('device_id', $device_id)
            ->orderByDesc('created_at')
            ->first();

        if (!$row) {
            return response()->json(['result' => 'WAIT']);
        }

        if ($row->status === 'confirmed') {
            $user = DB::table('users')->where('id', $row->user_id)->first();
            return response()->json(['result' => 'OK', 'message' => $user->name ?? '']);
        }

        if ($row->status === 'expired') {
            return response()->json(['result' => 'FAIL', 'message' => '逾時']);
        }

        return response()->json(['result' => 'WAIT']);
    }

    public function useDevice(Request $request, $device_id)
    {
        $user = Auth::user();
        DB::table('qrcode_sessions')
            ->where('device_id', $device_id)
            ->orderByDesc('created_at')
            ->limit(1)
            ->update([
                'status' => 'confirmed',
                'user_id' => $user->id,
                'confirmed_at' => now(),
            ]);

        // 判斷請求是否要 JSON
        if ($request->wantsJson()) {
            return response()->json([
                'result' => 'OK',
                'message' => 'name:' . ($user->name ?? '') . ', id:' . (string)($user->id ?? '')
            ]);
        }

        // 否則回 Blade View
        return view('success', [
            'result' => 'OK',
            'message' => 'name:' . ($user->name ?? '') 
        ]);

        //return response()->json([
        //    'result'  => 'OK',
        //    'message' => 'name:' . ($user->name ?? '') . ', id:' . (string)($user->id ?? '')
        //]);

    }

    public function waitDevice($device_id)
    {
        $row = DB::table('qrcode_sessions')
            ->where('device_id', $device_id)
            ->orderByDesc('created_at')
            ->first();

        if (!$row) {
            // 沒有紀錄 → 等待中
            return response()->json(['result' => 'WAIT', 'device_id' => $device_id]);
        }

        if ($row->status === 'confirmed') {
            $user = DB::table('users')->where('id', $row->user_id)->first();
            return response()->json(['result' => 'OK', 'device_id' => $device_id,  'message' => 'name:' . ($user->name ?? '') . ', id:' . (string)($user->id ?? '')]);
        }

        if ($row->status === 'expired') {
            return response()->json(['result' => 'FAIL', 'device_id' => $device_id, 'message' => '逾時']);
        }

        return response()->json(['result' => 'WAIT', 'device_id' => $device_id]);
    }

}





