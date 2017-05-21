<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Hash;
use App\Events\UserRestored;
use Illuminate\Support\Facades\Event;

use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use Auth;

class UserController extends Controller
{
    function __construct()
    {
        //$this->middleware(['permission:role-edit','permission:role-delete']);
    }
    
    public function changePassword(Request $request){
        
        $validationRules = [
            'old_password' => 'required|min:6',
            'new_password' => 'required|min:6'
        ];
            
        $this->validate($request, $validationRules);
        
        $user = User::find(Auth::user()->id);
        
        if (!empty($user))
        {
            if (!Hash::check($request->old_password, $user->password))
            {
                return redirect()->back()->with('fail', 'Old password could not be matched, new password not updated!');
            }
            
            $user->password = bcrypt($request->new_password);
            $user->save();
            
            return redirect()->back()->with('success', 'Password successfully updated!');
        }
        
        return redirect()->back()->with('fail', 'Password not updated!');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$users = User::all();
        $users = User::withTrashed()->get();
        $allRoles = Role::all();
        return view('user.index',compact(['users','allRoles']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        $allRoles = Role::all();
        return view('user.create',compact(['users','allRoles']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->merge(['password' => Hash::make('passwords')]);

        $user = User::create($request->except(['roles', '_token']));

        foreach($request->roles as $key => $value)
        {
            $user->attachRole($value);
        }
        
        return redirect()->route('user.index')->withMessage('User Created.');
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
        $user = User::withTrashed()->find($id);
        $roles = $request->roles;
        DB::table('role_user')->where('user_id',$id)->delete();

        foreach ($roles as $role){
            $user->attachRole($role);
        }

        return back()->withMessage('Updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($id == '1')
        {
            return redirect()->route('user.index')->with('fail', 'Super Admin account can\'t be deleted!');
        }
        
        $user = User::find($id);
        $user->destroy($id);
        
        return back()->with('success', 'User Deleted!');
    }
    
    public function restoreUser($id)
    {
        $user = User::withTrashed()->find($id);
        
        if ($user->trashed()) {
            $user->restore();
        }
        
        DB::table('role_user')->where('user_id',$user->id)->delete();
        $user->attachRole(Role::where('name', 'freelancer')->first());
        
        Event::fire(new UserRestored($user));
        
        return redirect()->route('user.index')->with('success', 'User Activated.');
    }
    
    public function resetUserPassword($id)
    {
        $user = User::find($id);
        
        if (!empty($user))
        {
            return view('user.password_reset_user', compact('user'));
        }
        
        return redirect()->back()->with('fail', 'User not found.');
        
    }
    
    public function postResetUserPassword(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!empty($user))
        {
            $user->password = bcrypt($request->new_password);
            $user->save();
            
            return redirect()->route('user.index')->with('success', 'Password updated!');
        }
        
        return redirect()->back()->with('fail', 'User not found.');
        
    }
    
    public function sendResetEmail($id)
    {
        $user = User::find($id);
        
        if (!empty($user))
        {
            $credentials = ['email' => $user->email];
            $response = Password::sendResetLink($credentials, function (Message $message) {
                $message->subject('Password Reset');
            });
    
            switch ($response) {
                case Password::RESET_LINK_SENT:
                    return redirect()->back()->with('success', 'Password reset email has been sent to the user.');
                case Password::INVALID_USER:
                    return redirect()->back()->with('fail', 'User not validated, email not sent.');
            }    
        }
        
        return redirect()->back()->with('fail', 'User not found.');
    }
}
