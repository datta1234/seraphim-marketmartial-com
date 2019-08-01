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
use App\Rules\MicrosoftPasswordPolicy;


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
    protected $redirectTo = '/my-profile';

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
            'new_organisation.unique' => 'The organisation already exists in the system',
        ];
        
        return Validator::make($data, [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                'string',
                new MicrosoftPasswordPolicy
            ],
            'cell_phone' => 'required|numeric',
            'role_id' => [
                'required',
                Rule::exists('roles','id')->where(function ($query) {
                     $query->where('is_selectable', 1);
                }),
            ],
            'market_types' => 'required',
            'organisation_id' => 'required_without:not_listed',
            'new_organisation' => 'required_with:not_listed|unique:organisations,title|string|max:255',
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
                'organisation_id' => $organisation,
                'cell_phone' => $data['cell_phone'],
                'active' => $active,
                'tc_accepted' => false,
                'verified' => $user_verified,
                'is_invited' => $is_invited,
            ]);
            $user->password = \Hash::make($data['password']); //this is not a fillable field is if it was users could change
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

            // Refactored due to task MM-711
            //$user->marketInterests()->attach($data['market_types']);
            $market_interests = [];
            foreach ($data['market_types'] as $market_type) {
                switch ($market_type) {
                    case 1:
                        $market_interests[] = 1;
                        $market_interests[] = 3;
                        break;
                    case 2: 
                        $market_interests[] = 2;
                        break;
                }
            }
            $user->marketInterests()->attach($market_interests);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return null;
        }

        // @TODO - Check if we need to send an email after registration

        // Notify Admin Of User Creation
        \Slack::postMessage([
            "text"      => "New user registered: ".$user->full_name." (".$user->organisation->title.")",
        ], 'notify');

        return $user;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        // Refactored due to task MM-711
        // $market_types = MarketType::all()->pluck('title', 'id')->toArray();
        $market_types = [1 => "Options", 2 => "Delta One (EFPs, Rolls and EFP Switches)"];
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
