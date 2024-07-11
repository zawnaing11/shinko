@extends('company.layouts.main')
@section('title', '商品価格管理')
@section('content')

<div class="contentbar">
    <form class="form-update" action="{{ route('company.product_prices.update', [ request()->store_id, request()->jan_cd ]) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-12">
                <div class="card m-b-30">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-7">
                                <h5 class="card-title mb-0">商品価格詳細</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <div class="mb-4">
                                    <h6>店舗名</h6>
                                    <p>{{ $product_price->store_name }}</p>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-4">
                                    <h6>JANコード</h6>
                                    <p>{{ $product_price->jan_cd }}</p>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-4">
                                    <h6>商品名</h6>
                                    <p>{{ $product_price->product_name }}</p>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-4">
                                    <h6>定価価格（税抜）</h6>
                                    <p>{{ number_format($product_price->list_price) . '円' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="card-title">商品価格編集</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="price" class="form-label">販売価格（税抜）<span class="required">*</span></label>
                                    <input type="number" id="price" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product_price?->price) }}" required min="0" max="{{ config('const.default_integer_maxvalue') }}" placeholder="販売価格（税抜）">
                                    @error('price')
                                        <div id="price-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('company.product_prices.index') }}" class="btn btn-light">キャンセル</a>
                            <button type="submit" class="btn btn-primary">更新</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
