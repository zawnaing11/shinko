@extends('company.layouts.main')
@section('title', 'Excelインポート管理')
@section('content')

<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <form method="GET" class="form-search" action="{{ route('company.imports.index') }}" autocomplete="off">
                <div class="card m-b-30" id="search_box">
                    <div class="card-header collapsed" data-toggle="collapse" data-target="#searchCollapse" aria-expanded="false" style="cursor: pointer;">
                        <h5 class="card-title">検索</h5>
                    </div>
                    <div id="searchCollapse" class="collapse">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="file_name">ファイル名</label>
                                        <input type="text" id="file_name" name="file_name" class="form-control" value="{{ request()->file_name }}" placeholder="ファイル名">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="status">ステータス</label>
                                        @foreach (config('const.imports.statuses') as $key => $val)
                                            @if ($loop->first)
                                            <select id="status" name="status" class="form-control">
                                                <option value="">選択してください</option>
                                            @endif
                                                <option value="{{ $key }}" @if ($key == request()->status) selected="selected" @endif>{{ $val }}</option>
                                            @if ($loop->last)
                                            </select>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="button" id="reset" class="btn btn-light form-reset">リセット</button>
                            <button type="submit" id="search" class="btn btn-primary">検索</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="card-title">Excelインポート管理</h5>
                </div>
                <div class="card-body">

                    @forelse ($imports as $import)
                        @if ($loop->first)
                        <div class="table-responsive m-b-30">
                            <table id="posts-table" class="table">
                                <thead class="text-nowrap">
                                    <tr>
                                        <th>ファイル名</th>
                                        <th>総件数</th>
                                        <th>成功件数</th>
                                        <th>失敗件数</th>
                                        <th>ステータス</th>
                                        <th>メッセージ</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                        @endif
                                    <tr>
                                        <td>{{ $import->file_name}}</td>
                                        <td>{{ $import->total_count}}</td>
                                        <td>{{ $import->success_count}}</td>
                                        <td>{{ $import->fail_count}}</td>
                                        <td>{{ config('const.imports.statuses.' . $import->status) }}</td>
                                        <td>
                                            @foreach ($import->messages as $message)
                                                <p>{{ $message }}</p>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="{{ route('company.imports.show', $import->id) }}" class="btn btn-info btn-sm @if ($import->status != 3) disabled @endif">詳細</a>
                                        </td>
                                    </tr>
                        @if ($loop->last)
                                </tbody>
                            </table>
                        </div>
                        @endif
                    @empty
                        <p>作成された情報がありません。</p>
                    @endforelse

                </div>
                @if ($imports->count() > 0)
                <div class="card-footer clearfix">
                    {{ $imports->appends(request()->input())->links('pagination::company') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

