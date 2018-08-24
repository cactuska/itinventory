<?php

namespace App\Http\Controllers;

use App\Equtypes;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;

class EqutypeController extends Controller
{
    protected $title='Eszköz típusok';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = $this->title;

        $equtypes = Equtypes::orderBy('id')->get();

        return view('Equtypes.index', compact('title'), ['equtypes' => $equtypes]);
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
        $equtype = new Equtypes();
        $equtype->EquipmentType = $request->EquipmentType;
        $equtype->status = 1;
        $equtype->save();
        return response()->json($equtype);
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
        $equtype = Equtypes::findOrFail($id);
        $equtype->EquipmentType = $request->EquipmentType;
        $equtype->save();
        return response()->json($equtype);
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
        $equtype = Equtypes::findOrFail($id);
        $equtype->delete();

        return response()->json($equtype);
    }

    public function changeStatus()
    {
        $id = Input::get('id');

        $equtype = Equtypes::findOrFail($id);
        $equtype->status = !$equtype->status;
        $equtype->save();

        return response()->json($equtype);
    }
}
