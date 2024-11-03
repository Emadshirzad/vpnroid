<?php

namespace Database\Seeders;

use App\Models\Channels;
use App\Models\LinkSub;
use App\Models\Service;
use App\Models\Type;
use App\Models\TypeConfig;
use App\Models\User;
use App\Models\WebServiceGet;
use App\Models\WebServicePost;
use Database\Factories\ChannelsFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'username' => 'Test User',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);
        Type::create([
            'name' => 'Link',
        ]);
        Type::create([
            'name' => 'Channel',
        ]);
        Type::create([
            'name' => 'GET',
        ]);
        Type::create([
            'name' => 'POST',
        ]);
        TypeConfig::create([
            'name' => 'VMess',
        ]);
        Service::factory(15)->create();
        Channels::factory(5)->create();
        LinkSub::factory(5)->create();
        TypeConfig::factory(4)->create();
        WebServiceGet::factory(5)->create();
        WebServicePost::factory(5)->create();
    }
}
