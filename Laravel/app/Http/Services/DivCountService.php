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
        $getDivCounts = DivCount::paginate();

        $divCounts = DivCountResource::collection($getDivCounts);

        return $divCounts;
    }

    /*
     * Store Div Counts */
    public function store($request)
    {
        $divCount = new DivCount;
        $divCount->url = $request->url;
        $divCount->count = $request->coiunt;
        $saved = $divCount->save();
        $message = "Div count saved";

		return [$saved, $message, $divCount];
    }
}
