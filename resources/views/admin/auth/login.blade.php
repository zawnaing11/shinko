@extends('admin.layouts.admin_auth')
@section('content')

<div id="containerbar" class="containerbar authenticate-bg">
    <div class="container">
        <div class="auth-box login-box">
            <div class="row no-gutters align-items-center justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="auth-box-right">
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.authenticate') }}" autocomplete="off">
                                    @csrf
                                    <div class="form-head">
                                        <img src="{{ asset('assets/admin/images/logo.svg') }}" class="img-fluid" alt="logo">
                                    </div>
                                    <h4 class="text-primary my-4">ログイン</h4>
                                    <div class="form-group">
                                        <input type="text" class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" value="{{ old('user_id') }}" placeholder="ユーザーID" required>
                                        @error('user_id')
                                        <span class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="パスワード" required>
                                        @error('password')
                                        <span class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-success btn-lg btn-block font-18">ログイン</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
