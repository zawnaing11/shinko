<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Import;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function index(Request $request)
    {
        $imports = Import::with('details')
            ->withCount([
                'details as total_count',
                'details as success_count' => function ($query) {
                    $query->where('result', 1);
                },
                'details as fail_count' => function ($query) {
                    $query->where('result', 0);
                }
            ])
            ->orderBy('created_at', 'DESC');

        if ($request->filled('file_name')) {
            $imports->where('file_name', 'like', '%' . $request->file_name . '%');
        }
        if ($request->filled('status')) {
            $imports->where('status', $request->status);
        }

        $imports = $imports->paginate(config('const.default_paginate_number'));

        return view('company.imports.index', [
            'imports' => $imports
        ]);
    }
}
