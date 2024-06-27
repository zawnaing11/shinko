@extends('admin.layouts.main')
@section('title', 'お知らせ管理')
@section('styles')
<!-- Datepicker css -->
<link href="{{ asset('assets/admin/plugins/datepicker/datepicker.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')

<div class="contentbar">
    <form class="form-store" action="{{ route('admin.notifications.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-5">
                    <div class="card-header">
                        <h5 class="card-title">お知らせ登録</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="" class="form-label">タイトル<span class="required">*</span></label>
                                    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" maxlength="{{ config('const.default_text_maxlength') }}" placeholder="タイトル" required>
                                    @error('title')
                                    <div id="title-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="" class="form-label">本文</label>
                                    <textarea id="body" name="body" class="form-control @error('body') is-invalid @enderror" placeholder="本文" cols="30" rows="5" maxlength="{{ config('const.default_textarea_maxlength') }}">{{ old('body') }}</textarea>
                                    @error('body')
                                    <div id="body-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">画像</label>
                                    <input type="hidden" id="is_image" name="is_image" value="{{ old('is_image') }}">
                                    <input type="file" id="image" name="image" class="image_upload" data-role="none" hidden accept="{{ implode(',', config('const.accept_image_extensions')) }}">
                                    <div class="col-6 pl-0">
                                        <img class="preview_image @if (empty(old('is_image'))) d-none @endif w-100" src="{{ old('is_image') ? \Storage::url(config('const.notification_tmp_path') . old('is_image')) : '#' }}">
                                        <div class="buttons text-center mt-3 @if (empty(old('is_image'))) d-none @endif">
                                            <button type="button" class="btn btn-danger image_delete_btn">画像削除</button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary-rgba btn-lg btn-block mt-3 image_upload_btn @if (! empty(old('is_image'))) d-none @endif">画像アップロード</button>
                                    @error('image')
                                    <div id="image-error" class="invalid-feedback animated fadeInDown d-block">{{ $message }}</div>
                                    @enderror
                                    @error('is_image')
                                    <div id="is_image-error" class="invalid-feedback animated fadeInDown d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">有効/無効<span class="required">*</span></label>
                                    <div>
                                        @foreach (config('const.is_active') as $key => $value)
                                            @if (old('is_active'))
                                            <div class="form-check form-check-inline">
                                                <input type="radio" id="is_active_{{ $key }}" name="is_active" class="form-check-input" value="{{ $key }}" @if (old('is_active') == $key) checked @endif>
                                                <label class="form-check-label" for="is_active_{{ $key }}">{{ $value }}</label>
                                            </div>
                                            @else
                                            <div class="form-check form-check-inline">
                                                <input type="radio" id="is_active_{{ $key }}" name="is_active" class="form-check-input" value="{{ $key }}" @if ($key == 1) checked @endif>
                                                <label class="form-check-label" for="is_active_{{ $key }}">{{ $value }}</label>
                                            </div>
                                            @endif
                                        @endforeach
                                        @error('is_active')
                                        <div id="is_active-error" class="invalid-feedback animated fadeInDown d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="publish_date">公開日時<span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="publish_date" name="publish_date" class="datetime-format form-control @error('publish_date') is-invalid @enderror" value="{{ old('publish_date') }}" placeholder="YYYY-MM-DD HH:mm" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                                        </div>
                                        @error('publish_date')
                                            <div id="publish_date-error" class="invalid-feedback animated fadeInDown d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-light">キャンセル</a>
                            <button type="submit" class="btn btn-primary">保存</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
@section('js')
<!-- Datepicker JS -->
<script src="{{ asset('assets/admin/plugins/datepicker/datepicker.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/datepicker/i18n/datepicker.ja.js') }}"></script>
<script src="{{ asset('assets/admin/js/datepicker-format.js') }}"></script>
<script src="{{ asset('assets/admin/js/upload.js') }}"></script>
@endsection
