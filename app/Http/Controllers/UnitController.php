<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\UnitPeople;
use App\Models\UnitPet;
use App\Models\UnitVehicle;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function getInfo($id) {

        $unit = Unit::with(['peoples', 'vehicles', 'pets'])->find($id);

        if (!$unit) {
            return setErrorResponse('Propriedade inexistente', 404);
        }

        return setSuccessResponse('', [
            'unit' => $unit
        ]);
    }

    public function insertPeople(Request $request, $id) {
        $fields = $request->validate([
            'name' => 'required|string',
            'birthdate' => 'required|date'
        ]);

        $newPerson = UnitPeople::create([
            'name' => $fields['name'],
            'unit_id' => $id,
            'birthdate' => $fields['birthdate']
        ]);

        return setSuccessResponse('', [
            'person' => $newPerson
        ], 201);
    }

    public function insertVehicle(Request $request, $id) {
        $fields = $request->validate([
            'title' => 'required',
            'color' => 'required',
            'plate' => 'required',
        ]);

        $newVehicle = UnitVehicle::create([
            'title' => $fields['title'],
            'unit_id' => $id,
            'color' => $fields['color'],
            'plate' => $fields['plate'],
        ]);

        return setSuccessResponse('', [
            'vehicle' => $newVehicle
        ], 201);
    }

    public function insertPet(Request $request, $id) {
        $fields = $request->validate([
            'name' => 'required',
            'race' => 'required',
        ]);

        $newPet = UnitPet::create([
            'name' => $fields['name'],
            'unit_id' => $id,
            'race' => $fields['race'],
        ]);

        return setSuccessResponse('', [
            'pet' => $newPet
        ], 201);
    }

    public function deletePeople($unitId, $id) {
        $people = UnitPeople::where('unit_id', $unitId)->find($id);

        if (!$people) {
            return setErrorResponse('Pessoa inexistente', 404);
        }

        $people->delete();

        return setSuccessResponse('Pessoa removida com sucesso!', []);
    }

    public function deleteVehicle($unitId, $id) {
        $vehicle = UnitVehicle::where('unit_id', $unitId)->find($id);

        if (!$vehicle) {
            return setErrorResponse('Veículo inexistente', 404);
        }

        $vehicle->delete();

        return setSuccessResponse('Veículo removido com sucesso!', []);
    }

    public function deletePet($unitId, $id) {
        $pet = UnitPet::where('unit_id', $unitId)->find($id);

        if (!$pet) {
            return setErrorResponse('Pet inexistente', 404);
        }

        $pet->delete();

        return setSuccessResponse('Pet removido com sucesso!', []);
    }
}
