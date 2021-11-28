<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        User::create([
            'id' => 1,
            'name' => 'Great God Above',
            'email' => 'admin@2local.com',
            'password' => bcrypt('123qwe'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
