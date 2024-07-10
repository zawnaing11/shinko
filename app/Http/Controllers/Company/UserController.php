<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\UserStoreRequest;
use App\Http\Requests\Company\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::orderBy('updated_at', 'DESC');

        if ($request->filled('email')) {
            $users->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('name')) {
            $users->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('retirement_date')) {
            $users->where('retirement_date', '>=', $request->retirement_date);
        }

        $users = $users->paginate(config('const.default_paginate_number'));

        return view('company.users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('company.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['company_id'] = Auth::guard('company')->user()->company_id;

        User::create($validated);

        return redirect()
            ->route('company.users.index')
            ->with('alert.success', 'ユーザーを作成しました。');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('company.users.edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $user->fill($request->validated())->save();

        return redirect()
            ->route('company.users.index')
            ->with('alert.success', 'ユーザーを更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('alert.success', 'ユーザーを削除しました。');
    }
}
