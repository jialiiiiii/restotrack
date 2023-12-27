@extends('homelayout')
@section('title', 'Register')

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
            <p class="h2 fw-bold title-blue text-center">Register Account</p>
            <form method="post" action="/customers/register">
                @csrf

                <div class="my-4">
                    <input type="text" class="form-control" name="email" id="email" placeholder="Email"
                        aria-describedby="emailHelp" maxlength="50" value="{{ old('email') }}">
                    <div id="emailHelp" class="form-text text-danger">
                        @error('email')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="my-4">
                    <input type="text" class="form-control" name="name" id="name" placeholder="Name"
                        aria-describedby="nameHelp" maxlength="50" value="{{ old('name') }}">
                    <div id="nameHelp" class="form-text text-danger">
                        @error('name')
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
                <div class="my-4">
                    <div class="password-input">
                        <input type="password" class="form-control" name="confirmPassword" id="confirmPassword"
                            placeholder="Confirm Password" aria-describedby="confirmPasswordHelp" maxlength="20">
                    </div>
                    <div id="confirmPasswordHelp" class="form-text text-danger">
                        @error('confirmPassword')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary w-100">Register</button>
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
