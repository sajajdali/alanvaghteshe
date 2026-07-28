@if(session()->has('success'))
    <div class="col-md-12 alert alert-success fade show" role="alert">
        <i class="fa fa-check-circle-o me-2" aria-hidden="true"></i>
        {!! session()->get('success') !!}
    </div>
@endif
@if(session()->has('error'))
    <div class="col-md-12 alert alert-danger fade show" role="alert">
        <i class="fa fa-remove me-2" aria-hidden="true"></i>
        {!!  session()->get('error') !!}
    </div>
@endif
