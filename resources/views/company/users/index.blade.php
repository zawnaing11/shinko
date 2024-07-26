@extends('company.layouts.main')
@section('title', 'ユーザー管理')
@section('styles')
<!-- Datepicker css -->
<link href="{{ asset('assets/admin/plugins/datepicker/datepicker.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('widgetbar')
<a href="{{ route('company.users.export') }}" class="btn btn-outline-primary"><i class="ri-chat-download-line align-middle mr-2"></i>EXCELエクスポート</a>
<a href="javascript:void(0)" class="btn btn-outline-primary" data-toggle="modal" data-target="#import_excel"><i class="ri-chat-upload-line align-middle mr-2"></i>EXCELインポート</a>
<a href="{{ route('company.users.create') }}" class="btn btn-outline-primary"><i class="ri-add-line align-middle mr-2"></i>登録</a>
@endsection
@section('content')

<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <form method="GET" class="form-search" action="{{ route('company.users.index') }}" autocomplete="off">
                <div class="card m-b-30" id="search_box">
                    <div class="card-header collapsed" data-toggle="collapse" data-target="#searchCollapse" aria-expanded="false" style="cursor: pointer;">
                        <h5 class="card-title">検索</h5>
                    </div>
                    <div id="searchCollapse" class="collapse">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="email">Eメールアドレス</label>
                                        <input type="text" id="email" name="email" class="form-control" value="{{ request()->email }}" placeholder="Eメールアドレス">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="name">氏名</label>
                                        <input type="text" id="name" name="name" class="form-control" value="{{ request()->name }}" placeholder="氏名">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <div>
                                            <label for="retirement_date">退職</label>
                                        </div>
                                        <div class="form-check-inline">
                                            <div class="form-check form-check-inline">
                                                <input type="radio" id="retirement_date_0" name="retirement_date" class="form-check-input default" value="0" {{ request()->retirement_date == 0 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="retirement_date_0">すべて</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="radio" id="retirement_date_1" name="retirement_date" class="form-check-input" value="1" {{ request()->retirement_date == 1 ? 'checked' : '' }}>
                                                <label for="retirement_date_1" class="form-check-label">未退職者</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="radio" id="retirement_date_2" name="retirement_date" class="form-check-input" value="2" {{ request()->retirement_date == 2 ? 'checked' : '' }}>
                                                <label for="retirement_date_2" class="form-check-label">退職者</label>
                                            </div>
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
                    <h5 class="card-title">ユーザー一覧</h5>
                </div>
                <div class="card-body">

                    @forelse ($users as $user)
                        @if ($loop->first)
                        <div class="table-responsive m-b-30">
                            <table id="posts-table" class="table">
                                <thead class="text-nowrap">
                                    <tr>
                                        <th>Eメールアドレス</th>
                                        <th>氏名</th>
                                        <th>退職日</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                        @endif
                                    <tr>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ Str::limit($user->name, 20) }}</td>
                                        <td>{{ $user->retirement_date?->format('Y-m-d') }}</td>
                                        <td>
                                            <form method="POST" class="form-destroy" action="{{ route('company.users.destroy', $user->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <a href="{{ route('company.users.edit', $user->id) }}" class="btn btn btn-outline-success">
                                                    <i class="feather icon-edit mr-2"></i>編集
                                                </a>
                                                @if ($user->id)
                                                <button type="submit" class="btn btn btn-outline-danger"><i class="feather icon-trash-2 mr-2"></i>削除</button>
                                                @endif
                                            </form>
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
                @if ($users->count() > 0)
                <div class="card-footer clearfix">
                    {{ $users->appends(request()->input())->links('pagination::company') }}
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal fade" id="import_excel" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">EXCELインポート</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('company.users.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <input type="file" name="import_file" class="form-control-file" required accept="{{ implode(',', config('const.accept_excel_extensions')) }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">インポート</button>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<!-- Datepicker JS -->
<script src="{{ asset('assets/admin/plugins/datepicker/datepicker.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/datepicker/i18n/datepicker.ja.js') }}"></script>
<script src="{{ asset('assets/admin/js/datepicker-format.js') }}"></script>
@endsection

