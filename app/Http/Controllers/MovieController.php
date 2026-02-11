<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = auth()->user()->movies()->latest()->get();
        return view('movies.index', compact('movies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('movies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'watched' => 'boolean',
            'rating' => 'nullable|integer|min:1|max:5',
            'opinion' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $validated;
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('movies', 'public');
        }

        $request->user()->movies()->create($data);

        return redirect(route('movies.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        return view('movies.show', compact('movie'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movie $movie)
    {
        if ($movie->user_id !== auth()->id()) {
            abort(403);
        }
        return view('movies.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie)
    {
        if ($movie->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'watched' => 'boolean',
            'rating' => 'nullable|integer|min:1|max:5',
            'opinion' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $validated;
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('movies', 'public');
        }

        $movie->update($data);

        return redirect(route('movies.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        if ($movie->user_id !== auth()->id()) {
            abort(403);
        }

        $movie->delete();

        return redirect(route('movies.index'));
    }
}
