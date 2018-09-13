<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;

use App\Models\UserManagement\User;
use App\Models\UserManagement\Organisation;
use App\Models\UserManagement\Role;
use App\Models\StructureItems\Market;
use App\Models\StructureItems\MarketType;
use App\Models\ApiIntegration\SlackIntegration;


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
            'organisation_id' => 'required_without:not_listed',
            'new_organisation' => 'required_with:not_listed|string|max:255'
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
        $user_verified = false;
        $is_invited = false;
        $active = false;
        $organisation_verified = false;

        // Sets verified and invited to true if admin is creating the user
        if( Auth::check() ) {
            $user_verified = true;
            $active = true;
            $is_invited = true;
            $organisation_verified = true;
        }

        try {
            DB::beginTransaction();
            if( array_key_exists('organisation_id', $data) ) {
                $organisation = $data['organisation_id'];
            } elseif( array_key_exists('new_organisation', $data) ) {
                $organisation = Organisation::create([
                    'title' => $data['new_organisation'],
                    'verified' => $organisation_verified,
                ])->id;
            } else {
                return null;
            }

            $user = new User([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'role_id' => $data['role_id'],
                'organisation_id' => $organisation,
                'cell_phone' => $data['cell_phone'],
                'active' => $active,
                'tc_accepted' => false,
                'verified' => $user_verified,
                'is_invited' => $is_invited,
            ]);
            $user->role_id  = $data['role_id'];//this is not a fillable field is if it was users could change
            $user->save();

            // Create slack channel if new organisation is made by the Admin
            if(array_key_exists('new_organisation', $data) && $organisation_verified) {
                $slack_channel_data = $user->organisation->createChannel($user->organisation->channelName());
                $slack_integration = new SlackIntegration([
                    "type"  => "string",
                    "field" => "channel",
                    "value" => $slack_channel_data->group->id,
                ]);
                $slack_integration->save();
                $user->organisation->slackIntegrations()->attach($slack_integration->id);
            }

            $user->marketInterests()->attach($data['market_types']);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return null;
        }
        return $user;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        $market_types = MarketType::all()->pluck('title', 'id')->toArray();
        $organisations = Organisation::all()->pluck('title','id')->toArray();
        $roles = Role::where('is_selectable',true)->get()->pluck('label','id')->toArray();
        return view('auth.register')->with(compact('organisations', 'market_types','roles'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());
        if(!$user) {
            return redirect()->back()->with('error', 'Failed to register new user.');
        } 
        event(new Registered($user));

        if( Auth::check() ) {
            if( $request->has('btn_add') ) {
                return redirect()->route('admin.user.show', ["user" => $user->id])->with('success', 'User Created');
            }
            return redirect()->route('admin.user.edit', ["user" => $user->id]);
        }
        
        $this->guard()->login($user);

        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }
}
