<?php

namespace App\Http\Controllers;

use App\Models\Church;
use App\Models\ReccuringMass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReccuringMassController extends Controller
{
    public function index()
    {
        $recurringMasses = ReccuringMass::get();

        if ($recurringMasses->count() > 0) {
            return response()->json([
                'status' => 'succès',
                'message' => 'Liste des messes réccurentes récupérée avec succès.',
                'data' => $recurringMasses
            ], 200);
        }

        return response()->json([
            'status' => 'échec',
            'message' => 'Aucune messe trouvée.'
        ], 404);
    }

    public function store(Request $request, $church_id)
    {
        $validator = Validator::make($request->all(), [
            'reccuring_masses' => 'required|array',
            'reccuring_masses.*.day_of_week' => 'required|array',
            'reccuring_masses.*.time' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'échec',
                'message' => 'Données invalides.',
                'errors' => $validator->errors()
            ], 400);
        }

        $church = Church::find($church_id);
        if (!$church) {
            return response([
                'status' => 'échec',
                'message' => 'église introuvable'
            ], 400);
        }

        $data = $validator->validates();

        foreach ($data['reccuring_masses'] as $mass) {
            ReccuringMass::create([
                'church_id' => $church->id,
                'day_of_week' => $mass['day_of_week'],
                'time' => $mass['time']
            ]);
        }

        return response()->json([
            'status' => 'succès',
            'message' => 'messe créée avec succès.',
            'data' => $church->load('recurringMasses')
        ], 201);
    }

    public function show(string $id)
    {
        $reccuringMass = ReccuringMass::find($id);

        if ($reccuringMass) {
            return response()->json([
                'status' => 'succès',
                'data' => $reccuringMass
            ], 200);
        }

        return response()->json([
            'status' => 'échec',
            'message' => 'messe introuvable.'
        ], 404);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'day_of_week' => 'sometimes|array',
            'time' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'échec',
                'errors' => $validator->errors()
            ], 400);
        }

        $reccuringMass = ReccuringMass::find($id);

        if (!$reccuringMass) {
            return response()->json([
                'status' => 'échec',
                'message' => 'messe introuvable.'
            ], 404);
        }

        $reccuringMass->update($request->only(['days_of_week', 'time']));

        return response()->json([
            'status' => 'succès',
            'message' => 'messe mise à jour avec succès.',
            'data' => $reccuringMass
        ], 200);
    }

    public function destroy(string $id)
    {
        $reccuringMass = ReccuringMass::find($id);

        if ($reccuringMass && $reccuringMass->delete()) {
            return response()->json([
                'status' => 'succès',
                'message' => 'messe supprimée avec succès.'
            ], 200);
        }

        return response()->json([
            'status' => 'échec',
            'message' => 'messe introuvable.'
        ], 404);
    }
}
