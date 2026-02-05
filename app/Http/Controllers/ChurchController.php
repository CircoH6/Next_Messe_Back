<?php

namespace App\Http\Controllers;

use App\Models\Church;
use App\Models\ReccuringMass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ChurchController extends Controller
{
    public function index()
    {
        $churches = Church::with('recurringMasses')->get();

        if ($churches->count() > 0) {
            return response()->json([
                'status' => 'succès',
                'message' => 'Liste des églises récupérée avec succès.',
                'data' => $churches
            ], 200);
        }

        return response()->json([
            'status' => 'échec',
            'message' => 'Aucune église trouvée.'
        ], 404);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'reccuring_masses' => 'required|array|min:1',
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

        try {
            $church = DB::transaction(function ($request) {
                $church = Church::create([
                    'name' => $request->name
                ]);

                foreach ($request->reccuring_masses as $mass) {
                    ReccuringMass::create([
                        'church_id' => $church->id,
                        'day_of_week' => $mass['day_of_week'],
                        'time' => $mass['time']
                    ]);
                }

                return $church;
            });

            return response()->json([
                'status' => 'succès',
                'message' => 'Église créée avec succès.',
                'data' => $church->load('recurringMasses')
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'échec',
                'message' => 'Erreur lors de la création',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function show(string $id)
    {
        $church = Church::with('recurringMasses')->find($id);

        if ($church) {
            return response()->json([
                'status' => 'succès',
                'data' => $church
            ], 200);
        }

        return response()->json([
            'status' => 'échec',
            'message' => 'Église introuvable.'
        ], 404);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'échec',
                'errors' => $validator->errors()
            ], 400);
        }

        $church = Church::find($id);

        if (!$church) {
            return response()->json([
                'status' => 'échec',
                'message' => 'Église introuvable.'
            ], 404);
        }

        $church->update($request->only('name'));

        return response()->json([
            'status' => 'succès',
            'message' => 'Église mise à jour avec succès.',
            'data' => $church
        ], 200);
    }

    public function destroy(string $id)
    {
        $church = Church::find($id);

        if ($church && $church->delete()) {
            return response()->json([
                'status' => 'succès',
                'message' => 'Église supprimée avec succès.'
            ], 200);
        }

        return response()->json([
            'status' => 'échec',
            'message' => 'Église introuvable.'
        ], 404);
    }
}
