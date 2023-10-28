@extends('homelayout')
@section('title', 'Login')

@section('head')
    <style>
        .divider:after,
        .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #c4b4a4;
        }

        .btn-danger {
            background: #dd4b39;
            border-color: #dd4b39;
        }

        .btn-danger:hover {
            background: #e44f3b;
            border-color: #e44f3b;
        }
    </style>
@endsection

@section('body')
    @if (session()->has('msg') && session()->get('msg') == 'registerSuccess')
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Email Sent',
                text: 'Please check your email inbox to confirms the account registration.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
            })
        </script>
    @endif

    <div class="body">
        <div class="d-flex flex-column mx-auto custom-form">
            <p class="h2 fw-bold title-blue text-center">Log In</p>
            <form method="post" action="/login">
                @csrf

                <div class="my-4">
                    <input type="text" class="form-control" name="emailOrId" id="emailOrId" placeholder="Email/ID"
                        aria-describedby="emailOrIdHelp" maxlength="50" value="{{ old('emailOrId') }}">
                    <div id="emailOrIdHelp" class="form-text text-danger">
                        @error('emailOrId')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="my-4">
                    <div class="password-input">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password"
                            aria-describedby="passwordHelp" maxlength="20">
                    </div>
                    <div id="passwordHelp" class="form-text text-danger">
                        @error('password')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </div>
                <div class="divider d-flex align-items-center my-4">
                    <p class="text-center fw-bold mx-3 mb-0 text-muted">OR</p>
                </div>
                <a class="btn btn-danger w-100 py-2 mb-4" href="/google" role="button">
                    <i class="fab fa-google me-2"></i>Continue with Google
                </a>
            </form>
        </div>
    </div>
@endsection
