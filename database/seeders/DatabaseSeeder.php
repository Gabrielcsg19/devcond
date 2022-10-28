<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('units')->insert([
            'name' => 'APT 100',
            'owner_id' => 1
        ]);

        DB::table('units')->insert([
            'name' => 'APT 101',
            'owner_id' => 1
        ]);

        DB::table('units')->insert([
            'name' => 'APT 200',
            'owner_id' => 2
        ]);

        DB::table('units')->insert([
            'name' => 'APT 201',
            'owner_id' => 2
        ]);

        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Academia',
            'cover' => 'gym.jpg',
            'days' => '1,2,4,5',
            'start_time' => '06:00:00',
            'end_time' => '22:00:00'
        ]);

        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Piscina',
            'cover' => 'pool.jpg',
            'days' => '1,2,3,4,5',
            'start_time' => '07:00:00',
            'end_time' => '23:00:00'
        ]);

        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Churrasqueira',
            'cover' => 'barbecue.jpg',
            'days' => '4,5,6',
            'start_time' => '09:00:00',
            'end_time' => '23:00:00'
        ]);

        DB::table('walls')->insert([
            'title' => 'Título de aviso de teste',
            'body' => 'Uma descrição sem sentido só pra encher linguiça',
            'created_at' => '2022-12-20 15:00:00'
        ]);

        DB::table('walls')->insert([
            'title' => 'Alerta geral',
            'body' => 'Outra descrição com alguma coisa sem sentido',
            'created_at' => '2022-12-20 18:00:00'
        ]);
    }
}
