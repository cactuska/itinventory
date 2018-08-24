<?php

namespace App\Http\Controllers;

use App\Employees;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;

class EmployeeController extends Controller
{
    protected $title='Munkavállalók';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = $this->title;

        $employees = Employees::orderBy('networklogonname')->get();

        return view('Employees.index', compact('title'), ['employees' => $employees]);
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
        $employee = new Employees();
        $employee->code = $request->code;
        $employee->firstname = $request->firstname;
        $employee->lastname = $request->lastname;
        $employee->networklogonname = $request->networklogonname;
        $employee->status = 1;
        $employee->save();
        return response()->json($employee);
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
        $employee = Employees::findOrFail($id);
        $employee->code = $request->code;
        $employee->firstname = $request->firstname;
        $employee->lastname = $request->lastname;
        $employee->networklogonname = $request->networklogonname;
        $employee->save();
        return response()->json($employee);
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
        $employee = Employees::findOrFail($id);
        $employee->delete();

        return response()->json($employee);
    }

    public function changeStatus()
    {
        $id = Input::get('id');

        $employee = Employees::findOrFail($id);
        $employee->status = !$employee->status;
        $employee->save();

        return response()->json($employee);
    }
}
