<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Character;
use App\Models\Place;
use App\Models\Contest;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admins = User::factory(rand(2, 4))->create(['admin' => true]);
        $notAdmins = User::factory(rand(5, 10))->create(['admin' => false]);

        $users = $admins->concat($notAdmins);


        $allyCharacters = Character::factory(rand(10, 15))->create(['enemy' => false]);
        $allyCharacters->each(function ($a) use (&$users) {
            $a->user()->associate($users->random())->save();
        });


        $enemyCharacters = Character::factory(rand(5, 10))->create(['enemy' => true]);
        $enemyCharacters->each(function ($e) use (&$admins) {
            $e->user()->associate($admins->random())->save();
        });


        $contests = Contest::factory(rand(15, 20))->create();
        $places = Place::factory(rand(5, 10))->create();



        $contests->each(function ($c) use (&$places, &$allyCharacters, &$enemyCharacters) {
            $c->place()->associate($places->random())->save();

            $ally = $allyCharacters->random();
            $enemy = $enemyCharacters->random();

            $c->win = rand(0, 1);

            $hp = mt_rand() / mt_getrandmax() * 20;
            $ally->contests()->attach($c, ['hero_hp' => $c->win ? $hp : 0, 'enemy_hp' => $c->win ? 0 : $hp]);
            $enemy->contests()->attach($c, ['hero_hp' => $c->win ? $hp : 0, 'enemy_hp' => $c->win ? 0 : $hp]);

            $c -> user() -> associate($ally->user) -> save();
        });

    }
}
