<?php

namespace App\Http\Controllers\Company;

use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\UserStoreRequest;
use App\Http\Requests\Company\UserUpdateRequest;
use App\Http\Requests\ExcelUploadRequest;
use App\Jobs\ExcelImportJob;
use App\Models\Import;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::orderBy('updated_at', 'DESC')
            ->where('company_id', auth()->user()->company_id);

        if ($request->filled('email')) {
            $users->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('name')) {
            $users->where('name', 'like', '%' . $request->name . '%');
        }
        if (! empty($request->retirement_date)) {
            $today = Carbon::today();
            if ($request->retirement_date == 1) {
                $users->where(function ($q) use ($today) {
                    $q->whereNull('retirement_date')
                        ->orWhere('retirement_date', '>', $today);
                });
            } else {
                $users->where('retirement_date', '<=', $today);
            }
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
        $validated['company_id'] = auth()->user()->company_id;

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

    public function export()
    {
        $file_name = 'ユーザー' . Carbon::now()->format('YmdHis') . '.xlsx';
        return Excel::download(new UserExport(), $file_name);
    }

    public function upload(ExcelUploadRequest $request)
    {
        $import_file = $request->validated()['import_file'];

        try {
            DB::transaction(function () use ($import_file) {
                $import = Import::create([
                    'model_name' => 'User',
                    'file_name' => $import_file->getClientOriginalName(),
                    'status' => 1,
                ]);

                $new_file_name = uniqid() . '.' . $import_file->getClientOriginalExtension();
                $file_path = Storage::putFileAs(config('const.imports.excel_file_path') . 'users', $import_file, $new_file_name);

                dispatch(new ExcelImportJob($import, $file_path))
                    ->onQueue('excel_import');
            });
        } catch (Exception $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);
            return back()
                ->with('alert.error', 'Excelアップロードに失敗しました。')
                ->withInput();
        }

        return redirect()
            ->back()
            ->with('alert.success', 'Excelアップロード受け付けました。');
    }
}
