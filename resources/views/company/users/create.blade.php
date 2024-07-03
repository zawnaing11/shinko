@extends('company.layouts.main')
@section('title', 'ユーザー管理')
@section('content')

<div class="contentbar">
    <form class="form-store" action="{{ route('company.users.store') }}" method="POST" autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-5">
                    <div class="card-header">
                        <h5 class="card-title">ユーザー登録</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="email" class="form-label">Eメールアドレス<span class="required">*</span></label>
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Eメールアドレス" required>
                                    @error('email')
                                    <div id="email-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="password" class="form-label">パスワード<span class="required">*</span></label>
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" minlength="{{ config('const.default_password_minlength') }}" maxlength="{{ config('const.default_password_maxlength') }}" placeholder="パスワード" required>
                                    @error('password')
                                    <div id="password-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name" class="form-label">氏名<span class="required">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" maxlength="{{ config('const.default_text_maxlength') }}" placeholder="氏名" required>
                                    @error('name')
                                    <div id="name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
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
                        <div class="text-right">
                            <a href="{{ route('company.users.index') }}" class="btn btn-light">キャンセル</a>
                            <button type="submit" class="btn btn-primary">保存</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
