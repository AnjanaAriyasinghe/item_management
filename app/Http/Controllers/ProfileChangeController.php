<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileChangeRequest;
use App\Http\Requests\UpdateProfileChangeRequest;
use App\Models\ProfileChange;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileChangeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::findOrFail(Auth::user()->id);
        return view('pages.admin.change_password.index', ['user' => $user]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProfileChangeRequest $request)
    {
        // dd('here');
        $user = User::findOrFail(Auth::user()->id);

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Current password does not match.', 'status' => false], 405);
        }

        if ($request->old_password == $request->password) {
            return response()->json(['message' => 'Passwords should not be the same !🙃', 'status' => false], 405);
        }

        $user->password = Hash::make($request->password);
        $user->update();

        return response()->json(['message' => 'Your password changed successfully', 'status' => true], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProfileChange $profileChange)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProfileChange $profileChange)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProfileChangeRequest $request, ProfileChange $profileChange)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProfileChange $profileChange)
    {
        //
    }
}
