<?php

namespace App\Http\Services;

use App\Http\Resources\DivCountResource;
use App\Models\DivCount;

class DivCountService extends Service
{
    /*
     * Fetch All Div Counts*/
    public function index()
    {
        $getDivCounts = DivCount::all()->paginate();

        $divCounts = DivCountResource::collection($getDivCounts);

        return $divCounts;
    }

    /*
     * Store Div Counts */
    public function store($request)
    {
        $divCount = new DivCount;
        $divCount->count = $request->divCount;
        $saved = $divCount->save();
        $message = "Div count saved";

		return [$saved, $message, $divCount];
    }
}
