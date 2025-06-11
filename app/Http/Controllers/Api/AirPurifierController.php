<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AirPurifierResource;
use App\Models\AirPurifier;
use Illuminate\Http\Request;

class AirPurifierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $airPurifiers = AirPurifier::query();

        if($request->has('all')) {
            $airPurifiers->all();
        }

        if($request->has('paginate')) {
            $airPurifiers->paginate();
        }

        if($request->has('max')) {
            $airPurifiers->limit($request->get('max'));
        }

        return AirPurifierResource::collection(
            (AirPurifier::paginate())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AirPurifier $airPurifier): AirPurifierResource
    {
        return new AirPurifierResource($airPurifier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
