@extends('company.layouts.main')
@section('title', '商品価格管理')
@section('content')
    <div class="contentbar">
        <form class="form-update" action="{{ route('company.product_prices.update', $product_price?->id) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf
            @method('PUT')
            <input type="hidden" name="store_id" value="{{ request()->store_id }}">
            <input type="hidden" name="jan_cd" value="{{ request()->jan_cd }}">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-5">
                        <div class="card-header">
                            <h5 class="card-title">商品価格編集</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="price" class="form-label">販売価格(税抜)<span class="required">*</span></label>
                                        <input type="text" id="price" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product_price?->price) }}" required placeholder="販売価格(税抜)">
                                        @error('price')
                                            <div id="price-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right card-body">
                <button type="submit" class="btn btn-primary">更新</button>
            </div>
        </form>
    </div>
@endsection
