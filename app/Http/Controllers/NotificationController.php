<?php
/**
 * Created by PhpStorm.
 * User: dposztos
 * Date: 2018. 08. 28.
 * Time: 15:25
 */
namespace App\Http\Controllers;

use App\Equtypes;
use App\Notifications;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;

class NotificationController extends Controller
{
    protected $title='Notifications';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = $this->title;

        $addresses = Notifications::orderBy('id')->get();

        return view('Notifications.index', compact('title'), ['addresses' => $addresses]);
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
        $address = new Notifications();
        $address->address = $request->address;
        $address->save();
        return response()->json($address);
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
        $address = Notifications::findOrFail($id);
        $address->address = $request->address;
        $address->save();
        return response()->json($address);
//        var_dump(get_object_vars ($request));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $address = Notifications::findOrFail($id);
        $address->delete();

        return response()->json($address);
    }
}
