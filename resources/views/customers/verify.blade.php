@extends('homelayout')

@section('head')
    <style>
        body {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        div.body {
            flex: 1 0 auto;
        }
    </style>
@endsection

@section('body')
    @if ($msg)
        @php
            $title = $msg == 'verifySuccess' ? 'Successful' : 'Failed';
            $img = $msg == 'verifySuccess' ? 'success' : 'fail';
            $txt = $msg == 'verifySuccess' ? 'Thanks for verifying your email. Your customer account has been registered successfully. You may start ordering now!' : 'Your request is invalid or it has expired. Please try again.';
            $url = $msg == 'verifySuccess' ? '/login' : '/home';
            $btn = $msg == 'verifySuccess' ? 'Login Now' : 'Back to Home';
        @endphp

        <div class="body container text-center">
            <p class="h2 fw-bold title-blue">Verification {{ $title }}</p>
            <img class="mx-auto my-4 img-fluid" src="/img/{{ $img }}.png" width="220" alt="{{ $img }}">
            <p class="mx-auto">
                {{ $txt }}
            </p>
            <a href="{{ $url }}" class="btn btn-primary mt-2 mb-4">{{ $btn }}</a>
        </div>
    @endif
@endsection
