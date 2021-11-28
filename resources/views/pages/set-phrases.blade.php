@extends('layouts.auth')

@section('page-title', 'Set phrase')

@section('authContent')
<section class="onboarding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <article>
                    <h1>Set phrase</h1>
                    @include('partials.user.phrases-wizard')
                </article>
            </div>
        </div>
    </div>
</section>
<script type="application/javascript">
    let startStep = @json($stepIndex);
</script>
@endsection
