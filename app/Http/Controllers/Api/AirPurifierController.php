<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchAirPurifierRequest;
use App\Http\Resources\AirPurifierResource;
use App\Models\AirPurifier;
use Illuminate\Http\Request;

final class AirPurifierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchAirPurifierRequest $request)
    {
        $query = AirPurifier::query();

        if ($request->filled('display')) {
            $columns = explode(',', $request->get('display'));
            $query->select($columns);
        }
        if ($request->filled('id')) {
            $ids = explode(',', $request->get('id'));
            $airPurifiers = $request->filled('display')
                ? $query->whereIn('id', $ids)->get()
                : AirPurifier::find($ids);

            return AirPurifierResource::collection($airPurifiers);
        }
        if ($request->boolean('paginate')) {
            return AirPurifierResource::collection($query->paginate());
        }
        if ($request->filled('max')) {
            return AirPurifierResource::collection($query->limit($request->integer('max'))->get());
        }

        return AirPurifierResource::collection($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
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
    public function update(Request $request, string $id): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): void
    {
        //
    }
}
