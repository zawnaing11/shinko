<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            '削除（1=削除）',
            'ID',
            'Eメールアドレス',
            'パスワード',
            '氏名',
            '退職日'
        ];
    }

    public function collection()
    {
        $users = User::orderBy('updated_at', 'DESC')
            ->where('company_id', auth()->user()->company_id)
            ->get();

        if ($users !== null) {
            foreach ($users as $user) {
                $datas[] = [
                    '',
                    $user->id,
                    $user->email,
                    '',
                    $user->name,
                    $user->retirement_date?->format('Y-m-d'),
                ];
            }
        }

        return collect($datas ?? []);
    }
}
