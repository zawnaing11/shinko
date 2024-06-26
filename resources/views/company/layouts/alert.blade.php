@if (session('alert.primary'))
    <div class="alert alert-primary m-t-30 m-r-30 m-l-30" role="alert">
        {{ session('alert.primary') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if (session('alert.secondary'))
    <div class="alert alert-secondary m-t-30 m-r-30 m-l-30" role="alert">
        {{ session('alert.secondary') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if (session('alert.success'))
    <div class="alert alert-success m-t-30 m-r-30 m-l-30" role="alert">
        {{ session('alert.success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if (session('alert.error'))
    <div class="alert alert-danger m-t-30 m-r-30 m-l-30" role="alert">
        {{ session('alert.error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if (session('alert.warning'))
    <div class="alert alert-warning m-t-30 m-r-30 m-l-30" role="alert">
        {{ session('alert.warning') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if (session('alert.info'))
    <div class="alert alert-info m-t-30 m-r-30 m-l-30" role="alert">
        {{ session('alert.info') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if (session('alert.light'))
    <div class="alert alert-light m-t-30 m-r-30 m-l-30" role="alert">
        {{ session('alert.light') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if (session('alert.dark'))
    <div class="alert alert-dark m-t-30 m-r-30 m-l-30" role="alert">
        {{ session('alert.dark') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
