<?php

namespace App\Http\Controllers;

use App\Employees;
use App\Inventory;
use App\Logs;
use App\Softwares;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class SoftwareController extends Controller
{
    protected $title='Softwares';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = $this->title;

        $softwares = Softwares::orderBy('description')->get();
        return view('Softwares.index', compact('title'), ['softwares' => $softwares]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $record = new Softwares();
        $record->description = $request->description;
        $record->serial = $request->serial;
        $record->inventory_id = '1';
        $record->invoiceno = $request->invoiceno;
        $record->purdate = $request->purdate;
        $record->expdate = $request->expdate;
        $record->supplyer = $request->supplyer;
        $record->price = $request->price;
        $record->save();

        $logs= new Logs();
        if (Auth::check()){ $user = Auth::user()->name; $username = Auth::user()->username; } else { $user = Auth::guard('api')->user()->name; $username = Auth::guard('api')->user()->username; }
        $logs->user = $username;
        $logs->description = "New Software: ".$record->description."_".$record->serial;
        $logs->save();


        $newrecord = Softwares::join('inventory', 'software.inventory_id', '=', 'inventory.id')
            ->join('employees', 'inventory.employee', '=', 'employees.id')
            ->select('software.*', 'employees.networklogonname', 'inventory.serial as deviceserial')
            ->findOrFail($record->id);
        return response()->json($newrecord);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $record = Softwares::with('device')->findOrFail($id);
        $record->description = $request->description;
        $record->serial = $request->serial;
        $record->invoiceno = $request->invoiceno;
        $record->purdate = $request->purdate;
        $record->expdate = $request->expdate;
        $record->supplyer = $request->supplyer;
        $record->price = $request->price;

        /*****************
         * Log info gather
         */

        $original=$record->getOriginal();
        $change=$record->getDirty();
        $log="Changed properties on serial ".$record->serial." \n";

        foreach ($change as $key => $value){
            if (($value==null and $original[$key]=="") or ($value=="" and $original[$key]==null) or ($value=="" and $original[$key]=="0000-00-00")) {
            } else {
                $log .= "A " . $key . ": " . $original[$key] . " -> " . $value . "\n";
            }
        }

        /*************
         * Save update
         */

        $record->save();

        /***********
         * Log to DB
         */
        $logs = new Logs();
        $logs->user = Auth::user()->username;
        $logs->description = $log;
        $logs->save();

        /*********************
         * Return updated data
         */

        $newrecord = Softwares::join('inventory', 'software.inventory_id', '=', 'inventory.id')
            ->join('employees', 'inventory.employee', '=', 'employees.id')
            ->select('software.*', 'employees.networklogonname', 'inventory.serial as deviceserial')
            ->findOrFail($id);
        return response()->json($newrecord);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $software = Softwares::findOrFail($id);
        $software->delete();

        $logs= new Logs();
        if (Auth::check()){ $user = Auth::user()->name; $username = Auth::user()->username; } else { $user = Auth::guard('api')->user()->name; $username = Auth::guard('api')->user()->username; }
        $logs->user = $username;
        $logs->description = "Software deleted: ".$software->description."_".$software->serial;
        $logs->save();

        return response()->json($software);
    }

    public function getdevices($serial)
    {
        $software = Softwares::join('inventory', 'software.inventory_id', '=', 'inventory.id')
            ->join('employees', 'inventory.employee', '=', 'employees.id')
            ->select('inventory.serial', 'inventory.id as inventoryid', 'software.id as softwareid', 'employees.networklogonname')
            ->where('software.serial', '=', $serial)->get();
        return response()->json($software);
    }

    public function getuserlist(Request $request)
    {

        $users = Employees::join('inventory', 'inventory.employee', '=', 'employees.id')
            ->has('tools', '>', 0)
            ->where(function($sql){
                $sql->where('inventory.type', '=', 2)
                    ->orWhere('inventory.type', '=', 8)
                    ->orWhere('inventory.type', '=', 11);
            })
            ->select('networklogonname', 'employees.id')
            ->groupBy('networklogonname', 'employees.id')
            ->orderBy('networklogonname')
            ->get();
        return response()->json($users);
    }

    public function getdeviceperuser(Request $request)
    {

        $users = Inventory::where('employee', $request->id)
            ->where(function($sql){
                $sql->where('inventory.type', '=', 2)
                    ->orWhere('inventory.type', '=', 8)
                    ->orWhere('inventory.type', '=', 11);
            })
            ->select('description')
            ->groupBy('description')
            ->orderBy('description')
            ->get();
        return response()->json($users);
    }

    public function getserialperdevice(Request $request)
    {

        $users = Inventory::where('employee', $request->employee)
            ->where('description', $request->description)
            ->select('serial', 'id')
            ->orderBy('serial')
            ->get();
        return response()->json($users);
    }
}
