<?php
/**
 * Created by PhpStorm.
 * User: dposztos
 * Date: 2018. 08. 16.
 * Time: 10:37
 */

namespace App\Http\Controllers;

use App\Employees;
use App\Equtypes;
use App\Inventory;
use App\Logs;
use App\Notifications;
use App\Personal_inventory;
use App\Returndoc;
use App\Sites;

use App\Softwares;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InventoryController extends Controller
{
    protected $title='IT Készlet';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = $this->title;

        $records = Inventory::orderBy('id', 'desc')->get();
        $types = Equtypes::orderBy('EquipmentType')->get();
        $sites = Sites::orderBy('compcode')->get();
        $employees = Employees::orderBy('networklogonname')->get();

        return view('Inventory.index', compact('title'), ['records' => $records, 'types' => $types, 'sites' => $sites, 'employees' => $employees]);
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
        $record = new Inventory();
        $record->description = $request->description;
        $record->type = $request->type;
        $record->serial = $request->serial;
        $record->location = $request->location;
        $record->employee = 158;
        $record->pin = $request->pin;
        $record->puk = $request->puk;
        $record->invoiceno = $request->invoiceno;
        $record->purdate = $request->purdate;
        $record->supplyer = $request->supplyer;
        $record->price = $request->price;
        $record->warranty = $request->warranty;
        $record->note = $request->note;

        $logs = new Logs();
        if (Auth::check()){ $user = Auth::user()->name; $username = Auth::user()->username; } else { $user = Auth::guard('api')->user()->name; $username = Auth::guard('api')->user()->username; }
        $logs->user = $username;
        $logs->description = "New device: ".$record->description."_".$record->serial;
        $logs->save();

        $recipients = Notifications::all('address');
        $subject = "Új eszköz";
        foreach ($recipients as $recipient) {
            Mail::send(['html' => 'emails.newdevice'], ['record' => $record, 'user' => $user], function ($message) use ($recipient, $subject) {
                $message->sender(env('MAIL_FROM'), env('APP_NAME'))->to($recipient->address)->subject($subject);
            });
        }

        $record->save();

        $record = Inventory::with('owner')->with('loc')->with('equtype')->findOrFail($record->id);
        return response()->json($record);
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
        $record = Inventory::with('owner')->with('loc')->with('equtype')->findOrFail($id);
        $record->description = $request->description;
        $record->type = $request->type;
        $record->serial = $request->serial;
        $record->location = $request->location;
        $record->pin = $request->pin;
        $record->puk = $request->puk;
        $record->invoiceno = $request->invoiceno;
        $record->purdate = $request->purdate;
        $record->supplyer = $request->supplyer;
        $record->price = $request->price;
        $record->warranty = $request->warranty;
        $record->note = $request->note;


        /*****************
         * Log info gather
         */

        $original=$record->getOriginal();
        $change=$record->getDirty();
        $log="Changed properties on serial ".$record->serial." \n";

        foreach ($change as $key => $value){
            if ($key == "type") {
                $new = Equtypes::findOrFail($value);
                $old = Equtypes::findOrFail($original[$key]);
                $log.="A típus: ".$old->EquipmentType." -> ".$new->EquipmentType."\n";
            } elseif ($key == "location") {
                $new = Sites::findOrFail($value);
                $old = Sites::findOrFail($original[$key]);
                $log.="A hely: ".$old->compcode." -> ".$new->compcode."\n";
            }
            else {
                if (($value==null and $original[$key]=="") or ($value=="" and $original[$key]==null) or ($value=="" and $original[$key]=="0000-00-00")) {
                } else {
                    $log .= "A " . $key . ": " . $original[$key] . " -> " . $value . "\n";
                }
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

        $record = Inventory::with('owner')->with('loc')->with('equtype')->findOrFail($id);
        return response()->json($record);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function handover()
    {
        $datas = json_decode(stripslashes($_POST['data']), true);
        $whom = json_decode(stripslashes($_POST['whom']), true);

        $newowner = Employees::findOrFail($whom);
        $user = Auth::user()->name;

        $items = array();

        foreach ($datas as $data){
            $record = Inventory::with('owner')->findOrFail($data);
            $record->employee = $whom;
            $items[] = $record;

            /****************
             * Do the Logging
             */

            $original=$record->getOriginal();
            $log="Changed owner on serial ".$record->serial." \n";
            $old = Employees::findOrFail($original['employee']);
            $log .= $old->networklogonname." -> ". $newowner->networklogonname." \n";

            $logs = new Logs();
            $logs->user = Auth::user()->username;
            $logs->description = $log;
            $logs->save();

            /*****************
             * Save the change
             */

            $record->save();
        }

        /*******************
         * Send notification
         */

        $recipients = Notifications::all('address');
        if ($whom==0){
            $subject = "Selejtezés";
            foreach ($recipients as $recipient){
                Mail::send( ['html' => 'emails.scrapping'], ['items' => $items, 'user' => $user], function($message) use ($recipient, $subject)
                {
                    $message->sender(env('MAIL_FROM'), env('APP_NAME'))->to($recipient->address)->subject($subject);
                });
            }
        } else {
            $employeename = $newowner->lastname . " " . $newowner->firstname;
            $subject = "Kiadás";
            foreach ($recipients as $recipient){
                Mail::send( ['html' => 'emails.handover'], ['employeename' => $employeename, 'items' => $items, 'user' => $user], function($message) use ($recipient, $subject)
                {
                    $message->sender(env('MAIL_FROM'), env('APP_NAME'))->to($recipient->address)->subject($subject);
                });
            }
        }
    }

    public function takeback()
    {
        $datas = json_decode(stripslashes($_POST['data']), true);
        $user = Auth::user()->name;
        $subject='Visszavétel';
        $items = array();

        foreach ($datas as $data){
            $record = Inventory::with('owner')->findOrFail($data);
            $record->employee = 158;
            $items[] = $record;
            $employeename = $record->owner->lastname." ".$record->owner->firstname;

            /****************
             * Do the Logging
             */

            $log="Changed owner on serial ".$record->serial." \n";
            $log .= $employeename." -> IT osztály \n";

            $logs = new Logs();
            $logs->user = Auth::user()->username;
            $logs->description = $log;
            $logs->save();

            /*****************
             * Save the change
             */

            $record->save();
        }

        /*******************
         * Send notification
         */

        $recipients = Notifications::all('address');
        foreach ($recipients as $recipient){
            Mail::send( ['html' => 'emails.takeback'], ['employeename' => $employeename, 'items' => $items, 'user' => $user], function($message) use ($recipient, $subject)
            {
                $message->sender(env('MAIL_FROM'), env('APP_NAME'))->to($recipient->address)->subject($subject);
            });
        }
    }

    public function takebackdoc($owner, $array)
    {
        $ids = json_decode($array);
        $data = array();
        $employee = array();

        $fpdf = new Returndoc();
        $fpdf->AliasNbPages();
        $fpdf->AddPage();
        $fpdf->SetFont('Arial','',10);
        $oldowner = Employees::where('networklogonname',$owner)->get();
        $responsible = Employees::where('networklogonname',Auth::user()->username)->get();
        $employee[] = $oldowner[0]->lastname." ".$oldowner[0]->firstname;
        $employee[] = $responsible[0]->lastname." ".$responsible[0]->firstname;

        foreach ($ids as $id) {
            $record = Inventory::with('owner')->with('loc')->with('equtype')->findOrFail($id);
            $data[] = $record->description;
            $data[] = $record->serial;

        }



        $fpdf->FancyTable($data, $employee);

        $pdfContent = $fpdf->Output('', "S");

        return response($pdfContent, 200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Length'      =>  strlen($pdfContent),
                'Cache-Control'       => 'private, max-age=0, must-revalidate',
                'Pragma'              => 'public'
            ]
        );
    }

    public function personal_inventory_sign(Request $request)
    {
        if (isset($request->signature))
        {
            $tosave = True;
            $dataURI = $request->signature;
            $owner = $request->user;
        }
        $data = array();

        $fpdf = new Personal_inventory();
        $fpdf->AliasNbPages();
        $fpdf->AddPage();
        $fpdf->SetFont('Arial','',10);

        $record = Employees::where('networklogonname',$owner)->get();
        $employee = $record[0]->lastname." ".$record[0]->firstname;
        $networklogonname = $record[0]->networklogonname;
        $emp_id = $record[0]->id;

        $tools = Inventory::where('employee', $emp_id)->get();

        foreach ($tools as $tool) {
            $handover = Logs::where('description', 'like', '%Eszköz kiadás '.$tool->serial.'%')->orWhere('description', 'like', '%Changed owner on serial '.$tool->serial.'%')->orderBy('created_at','desc')->get()->first();;
            if ( !$handover) {$date="";} else {$date=$handover->created_at;}

            if ($tool->serial == "") {$date="";}
            $data[] = $tool->description;
            $data[] = $tool->serial;
            $data[] = $date;
            $data[] = 'Office';
        }

        $header = array('Eszköz neve:', 'Szériaszáma', 'Felhasználás kezdete', 'Helye');
        $fpdf->FancyTable($employee, $networklogonname, $header, $data, $dataURI);
        $filename = "/var/www/html/itinventory/public/signed/".$owner.".pdf";
        $pdfContent = $fpdf->Output($filename, "F");

        return view('Inventory.document', compact('title'), ['pdfContent' => base64_encode($pdfContent)]);

    }

    public function personal_inventory_signed($owner)
    {
        return view('Inventory.signed', compact('title'), ['user' => $owner]);
    }

    public function personal_inventory($owner)
    {
        $data = array();

        $fpdf = new Personal_inventory();
        $fpdf->AliasNbPages();
        $fpdf->AddPage();
        $fpdf->SetFont('Arial','',10);

        $record = Employees::where('networklogonname',$owner)->get();
        $employee = $record[0]->lastname." ".$record[0]->firstname;
        $networklogonname = $record[0]->networklogonname;
        $emp_id = $record[0]->id;

        $tools = Inventory::where('employee', $emp_id)->get();

        foreach ($tools as $tool) {
            $handover = Logs::where('description', 'like', '%Eszköz kiadás '.$tool->serial.'%')->orWhere('description', 'like', '%Changed owner on serial '.$tool->serial.'%')->orderBy('created_at','desc')->get()->first();;
            if ( !$handover) {$date="";} else {$date=$handover->created_at;}

            if ($tool->serial == "") {$date="";}
            $data[] = $tool->description;
            $data[] = $tool->serial;
            $data[] = $date;
            $data[] = 'Office';
        }

        $header = array('Eszköz neve:', 'Szériaszáma', 'Felhasználás kezdete', 'Helye');

        $dataURI = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAA8YAAADyCAYAAACPktznAAAD2ElEQVR4nO3ZsQ2AIBCGUXZyTBzhKNmGnbAgDqAWBHkv+fpr/1xKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADwyHmMAAAAYEu5jwAAAGA79yjObfYlAAAAMIFvMQAAAMB3EVFLKV2SJEmSVi4i6ux9xaIMY0mSJEl/yDAGAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHjlAmLbTUzvlUs/AAAAAElFTkSuQmCC";
        $fpdf->FancyTable($employee, $networklogonname, $header, $data, $dataURI);
        $pdfContent = $fpdf->Output('', "S");

        return view('Inventory.document', compact('title'), ['pdfContent' => base64_encode($pdfContent)]);

    }

    public function logs($serial)
    {
        $logs = Logs::orderBy('created_at', 'desc')->where('description', 'like', '%'.$serial.'%')->get();
        return response()->json($logs);

    }

    public function excelupload(Request $request)
    {
        $this->store($request);
        return "OK";
    }

    public function getsoftwarelist()
    {
        $softwares = Softwares::orderBy('created_at', 'desc')->where('inventory_id', '=', '1')->get();
        return response()->json($softwares);

    }

    public function getseriallist(Request $request)
    {
        $serials = Softwares::orderBy('created_at', 'desc')->where('inventory_id', '=', '1')->where('description', '=', $request->description)->get();
        return response()->json($serials);

    }

    public function assignsoftware(Request $request)
    {
        $newrecord = Softwares::where('description', '=', $request->description)->where('serial', '=', $request->serial)->firstOrFail();
        $newrecord->inventory_id = $request->inventory_id;
        $newrecord->save();
        $record = Softwares::with('device')->findOrFail($newrecord->id);
        $record->networklogonname = $record->device->owner->networklogonname;
        $record->deviceserial = $record->device->serial;

        /*****************
         * Log info gather
         */

        $log=$record->description." with key ".$record->serial." has been assigned to inventory serial ".$record->device->serial." \n";


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

        return response()->json($record);
    }

    public function unassignsoftware(Request $request)
    {
        $newrecord = Softwares::findOrFail($request->id);
        $newrecord->inventory_id = '1';
        $newrecord->save();
        $record = Softwares::with('device')->findOrFail($request->id);
        $record->networklogonname = $record->device->owner->networklogonname;
        $record->deviceserial = $record->device->serial;

        /*****************
         * Log info gather
         */

        $log=$record->description." with key ".$record->serial." has been unassigned from inventory serial ".$request->inventory_serial." \n";


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

        return response()->json($record);
    }
}
