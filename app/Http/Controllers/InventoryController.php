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
use App\Personal_inventory;
use App\Returndoc;
use App\Sites;

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
        $logs->user = Auth::user()->name;
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

        if ($whom==0){
            $mail = "Selejtezés: \n\n";
            $subject = "Scrapping";
        } else {
            $mail = $newowner->lastname . " " . $newowner->firstname . " részére a következő IT eszköz lett kiadva: \n\n";
            $subject = "Device handover";
        }

        foreach ($datas as $data){
            $record = Inventory::with('owner')->findOrFail($data);
            $record->employee = $whom;

            $mail.="Típus: ".$record->description." \n";
            $mail.="S/N: ".$record->serial." \n\n";

            /****************
             * Do the Logging
             */

            $original=$record->getOriginal();
            $log="Changed owner on serial ".$record->serial." \n";

            $old = Employees::findOrFail($original['employee']);
            $log .= $old->networklogonname." -> ". $newowner->networklogonname." \n";

            $logs = new Logs();
            $logs->user = Auth::user()->name;
            $logs->description = $log;
            $logs->save();

            /*****************
             * Save the change
             */

            $record->save();

        }

        $mail.="Üdvözlettel: \n";
        $mail.="IT Osztály \n";
        $mail.="Rögzítette: ".Auth::user()->name." \n";

        /*******************
         * Send notification
         */

        Mail::raw($mail, function($message) use ($subject) {
            $message->sender('inventory@it.com','IT Department')->subject($subject)->to('daniel.posztos@fiege.com');
        });

    }

    public function takeback()
    {
        $datas = json_decode(stripslashes($_POST['data']), true);
        $mail="";

        foreach ($datas as $data){
            $record = Inventory::with('owner')->findOrFail($data);
            $record->employee = 158;

            $oldowner = $record->owner->lastname." ".$record->owner->firstname;
            if ($mail=="") {$mail=$oldowner."-tól a következő IT eszközök lettek visszavéve:\n\n";}

            $mail.="Típus: ".$record->description." \n";
            $mail.="S/N: ".$record->serial." \n\n";

            /****************
             * Do the Logging
             */

            $log="Changed owner on serial ".$record->serial." \n";

            $log .= $oldowner." -> IT osztály \n";

            $logs = new Logs();
            $logs->user = Auth::user()->name;
            $logs->description = $log;
            $logs->save();

            /*****************
             * Save the change
             */

            $record->save();

        }

        $mail.="Üdvözlettel: \n";
        $mail.="IT Osztály \n";
        $mail.="Rögzítette: ".Auth::user()->name." \n";

        /*******************
         * Send notification
         */

        Mail::raw($mail, function($message)
        {
            $message->sender('inventory@it.com','IT Department')->subject('Device take back')->to('daniel.posztos@fiege.com');
        });

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
        $responsible = Employees::where('networklogonname',Auth::user()->name)->get();
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
//            if ( !$handover) {$date="";} else {$date=$handover->created_at;}
            if ( !$handover) {$date="";} else {$date=$handover->created_at;}

            if ($tool->serial == "") {$date="";}
            $data[] = $tool->description;
            $data[] = $tool->serial;
            $data[] = $date;
            $data[] = 'Office';
        }

        $header = array('Eszköz neve:', 'Szériaszáma', 'Felhasználás kezdete', 'Helye');
        $fpdf->FancyTable($employee, $networklogonname, $header, $data);

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

    public function logs($serial)
    {
        $logs = Logs::orderBy('created_at', 'desc')->where('description', 'like', '%'.$serial.'%')->get();
        return response()->json($logs);

    }
}
