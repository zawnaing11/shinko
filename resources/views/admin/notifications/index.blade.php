@extends('admin.layouts.main')
@section('title', 'お知らせ管理')
@section('styles')
<!-- Datepicker css -->
<link href="{{ asset('assets/admin/plugins/datepicker/datepicker.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('widgetbar')
<a href="{{ route('admin.notifications.create') }}" class="btn btn-outline-primary"><i class="ri-add-line align-middle mr-2"></i>登録</a>
@endsection
@section('content')

<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <form method="GET" class="form-search" action="{{ route('admin.notifications.index') }}" autocomplete="off">
                <div class="card m-b-30" id="search_box">
                    <div class="card-header collapsed" data-toggle="collapse" data-target="#searchCollapse" aria-expanded="false" style="cursor: pointer;">
                        <h5 class="card-title">検索</h5>
                    </div>
                    <div id="searchCollapse" class="collapse">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="title">タイトル</label>
                                        <input type="text" class="form-control" id="title" name="title" value="{{ request()->title }}" placeholder="タイトル">
                                    </div>
                                </div>
                                <div class="col-3">
                                        <div class="form-group">
                                            <div>
                                                <label for="is_active">有効/無効</label>
                                            </div>
                                            <div class="form-check-inline">
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" id="is_active_1" name="is_active" class="form-check-input default" value="" {{ request()->is_active == '' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_active_1">全て</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" id="is_active_2" name="is_active" class="form-check-input" value="1" {{ request()->is_active ? 'checked' : '' }}>
                                                    <label for="is_active_2" class="form-check-label">有効</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" id="is_active_3" name="is_active" class="form-check-input" value="0" {{ request()->is_active == '' ? '' : (request()->is_active == 0 ? 'checked' : '') }}>
                                                    <label for="is_active_3" class="form-check-label">無効</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="publish_from">公開日時</label>
                                        <div class="input-group">
                                            <input type="text" id="publish_from" name="publish_from" class="datetime-format form-control" value="{{ request()->publish_from }}" placeholder="YYYY-MM-DD HH:mm">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <span class="range_icon">～</span>
                                    <div class="form-group">
                                        <div class="input-group mt-2">
                                            <input type="text" id="publish_to" class="datetime-format form-control" name="publish_to" value="{{ request()->publish_to }}" placeholder="YYYY-MM-DD HH:mm">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="ri-calendar-line"></i></span>
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
                    <h5 class="card-title">お知らせ管理一覧</h5>
                </div>
                <div class="card-body">
                    @forelse ($notifications as $notification)
                        @if ($loop->first)
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="text-nowrap">
                                    <tr>
                                        <th scope="col">タイトル</th>
                                        <th scope="col">有効/無効</th>
                                        <th scope="col">公開日時</th>
                                        <th scope="col">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                        @endif
                                    <tr>
                                        <td>{{ Str::limit($notification->title, 60) }}</td>
                                        <td>{{ config('const.is_active.' . $notification->is_active) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($notification->publish_date)->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            <form method="POST" class="form-destroy" action="{{ route('admin.notifications.destroy', $notification->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <a href="{{ route('admin.notifications.edit', $notification->id) }}" class="btn btn btn-outline-success">
                                                    <i class="feather icon-edit mr-2"></i>編集
                                                </a>
                                                <button type="submit" class="btn btn btn-outline-danger"><i class="feather icon-trash-2 mr-2"></i>削除</button>
                                            </form>
                                        </td>
                                    </tr>
                        @if ($loop->last)
                                </tbody>
                            </table>
                        </div>
                        @endif
                    @empty
                        <p>登録された情報がありません。</p>
                    @endforelse
                </div>
                @if ($notifications->count() > 0)
                <div class="card-footer clearfix">
                    {{ $notifications->appends(request()->input())->links('pagination::admin') }}
                </div>
                @endif
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
