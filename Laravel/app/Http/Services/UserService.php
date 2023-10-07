<?php

namespace App\Http\Services;

use App\Http\Resources\UserResource;
use App\Models\User;

class UserService extends Service
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getUsers = User::all();

        return UserResource::collection($getUsers);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function auth()
    {
        $auth = auth('sanctum')->user();

        return new UserResource($auth);
    }
}
