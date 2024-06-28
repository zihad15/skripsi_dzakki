<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\Models\IpFailedLoginAttempt;
use App\DataTables\IpFailedLoginAttemptDataTable;
use App\DataTables\IpFailedLoginAttemptListDataTable;
use DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role !== 'admin') {
                return abort(401);
            }

            return $next($request);
        });
    }

    public function index() {
        $data = [];
        $dates = [];
        $loginAttemptData = [];
        $currentDate = Carbon::now();
        
        for ($i = 0; $i < 30; $i++) {
            $dates[] = $currentDate->copy()->subDays($i)->format('M j');
            $loginAttemptData[] = IpFailedLoginAttempt::where('failed_attempt', '<>', 0)->where('created_at', 'LIKE', $currentDate->copy()->subDays($i)->format('Y-m-d').'%')->count();
        }

        // count
        $data['dates'] = array_reverse($dates);
        $data['loginAttemptData'] = array_reverse($loginAttemptData);
        $data['maxLimitData'] = max($loginAttemptData);

         // Render the DataTable
        $IpFailedLoginAttemptDataTable = new IpFailedLoginAttemptDataTable('datatablehistory');
        $IpFailedLoginAttemptListDataTable = new IpFailedLoginAttemptListDataTable('datatablelist');
        $data['dataTable'] = $IpFailedLoginAttemptDataTable->html();
        $data['dataTableList'] = $IpFailedLoginAttemptListDataTable->html();
        $data['listIpBlockedOrNot'] = IpFailedLoginAttempt::select('ip', 'input_username', 'failed_attempt')
            ->whereIn('id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('ip_failed_login_attempt')
                    ->groupBy('ip');
            })
            ->get();

        return view('admin.dashboard', $data);
    }

    public function ipFailedLoginAttemptData()
    {
        return datatables()->eloquent(IpFailedLoginAttempt::where('failed_attempt', '<>', 0)->select())
            ->editColumn('created_at', function ($data) {
                return $data->created_at->format('Y-m-d H:i:s'); // Custom format
            })
            ->toJson();
    }

    public function ipFailedLoginAttemptListData()
    {
        return datatables()->eloquent(IpFailedLoginAttempt::select('ip', 'input_username', 'failed_attempt')
            ->whereIn('id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('ip_failed_login_attempt')
                    ->groupBy('ip');
            })
            ->select())
            ->editColumn('status', function ($data) {
                if ($data->failed_attempt == 5) {
                    return "Blocked";
                } else {
                    return "Active";
                }
            })
            ->addColumn('action', function ($data) {
                if ($data->failed_attempt == 5) {
                    return '<form action="'.route('unblock').'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                <input type="text" name="ip_address" value="'.$data->ip.'" hidden />
                                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure?\')">Unblock</button>
                            </form>';
                } else {
                    return '<form action="'.route('block').'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                <input type="text" name="ip_address" value="'.$data->ip.'" hidden />
                                <button type="submit" class="btn btn-xs btn-primary" onclick="return confirm(\'Are you sure?\')">Block</button>
                            </form>';
                }
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function block() {
        IpFailedLoginAttempt::create([
            'ip' => request()->ip_address,
            'failed_attempt' => 5,
        ]);

        return redirect()->back();
    }

    public function unblock() {
        IpFailedLoginAttempt::create([
            'ip' => request()->ip_address,
            'failed_attempt' => 0,
        ]);

        return redirect()->back();
    }
}
