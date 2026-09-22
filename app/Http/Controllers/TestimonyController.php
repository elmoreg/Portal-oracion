<?php

namespace App\Http\Controllers;

use App\Models\Testimony;
use Illuminate\Http\Request;

class TestimonyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $testimonies = Testimony::where('status', 'approved')
            ->latest()
            ->paginate(15);

        return view('testimonies.index', compact('testimonies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('testimonies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        Testimony::create($validated);

        return redirect()->route('testimonies.index')
            ->with('status', 'Testimonio enviado exitosamente. Pendiente de aprobación.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimony $testimony)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Testimony $testimony)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Testimony $testimony)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimony $testimony)
    {
        //
    }
}
