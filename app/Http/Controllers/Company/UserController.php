<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\UserStoreRequest;
use App\Http\Requests\Company\UserUpdateRequest;
use App\Http\Requests\CsvUploadRequest;
use App\Jobs\UserImportJob;
use App\Models\Import;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $headers = [
            'Content-Type' => 'application/octet-stream',
        ];

        $file_name = 'ユーザー' . Carbon::now()->format('YmdHis') . '.csv';

        $callback = function () {

            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                '削除（1=削除）',
                'Eメールアドレス',
                'パスワード',
                '氏名',
                '退職日'
            ]);

            User::orderBy('updated_at', 'DESC')
                ->where('company_id', auth()->user()->company_id)
                ->chunk(1000, function ($users) use ($handle) {
                    foreach ($users as $user) {
                        $values = [
                            'flag' => '',
                            'email' => $user->email,
                            'password' => '',
                            'name' => $user->name,
                            'retirement_date' => $user->retirement_date?->format('Y-m-d'),
                        ];
                        fputcsv($handle, $values);
                    }
                });
            fclose($handle);
        };

        return response()->streamDownload($callback, $file_name, $headers);
    }

    public function upload(CsvUploadRequest $request)
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
                $file_path = Storage::putFileAs(config('const.imports.csv_file_path') . 'users', $import_file, $new_file_name);

                dispatch(new UserImportJob($import, $file_path))
                    ->onQueue('import');
            });
        } catch (Exception $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);
            return back()
                ->with('alert.error', 'CSVアップロードに失敗しました。')
                ->withInput();
        }

        return redirect()
            ->back()
            ->with('alert.success', 'CSVアップロード受け付けました。');
    }
}
