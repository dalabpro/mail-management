<?php

namespace Dalab\MailManagement\Database\Seed;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run( )
    {
        $this->call(MailManagementTableSeeder::class);
    }
}
