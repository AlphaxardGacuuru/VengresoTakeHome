<?php

namespace App\Http\Controllers;

use App\Events\DivCountSavedEvent;
use App\Http\Services\DivCountService;
use App\Models\DivCount;
use Illuminate\Http\Request;

class DivCountController extends Controller
{
    public function __construct(protected DivCountService $service)
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->service->index();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            "url" => "required",
            "count" => "required",
        ]);

        [$saved, $message, $divCount] = $this->service->store($request);

        DivCountSavedEvent::dispatchIf($saved, $divCount);

        return response([
            "status" => $saved,
            "message" => $message,
            "data" => $divCount,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DivCount  $divCount
     * @return \Illuminate\Http\Response
     */
    public function show(DivCount $divCount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DivCount  $divCount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DivCount $divCount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DivCount  $divCount
     * @return \Illuminate\Http\Response
     */
    public function destroy(DivCount $divCount)
    {
        //
    }

	/*
	* Show Dashboard Data
	*/ 
	public function dashboard()
	{
		return $this->service->dashboard();
	}
}
