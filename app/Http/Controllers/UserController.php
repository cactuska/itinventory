<?php
/**
 * Created by PhpStorm.
 * User: dposztos
 * Date: 2018. 08. 29.
 * Time: 8:08
 */

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    protected $title='IT Users';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = $this->title;

        $users = User::orderBy('id')->get();

        foreach ($users as $user){
            if (Auth::user()->name != $user->name){$user->api_token="***";}
        }

        return view('Users.index', compact('title'), ['users' => $users]);
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
        $user = new User();
        $user->username = $request->username;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->api_token = "";
        $user->password = '0';
        $user->save();

        $recipient = $request->email;
        $subject = "Access to IT inventory";
        $mail = $request->name;

        Mail::send( ['html' => 'emails.newuser'], ['text' => $mail], function($message) use ($recipient, $subject)
        {
            $message->to($recipient)->subject($subject);
        });
        return response()->json($user);

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
        $user = User::findOrFail($id);
        $user->username = $request->username;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        return response()->json($user);
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
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json($user);
    }

    public function renew_api($id)
    {
        $user = User::findOrFail($id);
        $user->api_token = str_random(60);
        $user->save();
        return response()->json($user);
    }
}
