@extends('admin.layouts.main')
@section('title', '店舗管理')
@section('styles')
    <!-- Select2 css -->
    <link href="{{ asset('assets/admin/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')

<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <form method="GET" class="form-search" action="{{ route('admin.stores.index') }}" autocomplete="off">
                <div class="card m-b-30" id="search_box">
                    <div class="card-header collapsed" data-toggle="collapse" data-target="#searchCollapse" aria-expanded="false" style="cursor: pointer;">
                        <h5 class="card-title">検索</h5>
                    </div>
                    <div id="searchCollapse" class="collapse">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="company_id">会社名</label>
                                        <select id="company_id" name="company_id" class="select2-single form-control">
                                            <option value="">選択してください。</option>
                                            @foreach ($companies_list as $company)
                                                <option value="{{ $company->id }}" {{ request()->company_id == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="store_id">店舗名</label>
                                        <select id="store_id" name="store_id" class="select2-single form-control">
                                            <option value="">選択してください。</option>
                                            @foreach ($stores_list as $store)
                                                <option value="{{ $store->id }}" {{ request()->store_id == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                            @endforeach
                                        </select>
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
                    <h5 class="card-title">店舗管理一覧</h5>
                </div>
                <div class="card-body">
                    @forelse ($stores as $store)
                        @if ($loop->first)
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="text-nowrap">
                                    <tr>
                                        <th>ID</th>
                                        <th>会社名</th>
                                        <th>店舗名</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                        @endif
                                    <tr>
                                        <td>{{ $store->id }}</td>
                                        <td>{{ Str::limit($store->companyAdminUserStore?->user->company->name, 30) }}</td>
                                        <td>{{ Str::limit($store->name, 30) }}</td>
                                        <td>
                                            <a href="javascript:void(0)" class="btn btn btn-outline-info show_qr_modal float-left mr-1" data-qr-code="{{ $store->qr_code }}"><i class="ri-qr-code-line mr-2"></i>QR</a>
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
                @if ($stores->count() > 0)
                <div class="card-footer clearfix">
                    {{ $stores->appends(request()->input())->links('pagination::admin') }}
                </div>
                @endif
            </div>
        </div>
    </div>
    <!-- modal / QRコード -->
    <div id="qr_modal" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">QRコード</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body text-center qr_code">
                    <img src="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<!-- Select2 js -->
<script src="{{ asset('assets/admin/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/select2.js') }}"></script>
<script type="text/javascript">
    $('.show_qr_modal').on('click', (e) => {
        const qr_code = $(e.currentTarget).data('qr-code');
        $('.qr_code > img').attr('src', 'data:image/jpeg;base64,' + qr_code);
        $('#qr_modal').modal('show');
    });
</script>
@endsection
