@extends('company.layouts.main')
@section('title', 'CSVインポート詳細')
@section('content')

<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <form method="GET" class="form-search" action="{{ route('company.imports.show', request()->import->id) }}" autocomplete="off">
                <div class="card m-b-30" id="search_box">
                    <div class="card-header collapsed" data-toggle="collapse" data-target="#searchCollapse" aria-expanded="false" style="cursor: pointer;">
                        <h5 class="card-title">検索</h5>
                    </div>
                    <div id="searchCollapse" class="collapse">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <div>
                                            <label for="result">結果</label>
                                        </div>
                                        <div class="form-check-inline">
                                            <div class="form-check form-check-inline">
                                                <input type="radio" id="result" name="result" class="form-check-input default" value="" {{ request()->result == '' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="result">全て</label>
                                            </div>
                                            @foreach (config('const.import_details.results') as $code => $name)
                                            <div class="form-check">
                                                <input type="radio" id="result_{{ $code }}" name="result" class="form-check-input" value="{{ $code }}" {{ request()->result == ''? '' : (request()->result == $code? 'checked' : '') }}>
                                                <label class="form-check-label" for="result_{{ $code }}">{{ $name }}</label>
                                            </div>
                                            @endforeach
                                        </div>
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
                    <h5 class="card-title">CSVインポート詳細</h5>
                </div>
                <div class="card-body">

                    @forelse ($import_details as $detail)
                        @if ($loop->first)
                        <div class="table-responsive m-b-30">
                            <table id="posts-table" class="table">
                                <thead class="text-nowrap">
                                    <tr>
                                        <th>行番号</th>
                                        <th>結果</th>
                                        <th>メッセージ</th>
                                    </tr>
                                </thead>
                                <tbody>
                        @endif
                                    <tr>
                                        <td>{{ number_format($detail->line_number) }}</td>
                                        <td>{{ config('const.import_details.results.' . $detail->result) }}</td>
                                        <td>
                                            @foreach ($detail->messages as $message)
                                                <p>{{ $message }}</p>
                                            @endforeach
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
                @if ($import_details->count() > 0)
                <div class="card-footer clearfix">
                    {{ $import_details->appends(request()->input())->links('pagination::company') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

