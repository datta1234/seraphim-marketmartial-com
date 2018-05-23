<?php

namespace App\Http\Controllers\Auth;

use App\Models\UserManagement\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Models\UserManagement\Organisation;
use App\Models\StructureItems\Market;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
           'markets.required' => 'Please select at least one of the listed markets',
        ];
        return Validator::make($data, [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'cell_phone' => 'required|numeric',
            'role' => 'required',
            'markets' => 'required',
            'organisation' => 'required_without:new_organistation',
            'new_organistation' => 'required_without:organisation|string|max:255'
        ], $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\UserManagement\User
     */
    protected function create(array $data)
    {   
        $roles = config('marketmartial.registration_role');
        if( !in_array($data['role'], $roles) ) {
            return back()->with('error', 'Incorrect role selected');
        }
        $role = array_flip($roles)[$data['role']];

        $organisation = '';
        if( array_key_exists('organisation', $data) ) {
            $organisation = $data['organisation'];
        } elseif( array_key_exists('new_organistation', $data) ) {
            $organisation = Organisation::create([
                'title' => $data['new_organistation'],
                'verified' => false,
            ])->id;
        } else {
            return back()->with('error', 'a Problem occured with your organisation selection, please try again');
        }

        $user = User::create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role_id' => $role,
            'organisation_id' => $organisation,
            'cell_phone' => $data['cell_phone'],
            'active' => false,
            'tc_accepted' => false,
        ]);

        $user->marketInterests()->sync($data['markets']);
        
        return $user;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        $markets = Market::all()->pluck('title', 'id');
        $organisations = Organisation::all()->pluck('title','id');
        return view('auth.register')->with(compact('organisations', 'markets'));
    }
}
