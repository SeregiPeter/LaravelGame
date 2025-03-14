<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(!Auth::user() || !Auth::user()->admin) {
            return abort(403);
        }
         return view('places.index', ['places' => Place::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(!Auth::user() || !Auth::user()->admin) {
            return abort(403);
        }
        return view('places.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(!Auth::user() || !Auth::user()->admin) {
            return abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|min:3|max:30|unique:App\Models\Place,name',
            'image' => 'required|image',
        ]);

        if($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = $file -> hashName();
            Storage::disk('public')->put('images/' . $fileName, $file -> get());
            $validated['image'] = $fileName;
        }

        Place::create($validated);
        Session::flash('success', 'Place created successfully!');

        return redirect() -> route('places.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Place $place)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Place $place)
    {
        if(!Auth::user() || !Auth::user()->admin)  {
            return abort(403);
        }

        return view('places.edit', ['place' => $place]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Place $place)
    {
        if(!Auth::user() || !Auth::user()->admin)  {
            return abort(403);
        }
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:3',
                'max:30',
                Rule::unique('places', 'name')->ignore($place->id),
            ],
            'image' => 'image',
        ]);

        if($request->hasFile('image')) {
            if ($place->image) {
                Storage::disk('public')->delete('images/' . $place->image);
            }
            $file = $request->file('image');
            $fileName = $file -> hashName();
            Storage::disk('public')->put('images/' . $fileName, $file -> get());
            $validated['image'] = $fileName;
        }


        $place->update($validated);

        Session::flash('success', 'Place updated successfully!');

        return redirect()->route('places.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Place $place)
    {
        if(!Auth::user() || !Auth::user()->admin)  {
            return abort(403);
        }

        if($place->image !== null) {
            Storage::disk('public')->delete('images/' . $place->image);
        }
        $place->delete();
        Session::flash('success', 'Place deleted successfully!');

        return redirect()->route('places.index');
    }
}
