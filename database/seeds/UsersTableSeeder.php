<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Nicholas Hoium',
            'email' => 'nhoium@gmail.com',
            'password' => bcrypt('secret'),
            'confirmed' => true,
            'confirmation_token' => str_limit(md5('nhoium@gmail.com' . str_random()), 25, ''),
            'is_admin' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
