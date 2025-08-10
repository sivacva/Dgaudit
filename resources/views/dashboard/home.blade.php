@section('content')
    @extends('index2')
    @include('common.alert')

    @php
        $sessionchargedel = session('user');
        //print_r($sessionchargedel);
    @endphp


<div class="position-fixed top-20 start-50 translate-middle-x p-3" style="z-index: 9999; width: 100%;">
    <div class="toast toast-onload align-items-center text-bg-primary border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body hstack align-items-start gap-6">
            <i class="ti ti-alert-circle fs-6"></i>
            <div>
                <h5 class="text-white fs-3 mb-1">Welcome to CAMS,</h5>
                <h6 class="text-white fs-2 mb-0">{{ $sessionchargedel->username ?? 'User' }}</h6>
            </div>
            <button type="button" class="btn-close btn-close-white fs-2 m-0 ms-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>



<script>
    window.addEventListener('DOMContentLoaded', (event) => {
        var toastEl = document.querySelector('.toast-onload');
        var toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    });
</script>

@endsection