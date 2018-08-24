<?php

namespace App\Http\Controllers;

use App\Sites;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;

class SiteController extends Controller
{
    protected $title='Sites';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = $this->title;
//        return view('Sites.index', compact('title'));

        $sites = Sites::orderBy('compcode')->get();

        return view('Sites.index', compact('title'), ['sites' => $sites]);
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
            $site = new Sites();
            $site->compcode = $request->compcode;
            $site->companyname = $request->companyname;
            $site->zip = $request->zip;
            $site->city = $request->city;
            $site->address = $request->address;
            $site->status = 1;
            $site->save();
            return response()->json($site);
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
        $site = Sites::findOrFail($id);
        $site->compcode = $request->compcode;
        $site->companyname = $request->companyname;
        $site->zip = $request->zip;
        $site->city = $request->city;
        $site->address = $request->address;
        $site->save();
        return response()->json($site);
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
        $site = Sites::findOrFail($id);
        $site->delete();

        return response()->json($site);
    }

    public function changeStatus()
    {
        $id = Input::get('id');

        $site = Sites::findOrFail($id);
        $site->status = !$site->status;
        $site->save();

        return response()->json($site);
    }
}
