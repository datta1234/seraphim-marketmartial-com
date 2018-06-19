<?php

namespace Tests\Unit\Models\UserManagment;

use Illuminate\Database\Seeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleTest extends TestCase
{
    use RefreshDatabase;


    private function populateRolesTable(){

    	\DB::table('roles')->insert( config('marketmartial.roles') );

    }

    /**
     * Role Model Type Casting test
     *
     * Not accounting for null values within the casting test.
     * @coversNothing
     * @testdox check App\Models\UserManagement\Role::class casting
     * @group data
     *	 
     * @return void
     */
    public function testRolesAccountCasting()
    {

    	$this->populateRolesTable();
    	$configs = config('marketmartial.roles');
    	$roles = \App\Models\UserManagement\Role::get();

    	$roles->each( function($item,$key) use ($configs) {

    		$config = $configs[$key];

			$this->assertInternalType('int', $item->id);
    		$this->assertInternalType('string', $item->title);
    		$this->assertInternalType('bool', $item->is_selectable);

    		$this->assertNull( 
	    		\Validator::make($item->toArray(), [
					'created_at' => 'date_format:Y-m-d H:i:s',
					'updated_at' => 'date_format:Y-m-d H:i:s'
				])->validate()
    		);

    	}); 	

    }
    
    /**
     * Role has many users relation Test
     *
     * @covers \App\Models\UserManagement\Role::users
     * @testdox Role has many \App\Models\UserManagement\User test
     * @group relations/usermanagment
     *
     * @return void
     */
    public function testRoleHasManyUsers()
    {

    	$this->populateRolesTable();
    	$role = \App\Models\UserManagement\Role::find( rand(1,3) );

		$users = factory( \App\Models\UserManagement\User::class, 5 )->create([
			'role_id' => $role->id
		])->keyBy('id');



		$role->users->keyBy('id')->each( function($item,$key) use ($role, $users) {

			$user = $users[$key];

			$this->assertArrayHasKey('email', $item->toArray());
			$this->assertArrayHasKey('full_name', $item->toArray());
			$this->assertArrayHasKey('cell_phone', $item->toArray());
			$this->assertArrayHasKey('work_phone', $item->toArray());
			$this->assertArrayHasKey('role_id', $item->toArray());
			$this->assertArrayHasKey('organisation_id', $item->toArray());
			$this->assertArrayHasKey('active', $item->toArray());
			$this->assertArrayHasKey('tc_accepted', $item->toArray());
			$this->assertArrayHasKey('birthdate', $item->toArray());
			$this->assertArrayHasKey('is_married', $item->toArray());
			$this->assertArrayHasKey('has_children', $item->toArray());
			$this->assertArrayHasKey('hobbies', $item->toArray());

			$this->assertArrayNotHasKey('remember_token', $item->toArray());
			$this->assertArrayNotHasKey('password', $item->toArray());
			$this->assertArrayNotHasKey('last_login', $item->toArray());

			$this->assertDatabaseHas('roles', [
	    		'id'		=> $role->id,
	            'title'	=> $role->title
	        ]);

	        $this->assertDatabaseMissing('user_market_watched', [
				'user_id' 	=> $user->id,
				'market_id'	=> $item->id
        	]);

	        $this->assertDatabaseHas('users', [
	    		'id'		=> $item->id,
	    		'email'		=> $item->email,
	            'full_name'	=> $item->full_name,
	            'cell_phone'	=> $item->cell_phone,
	            'work_phone'	=> $item->work_phone,
	            'role_id'	=> $role->id,
	            'organisation_id'	=> $item->organisation_id,
	            'active'	=> $item->active,
	            'tc_accepted'	=> $item->tc_accepted,
	            'password'	=> $item->password,
	            'remember_token'	=> $item->remember_token,
	            'birthdate'	=> $item->birthdate,
	            'is_married'	=> $item->is_married,
	            'has_children'	=> $item->has_children,
	            'last_login'	=> $item->last_login,
	            'hobbies'	=> $item->hobbies,
	        ]);



		});


    }
}
