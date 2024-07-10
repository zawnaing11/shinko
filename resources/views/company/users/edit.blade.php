@extends('company.layouts.main')
@section('title', 'ユーザー管理')
@section('styles')
<!-- Datepicker css -->
<link href="{{ asset('assets/admin/plugins/datepicker/datepicker.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')

<div class="contentbar">
    <form class="form-update" action="{{ route('company.users.update', $user->id) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-5">
                    <div class="card-header">
                        <h5 class="card-title">ユーザー編集</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="email">Eメールアドレス<span class="required">*</span></label>
                                    <input type="text" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" placeholder="Eメールアドレス" required>
                                    @error('email')
                                        <div id="email-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="password">パスワード<span class="required">*</span></label>
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" value="" placeholder="パスワード">
                                    @error('password')
                                        <div id="password-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="name">氏名<span class="required">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" maxlength="{{ config('const.default_text_maxlength') }}" placeholder="氏名" required>
                                    @error('name')
                                        <div id="name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="retirement_date">退職日</label>
                                    <div class="input-group">
                                        <input type="text" id="retirement_date" name="retirement_date" class="date-format form-control @error('retirement_date') is-invalid @enderror" value="{{ old('retirement_date', $user->retirement_date ? date('Y-m-d', strtotime($user->retirement_date)) : "") }}" placeholder="YYYY-MM-DD">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                                        </div>
                                        @error('retirement_date')
                                            <div id="retirement_date-error" class="invalid-feedback animated fadeInDown d-block">{{ $message }}</div>
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
@section('js')
<!-- Datepicker JS -->
<script src="{{ asset('assets/admin/plugins/datepicker/datepicker.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/datepicker/i18n/datepicker.ja.js') }}"></script>
<script src="{{ asset('assets/admin/js/datepicker-format.js') }}"></script>
@endsection
