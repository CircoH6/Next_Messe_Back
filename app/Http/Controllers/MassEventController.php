<?php

namespace App\Http\Controllers;

use App\Models\Church;
use App\Models\MassEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MassEventController extends Controller
{
    public function index()
    {
        $massEvents = MassEvent::get();
        if ($massEvents->count() > 0) {
            return response()->json([
                'status' => 'succès',
                'message' => 'Liste des messes extraordinaires récupérée avec succès',
                'data' => $massEvents
            ], 200);
        }

        return response()->json([
            'status' => 'échec',
            'message' => 'Aucune messe extraordinaire trouvée'
        ], 404);
    }

    public function store(Request $request, $church_id)
    {
        $validator = Validator::make($request->all(), [
            'mass_events' => 'required|array',
            'mass_events.*.date' => 'required|date',
            'mass_events.*.time' => 'required',
            'mass_events.*.status' => 'required|string',
            'mass_events.*.note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'échec',
                'message' => 'Données invalides',
                'data' => $validator->errors()
            ], 400);
        }

        $church = Church::find($church_id);

        if (!$church) {
            return response([
                'status' => 'échec',
                'message' => 'Église introuvable',
            ], 400);
        }

        $data = $validator->validates();

        foreach ($data['mass_events'] as $massEvent) {
            MassEvent::create([
                'church_id' => $church->id,
                'date' => $massEvent['date'],
                'time' => $massEvent['time'],
                'status' => $massEvent['status'],
                'note' => $massEvent['note']
            ]);
        }

        return response()->json([
            'status' => 'succès',
            'message' => 'Messe extrordinaire ajoutée avec succès',
            'data' => $church->load('massEvents')
        ], 200);
    }

    public function show($id) {
        $massEvent = MassEvent::find($id);

        if ($massEvent) {
            return response([
                'status' => 'succès',
                'data' => $massEvent
            ],200);
        }

        return response([
            'status' => 'échec',
            'message' => 'messe introuvable'
        ],400);
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'mass_events' => 'sometimes|array',
            'date' => 'sometimes|date',
            'time' => 'sometimes',
            'status' => 'sometimes|string',
            'note' => 'sometimes|string',
        ]);

        if(!$validator){
            return response([
                'status' => 'échec',
                'errors' => $validator->errors()
            ],400);
        }

        $massEvent = MassEvent::find($id);
        if(!$massEvent){
            return response()->json([
                'status' => 'échec',
                'message' => 'Aucune messe extraordinaire trouvée'
            ],404);
        }

        $massEvent->update($request->only(['mass_events', 'date', 'time', 'status', 'note']));

        if ($massEvent) {
            return response([
                'status' => 'succès',
                'message' => 'Messe extraordinaire mise à jour avec succès',
                'data' => $massEvent
            ],200);
        }
    }

    public function destroy(string $id)
    {
        $massEvent = MassEvent::find($id);

        if ($massEvent && $massEvent->delete()) {
            return response()->json([
                'status' => 'succès',
                'message' => 'messe extraordinaire supprimée avec succès.'
            ], 200);
        }

        return response()->json([
            'status' => 'échec',
            'message' => 'messe extraordinaire introuvable.'
        ], 404);
    }
}
