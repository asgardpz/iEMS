<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class workorderController extends Controller
{

    public function index(Request $request)
    {
        $startDate = $request->query('startDate');
        $endDate   = $request->query('endDate');

        if (!$startDate || !$endDate) {
            $endDate   = now()->addDay();
            $startDate = now()->subMonths(3);
        }

        $today = now()->format('Ymd');

        // 生成新工單編號
        $latestOrder = DB::table('work_orders')
            ->whereDate('created_time', now()->toDateString())
            ->orderBy('created_time', 'desc')
            ->first();

        if ($latestOrder) {
            $parts = explode('-', $latestOrder->work_order_code);
            $sequence = intval($parts[2]);
            $newSequence = str_pad($sequence + 1, 4, '0', STR_PAD_LEFT);
            $new_order = "W-{$today}-{$newSequence}";
        } else {
            $new_order = "W-{$today}-0001";
        }

        // 查詢工單清單
        $work_orders = DB::table('work_orders')
            ->select('work_order_code','created_time','device_id','segment',
                    'current_status','source','repair_status','maintenance_items',
                    'priority','inspection_date')
            ->whereBetween('created_time', [$startDate, $endDate])
            ->orderBy('created_time','desc')
            ->get();

        // 統計數據
        $todays_faults = DB::table('work_orders')
            ->whereDate('created_time', DB::raw('CURDATE()'))
            ->whereIn('repair_status', ['Pending','In Progress'])
            ->count();

        $created_work_orders = DB::table('work_orders')
            ->whereDate('created_time', DB::raw('CURDATE()'))
            ->count();

        $mttr = DB::table('work_orders')
            ->where('repair_status', 'Completed')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_time, inspection_date)) as avg_hours')
            ->value('avg_hours');

        $offline_devices = DB::table('work_orders')
            ->where('current_status', 'Offline')
            ->count();

        $users = DB::table('users')->select('id','name')->get();

        $devices = DB::table('devices')
            ->join('stations','devices.station_id','=','stations.id')
            ->select('devices.id as device_pk','devices.device_id',
                    'stations.address','stations.latitude','stations.longitude',
                    'devices.status')
            ->get();

        // 新增：如果有 work_order_code 參數，查出該工單
        $selectedOrder = null;
        if ($request->has('work_order_code')) {
            $selectedOrder = DB::table('work_orders')
                ->where('work_order_code', $request->query('work_order_code'))
                ->first();
        }

        $selectedOrder = null;
        $selectedDevice = null;

        if ($request->has('work_order_code')) {
            $selectedOrder = DB::table('work_orders')
                ->where('work_order_code', $request->query('work_order_code'))
                ->first();

            if ($selectedOrder) {
                $selectedDevice = DB::table('devices')
                    ->join('stations','devices.station_id','=','stations.id')
                    ->select(
                        'devices.id as device_pk',
                        'devices.device_id',
                        'stations.address',
                        'stations.latitude',
                        'stations.longitude',
                        'devices.status'
                    )
                    ->where('devices.id', $selectedOrder->device_id)
                    ->first();
            }
        }


        return view('workorders.index', compact(
            'work_orders',
            'todays_faults',
            'created_work_orders',
            'mttr',
            'offline_devices',
            'new_order',
            'users',
            'devices',
            'selectedOrder', // 傳到 Blade
            'selectedDevice' 
        ));
    }

    public function store(Request $request)
    {
        if ($request->has('work_order_code')) {
            // 更新工單
            DB::table('work_orders')
                ->where('work_order_code', $request->input('work_order_code'))
                ->update([
                    'device_id'         => $request->input('device_pk'),
                    'segment'           => $request->input('segment'),
                    'current_status'    => $request->input('current_status'),
                    'source'            => $request->input('source'),
                    'description'       => $request->input('source_description') 
                                        ?? $request->input('maintenance_description'),
                    'repair_status'     => $request->input('repair_status'),
                    'maintenance_items' => $request->input('Maintenance'),
                    'assigned_user'     => $request->input('assigned_user'),
                    'priority'          => $request->input('priority'),
                    'inspection_date'   => $request->input('inspection_date')
                ]);

            return redirect()->route('maintenance')
                            ->with('success', 'Work order updated successfully!');
        } else {
            // 新增工單
            DB::table('work_orders')->insert([
                'work_order_code'   => $request->input('work_order_code'),
                'created_time'      => now()->format('Y-m-d H:i:s'),
                'device_id'         => $request->input('device_pk'),
                'segment'           => $request->input('segment'),
                'current_status'    => $request->input('current_status'),
                'source'            => $request->input('source'),
                'description'       => $request->input('source_description') 
                                    ?? $request->input('maintenance_description'),
                'repair_status'     => $request->input('repair_status'),
                'maintenance_items' => $request->input('Maintenance'),
                'assigned_user'     => $request->input('assigned_user'),
                'priority'          => $request->input('priority'),
                'inspection_date'   => $request->input('inspection_date'),
            ]);

            return redirect()->route('workorders.index')
                            ->with('success', 'Work order created successfully!');
        }
    }

    public function create(Request $request)
    {
        $startDate = $request->query('startDate');
        $endDate   = $request->query('endDate');

        if (!$startDate || !$endDate) {
            $endDate   = now()->addDay();
            $startDate = now()->subMonths(3);
        }

        $today = now()->format('Ymd');

        // 生成新工單編號
        $latestOrder = DB::table('work_orders')
            ->whereDate('created_time', now()->toDateString())
            ->orderBy('created_time', 'desc')
            ->first();

        if ($latestOrder) {
            $parts = explode('-', $latestOrder->work_order_code);
            $sequence = intval($parts[2]);
            $newSequence = str_pad($sequence + 1, 4, '0', STR_PAD_LEFT);
            $new_order = "W-{$today}-{$newSequence}";
        } else {
            $new_order = "W-{$today}-0001";
        }

        // 查詢工單清單
        $work_orders = DB::table('work_orders')
            ->select('work_order_code','created_time','device_id','segment',
                    'current_status','source','repair_status','maintenance_items',
                    'priority','inspection_date')
            ->whereBetween('created_time', [$startDate, $endDate])
            ->orderBy('created_time','desc')
            ->paginate(10);

        // 統計數據
        $todays_faults = DB::table('work_orders')
            ->whereDate('created_time', DB::raw('CURDATE()'))
            ->whereIn('repair_status', ['Pending','In Progress'])
            ->count();

        $created_work_orders = DB::table('work_orders')
            ->whereDate('created_time', DB::raw('CURDATE()'))
            ->count();

        $mttr = DB::table('work_orders')
            ->where('repair_status', 'Completed')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_time, inspection_date)) as avg_hours')
            ->value('avg_hours');

        $offline_devices = DB::table('work_orders')
            ->where('current_status', 'Offline')
            ->count();

        $users = DB::table('users')->select('id','name')->get();

        $devices = DB::table('devices')
            ->join('stations','devices.station_id','=','stations.id')
            ->select('devices.id as device_pk','devices.device_id',
                    'stations.address','stations.latitude','stations.longitude',
                    'devices.status')
            ->get();

        // 新增：如果有 work_order_code 參數，查出該工單
        $selectedOrder = null;
        if ($request->has('work_order_code')) {
            $selectedOrder = DB::table('work_orders')
                ->where('work_order_code', $request->query('work_order_code'))
                ->first();
        }

        $selectedOrder = null;
        $selectedDevice = null;

        if ($request->has('work_order_code')) {
            $selectedOrder = DB::table('work_orders')
                ->where('work_order_code', $request->query('work_order_code'))
                ->first();

            if ($selectedOrder) {
                $selectedDevice = DB::table('devices')
                    ->join('stations','devices.station_id','=','stations.id')
                    ->select(
                        'devices.id as device_pk',
                        'devices.device_id',
                        'stations.address',
                        'stations.latitude',
                        'stations.longitude',
                        'devices.status'
                    )
                    ->where('devices.id', $selectedOrder->device_id)
                    ->first();
            }
        }


        return view('workorders.create', compact(
            'work_orders',
            'todays_faults',
            'created_work_orders',
            'mttr',
            'offline_devices',
            'new_order',
            'users',
            'devices',
            'selectedOrder', // 傳到 Blade
            'selectedDevice' 
        ));
    }

    public function save(Request $request)
    {
        // 新增工單
        DB::table('work_orders')->insert([
            'work_order_code'   => $request->input('work_order_code'),
            'created_time'      => now()->format('Y-m-d H:i:s'),
            'device_id'         => $request->input('device_pk'),
            'segment'           => $request->input('segment'),
            'current_status'    => $request->input('current_status'),
            'source'            => $request->input('source'),
            'description'       => $request->input('source_description') 
                                ?? $request->input('maintenance_description'),
            'repair_status'     => $request->input('repair_status'),
            'maintenance_items' => $request->input('Maintenance'),
            'assigned_user'     => $request->input('assigned_user'),
            'priority'          => $request->input('priority'),
            'inspection_date'   => $request->input('inspection_date'),
        ]);

        return redirect()->route('maintenance')
                        ->with('success', 'Work order created successfully!');

    }

}
