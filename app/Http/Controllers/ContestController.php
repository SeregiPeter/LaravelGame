<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Character;
use App\Models\Place;
use App\Models\User;

class ContestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $character = Character::find($request->character_id);
        $enemy = Character::where('enemy', true)->where('id', '!=', $character->id)->inRandomOrder()->first();
        $place = Place::inRandomOrder()->first();

        if($place === null) {
            $place = Place::factory()->create();
        }

        if($enemy === null) {
            return abort(400);
        }

        if((!Auth::user()) || Auth::id() != $character->user_id) {
            return abort(403);
        }

        $contest = new Contest();
        $contest->win = null;
        $contest->history = "The contest is created.";
        $contest->place()->associate($place);
        $contest->user()->associate($character->user);
        $contest->save();
        $character->contests()->attach($contest, ['hero_hp' => 20, 'enemy_hp' => 20]);
        $enemy->contests()->attach($contest, ['hero_hp' => 20, 'enemy_hp' => 20]);

        $character_hp = $character->contests()->withPivot('hero_hp', 'enemy_hp')->wherePivot('contest_id', $contest->id)->first()->pivot->hero_hp;
        $enemy_hp = $enemy->contests()->withPivot('hero_hp', 'enemy_hp')->wherePivot('contest_id', $contest->id)->first()->pivot->enemy_hp;
        
        return redirect() -> route('contests.show', ['contest' => $contest]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contest $contest)
    {
        $character = $contest->characters->where('enemy', false)->first();
        $character_hp = $character->contests()->withPivot('hero_hp', 'enemy_hp')->wherePivot('contest_id', $contest->id)->first()->pivot->hero_hp;
        $enemy_hp = $character->contests()->withPivot('hero_hp', 'enemy_hp')->wherePivot('contest_id', $contest->id)->first()->pivot->enemy_hp;
        $enemy = $contest->characters->where('enemy', true)->first();

        if(!Auth::user() || (Auth::id() != $character->user_id && Auth::id() != $enemy->user_id))  {
            return abort(403);
        }

        return view('contests.show', [
            'contest' => $contest,
            'character' => $character,
            'character_hp' => $character_hp,
            'enemy' => $enemy,
            'enemy_hp' => $enemy_hp,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contest $contest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contest $contest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contest $contest)
    {
        //
    }

    public function attack($id, $attackType) {
        $contest = Contest::find($id);
        if($contest->win !== null) {
            return redirect() -> route('contests.show', ['contest' => $contest]);
        }
        $character = $contest->characters->where('enemy', false)->first();
        $enemy = $contest->characters->where('enemy', true)->first();

        $character_hp = $character->contests()->withPivot('hero_hp', 'enemy_hp')->wherePivot('contest_id', $contest->id)->first()->pivot->hero_hp;
        $enemy_hp = $enemy->contests()->withPivot('hero_hp', 'enemy_hp')->wherePivot('contest_id', $contest->id)->first()->pivot->enemy_hp;

        $damage = $this->calculateDamage($attackType, $character, $enemy);

        $newEnemyHp = ($enemy_hp - $damage);

        $updatedHistory = $contest->history . "\n\n" . $character->name . " : " . $attackType . " : " . $damage;
        $contest->history = $updatedHistory;
        $contest->save();

        $contest->characters()->updateExistingPivot($character->id, ['enemy_hp' => ($newEnemyHp >= 0 ? $newEnemyHp :0)]);
        $contest->characters()->updateExistingPivot($enemy->id, ['enemy_hp' => ($newEnemyHp >= 0 ? $newEnemyHp :0)]);

        if($newEnemyHp <= 0) {
            $contest->win = true;
            $contest->save();
            return redirect() -> route('contests.show', ['contest' => $contest]);
        }

        $attackTypes = ['melee', 'ranged', 'special'];
        $randomAttack = $attackTypes[array_rand($attackTypes)];

        $damage = $this->calculateDamage($randomAttack, $enemy, $character);
        $newCharacterHp = $character_hp - $damage;

        $updatedHistory = $contest->history . "\n\n" . $enemy->name . " : " . $randomAttack . " : " . $damage;
        $contest->history = $updatedHistory;
        $contest->save();

        $contest->characters()->updateExistingPivot($character->id, ['hero_hp' => ($newCharacterHp >= 0 ? $newCharacterHp : 0)]);
        $contest->characters()->updateExistingPivot($enemy->id, ['hero_hp' => ($newCharacterHp >= 0 ? $newCharacterHp : 0)]);

        if($newCharacterHp <= 0) {
            $contest->win = false;
            $contest->save();
            return redirect() -> route('contests.show', ['contest' => $contest]);
        }


        return redirect() -> route('contests.show', ['contest' => $contest]);
    }

    public function calculateDamage($attackType, Character $attacker, Character $defender) {
        /* Melee: (DEF.HP) - ((ATT.STRENGTH * 0.7 + ATT.ACCURACY * 0.1 + ATT.MAGIC * 0.1) - DEF.DEFENCE)
        Ranged: (DEF.HP) - ((ATT.STRENGTH * 0.1 + ATT.ACCURACY * 0.7 + ATT.MAGIC * 0.1) - DEF.DEFENCE)
        Special (magic): (DEF.HP) - ((ATT.STRENGTH * 0.1 + ATT.ACCURACY * 0.1 + ATT.MAGIC * 0.7) - DEF.DEFENCE)
        */
        $damage = 0;
        switch($attackType) {
            case 'melee':
                $damage = ($attacker->strength * 0.7 + $attacker->accuracy * 0.1 + $attacker->magic * 0.1) - $defender->defence;
                break;
            case 'ranged':
                $damage = ($attacker->strength * 0.1 + $attacker->accuracy * 0.7 + $attacker->magic * 0.1) - $defender->defence;
                break;
            case 'special':
                $damage = ($attacker->strength * 0.7 + $attacker->accuracy * 0.1 + $attacker->magic * 0.1) - $defender->defence;
                break;
        }
        return max(0,$damage);
    }
}
