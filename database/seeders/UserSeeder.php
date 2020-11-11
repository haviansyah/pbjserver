<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserManagerBidang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RoleConst;
use RoleConstId;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            "name" => "Dadang Manager",
            "email" => "managerbid1@gmail.com",
            "role_id" => RoleConstId::MANAGERBIDANG,
            "jabatan_id" => 12,
            "password" => Hash::make("admin789")
        ]);

        $user->managerBidang()->save(new UserManagerBidang(["bidang_id"=>1]));

    }
}
