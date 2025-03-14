<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Place;
use App\Policies;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use Illuminate\Validation\Rule;
use App\Policies\CharacterPolicy;


class CharacterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::user()) {
            return redirect()->route('login');
        }
        $toSend = ['characters' => Auth::user()->characters];
        if (Auth::user()->admin) {
            $toSend['characters'] = $toSend['characters']->merge(Character::where('enemy', true)->get());
        }

        return view('characters.index', $toSend);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()) {
            return redirect()->route('login');
        }
        return view('characters.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()) {
            return abort(403);
        }
        $validated = $request->validate([
            'name' => 'required|string|unique:App\Models\Character,name',
            'defence' => 'required|integer|min:0|max:3',
            'strength' => 'required|integer|min:0|max:20',
            'accuracy' => 'required|integer|min:0|max:20',
            'magic' => 'required|integer|min:0|max:20',
        ]);

        if (isset($request->enemy)) {
            $validated['enemy'] = true;
        } else {
            $validated['enemy'] = false;
        }


        $totalPoints = $request->defence + $request->strength + $request->accuracy + $request->magic;
        if ($totalPoints != 20) {
            return back()->withInput()->withErrors(['totalError' => 'The sum of the skill points should be 20!']);
        }

        $c = Character::create($validated);
        $c->user()->associate(Auth::user())->save();

        Session::flash('success', 'Character created successfully!');

        return redirect()->route('characters.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Character $character)
    {
        if (!Auth::user() || (!Auth::user()->admin && Auth::id() != $character->user_id) || (Auth::user()->admin && Auth::id() != $character->user_id && !$character->enemy)) {
            return abort(403);
        }
        return view('characters.show', [
            'character' => $character,
            'contests' => $character->contests(),
            'areThereEnemies' => Character::where('enemy', true)->count() != 0,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Character $character)
    {
        if (!Auth::user() || (!Auth::user()->admin && Auth::id() != $character->user_id) || (Auth::user()->admin && Auth::id() != $character->user_id && !$character->enemy)) {
            return abort(403);
        }
        return view('characters.edit', ['character' => $character]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Character $character)
    {
        if (!Auth::user() || (!Auth::user()->admin && Auth::id() != $character->user_id) || (Auth::user()->admin && Auth::id() != $character->user_id && !$character->enemy)) {
            return abort(403);
        }
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('characters', 'name')->ignore($character->id),
            ],
            'defence' => 'required|integer|min:0|max:3',
            'strength' => 'required|integer|min:0|max:20',
            'accuracy' => 'required|integer|min:0|max:20',
            'magic' => 'required|integer|min:0|max:20',
        ]);


        $totalPoints = $request->defence + $request->strength + $request->accuracy + $request->magic;
        if ($totalPoints != 20) {
            return back()->withInput()->withErrors(['totalError' => 'The sum of the skill points should be 20!']);
        }

        $character->update($validated);

        Session::flash('success', 'Character updated successfully!');

        return redirect()->route('characters.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Character $character)
    {
        if ((!Auth::user()) || (!Auth::user()->admin && Auth::user() != $character->user) || (Auth::user()->admin && Auth::user() != $character->user && !$character->enemy)) {
            return abort(403);
        }

        $contests = $character->contests;
        foreach ($contests as $contest) {
            $contest->delete();
        }

        $character->delete();
        Session::flash('success', 'Character deleted successfully!');

        return redirect()->route('characters.index');
    }
}
