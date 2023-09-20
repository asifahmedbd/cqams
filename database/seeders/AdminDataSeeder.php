<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class AdminDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert(array(
            array(
             'name' => "Asif Ahmed",
             'username' => "romeoasif",
             'email' => 'asifahmed.mist@gmail.com',
             'password' => bcrypt('superadmin123'),
             'phone' => '01676293452',
             'role' => 'admin',
             'status' => 'active'
            )
        ));
    }
}
