<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use Carbon\Carbon;

class EventoController extends Controller
{
    public function index()
    {
        return response()->json(Evento::all());
    }

    public function show($id)
    {
        $evento = Evento::find($id);

        if (!$evento) {
            return response()->json(['error' => 'Evento no encontrado'], 404);
        }

        return response()->json([
            'title' => $evento->title,
            'location' => $evento->location,
            'all_day' => $evento->all_day,
            'start' => $evento->start,
            'end' => $evento->end,
            'repeat' => $evento->repeat,
            'repeat_end' => $evento->repeat_end,
            'guests' => json_decode($evento->guests, true),
            'alert' => $evento->alert,
            'url' => $evento->url,
            'notes' => $evento->notes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date|after_or_equal:start',
            'all_day' => 'required|boolean',
            'location' => 'nullable|string|max:255',
            'repeat' => 'nullable|string',
            'repeat_end' => 'nullable|date',
            'guests' => 'nullable|array',
            'alert' => 'nullable|string',
            'url' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        $evento = Evento::create([
            'title' => $validated['title'],
            'start' => Carbon::parse($validated['start']),
            'end' => isset($validated['end']) ? Carbon::parse($validated['end']) : null,
            'all_day' => $validated['all_day'],
            'location' => $validated['location'] ?? null,
            'repeat' => $validated['repeat'] ?? null,
            'repeat_end' => $validated['repeat_end'] ?? null,
            'guests' => isset($validated['guests']) ? json_encode($validated['guests']) : null,
            'alert' => $validated['alert'] ?? null,
            'url' => $validated['url'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json(['success' => true, 'evento' => $evento], 201);
    }

    public function update($id, Request $request)
    {
        $evento = Evento::find($id);

        if (!$evento) {
            return response()->json(['error' => 'Evento no encontrado'], 404);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start',
            'location' => 'nullable|string|max:255',
            'all_day' => 'nullable|boolean',
            'repeat' => 'nullable|string',
            'repeat_end' => 'nullable|date',
            'guests' => 'nullable|array',
            'alert' => 'nullable|string',
            'url' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        if (isset($validated['start'])) {
            $evento->start = Carbon::parse($validated['start']);
        }

        if (isset($validated['end'])) {
            $evento->end = Carbon::parse($validated['end']);
        }

        $evento->fill([
            'title' => $validated['title'] ?? $evento->title,
            'location' => $validated['location'] ?? $evento->location,
            'all_day' => $validated['all_day'] ?? $evento->all_day,
            'repeat' => $validated['repeat'] ?? $evento->repeat,
            'repeat_end' => $validated['repeat_end'] ?? $evento->repeat_end,
            'guests' => isset($validated['guests']) ? json_encode($validated['guests']) : $evento->guests,
            'alert' => $validated['alert'] ?? $evento->alert,
            'url' => $validated['url'] ?? $evento->url,
            'notes' => $validated['notes'] ?? $evento->notes,
        ])->save();

        return response()->json(['success' => true, 'evento' => $evento], 200);
    }

    public function destroy($id)
    {
        $evento = Evento::find($id);

        if (!$evento) {
            return response()->json(['error' => 'Evento no encontrado'], 404);
        }

        $evento->delete();

        return response()->json(['success' => true, 'message' => 'Evento eliminado'], 200);
    }
    // EventoController.php
public function usuarios()
{
    return response()->json(User::select('id', 'name')->get());
}

}
