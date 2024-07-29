<?php

namespace App\Jobs;

use App\Exceptions\ImportException;
use App\Imports\ProductPriceImport;
use App\Imports\UserImport;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ExcelImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $file_path;
    private $import;
    private $auth_user;

    public function __construct($import, $file_path)
    {
        $this->import = $import;
        $this->file_path = $file_path;
        $this->auth_user = auth()->user();
    }

    public function handle()
    {
        Log::setDefaultDriver('excel_import_' . Str::plural(Str::snake($this->import->model_name)));

        logger()->info('Import Start', $this->import->toArray());

        $this->storeImport(2);

        try {
            // ファイル存在確認
            if (! Storage::exists($this->file_path)) {
                throw new ImportException($this->import->file_name . ' が存在していません。');
            }

            switch ($this->import->model_name) {
                case 'User':
                    Excel::import(new UserImport($this->import->id, $this->auth_user), $this->file_path);
                    break;
                case 'ProductPrice':
                    Excel::import(new ProductPriceImport($this->import->id, $this->auth_user), $this->file_path);
                    break;
            }

            $this->storeImport(3);

        } catch (ImportException $ie) {
            logger()->info('$ie', [$ie->getCode(), $ie->getMessage()]);
            $this->storeImport(10, [$ie->getMessage()]);

        } catch (Exception $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);
            $this->storeImport(10);
        }

        Storage::delete($this->file_path);

        logger()->info('Import End', $this->import->toArray());
    }

    private function storeImport(int $status, array $messages = [])
    {
        $this->import->fill([
            'status' => $status,
            'messages' => $messages
        ])
        ->save();
    }
}
