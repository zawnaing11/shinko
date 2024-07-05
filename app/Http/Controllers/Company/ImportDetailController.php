<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Import;
use App\Models\ImportDetail;
use Illuminate\Http\Request;

class ImportDetailController extends Controller
{
    public function index(Request $request, Import $import)
    {
        $import_details = ImportDetail::orderBy('line_number', 'ASC')
            ->where('import_id', $import->id);

        if ($request->filled('result')) {
            $import_details->where('result', $request->result);
        }

        $import_details = $import_details->paginate(config('const.import_details.default_paginate_number'));

        return view('company.import_details.index', [
            'import_details' => $import_details,
            'import' => $import
        ]);
    }
}
