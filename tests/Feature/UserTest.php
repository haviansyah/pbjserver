<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateUser()
    {
        $name = "Haviansyah";
        $email = "haviansyah@gmail.com";
        $password = "admin789";
        $jabatan_id = 0;
        $role_id = 0;

        $response = $this->postJson('api/users', ['name' => $name,'email'=>$email,'password'=> $password, 'password_confirm' => $password,'jabatan_id'=>$jabatan_id,"role_id", $role_id]);
        
        $response
            ->assertStatus(201);

        $user = User::where("name",$name)->delete();
    }
}
