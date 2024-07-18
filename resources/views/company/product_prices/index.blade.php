@extends('company.layouts.main')
@section('title', '商品価格管理')
@section('styles')
    <!-- Select2 css -->
    <link href="{{ asset('assets/admin/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('widgetbar')
    <a href="{{ route('company.product_prices.export') }}" class="btn btn-outline-primary"><i class="ri-chat-download-line align-middle mr-2"></i>CSVエクスポート</a>
    <a href="javascript:void(0)" class="btn btn-outline-primary" data-toggle="modal" data-target="#import_csv"><i class="ri-chat-upload-line align-middle mr-2"></i>CSVインポート</a>
@endsection
@section('content')

<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <form method="GET" class="form-search" action="{{ route('company.product_prices.index') }}" autocomplete="off">
                <div class="card m-b-30" id="search_box">
                    <div class="card-header collapsed" data-toggle="collapse" data-target="#searchCollapse" aria-expanded="false" style="cursor: pointer;">
                        <h5 class="card-title">検索</h5>
                    </div>
                    <div id="searchCollapse" class="collapse">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="store_name">店舗名</label>
                                        <select id="store_name" name="store_name" class="select2-single form-control">
                                            <option value="">選択してください。</option>
                                            @foreach ($stores as $store)
                                                <option value="{{ $store->id }}" {{ request()->store_name == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="jan_cd">JANコード</label>
                                        <input type="text" id="jan_cd" name="jan_cd" class="form-control" value="{{ request()->jan_cd }}" placeholder="JANコード">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="product_name">商品名</label>
                                        <input type="text" id="product_name" name="product_name" class="form-control" value="{{ request()->product_name }}" placeholder="商品名">
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
                    <h5 class="card-title">商品価格一覧</h5>
                </div>
                <div class="card-body">

                    @forelse ($product_prices as $product_price)
                        @if ($loop->first)
                        <div class="table-responsive m-b-30">
                            <table id="posts-table" class="table">
                                <thead class="text-nowrap">
                                    <tr>
                                        <th>店舗名</th>
                                        <th>JANコード</th>
                                        <th>商品名</th>
                                        <th>定価価格（税抜）</th>
                                        <th>販売価格（税込）</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                        @endif
                                    <tr>
                                        <td class="text-break">{{ Str::limit($product_price->store_name, 30) }}</td>
                                        <td>{{ $product_price->jan_cd }}</td>
                                        <td class="text-break">{{ Str::limit($product_price->product_name, 20) }}</td>
                                        <td>{{ number_format($product_price->list_price) . '円' }}</td>
                                        <td>{{ $product_price->price_tax ? number_format($product_price->price_tax) . '円' : '未設定' }}</td>
                                        <td>
                                            <form method="POST" class="form-destroy" action="{{ route('company.product_prices.destroy', $product_price?->product_price_id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <a href="{{ route('company.product_prices.edit', ['store_id' => $product_price->store_id, 'jan_cd' => $product_price->jan_cd]) }}" class="btn btn btn-outline-success">
                                                    <i class="feather icon-edit mr-2"></i>編集
                                                </a>
                                                @if ($product_price->product_price_id)
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
                @if ($product_prices->count() > 0)
                <div class="card-footer clearfix">
                    {{ $product_prices->appends(request()->input())->links('pagination::company') }}
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal fade" id="import_csv" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">CSVインポート</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('company.product_prices.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <input type="file" name="import_file" class="form-control-file" required accept="{{ implode(',', config('const.accept_csv_extensions')) }}">
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
    <!-- Select2 js -->
    <script src="{{ asset('assets/admin/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/select2.js') }}"></script>
@endsection
