<?php

namespace App\Http\Controllers\Auth;

use App\Models\UserManagement\User;
use App\Models\UserManagement\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Models\UserManagement\Organisation;
use App\Models\StructureItems\Market;
use App\Models\StructureItems\MarketType;
use Illuminate\Validation\Rule;

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
            'role_id.required' => 'Please select a role',
            'role_id.exists' => 'Please select a role that is valid',
            'organisation_id.required_without' => 'Please select your organisation.',
            'cell_phone.required' => 'The phone field is required',
        ];


        return Validator::make($data, [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'cell_phone' => 'required|numeric',
            'role_id' => [
                'required',
                Rule::exists('roles','id')->where(function ($query) {
                     $query->where('is_selectable', 1);
                }),
            ],
            'market_types' => 'required',
            'organisation_id' => 'required_without:new_organistation',
            'new_organistation' => 'required_if:organisation_id,null|string|max:255'
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
        $organisation = '';

        if( array_key_exists('organisation_id', $data) ) {
            $organisation = $data['organisation_id'];
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
            'role_id' => $data['role_id'],
            'organisation_id' => $organisation,
            'cell_phone' => $data['cell_phone'],
            'active' => false,
            'tc_accepted' => false,
        ]);

        $user->role_id  = $data['role_id'];//this is not a fillable field is if it was users could change
        
        $markets = Market::where('market_type_id',$data['market_types'])->pluck('id');
        $user->marketInterests()->sync($markets);
        
        return $user;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        $market_types = MarketType::all()->pluck('title', 'id');
        $organisations = Organisation::all()->pluck('title','id');
        $roles = Role::where('is_selectable',true)->pluck('title','id');
        return view('auth.register')->with(compact('organisations', 'market_types','roles'));
    }
}
