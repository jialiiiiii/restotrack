@extends('mgmtlayout')
@section('title', 'Add Customer')

@section('body')
    @if (session()->has('msg'))
        <script>
            @if (session()->get('msg') == 'addSuccess')
                Swal.fire({
                    icon: 'success',
                    title: 'Email Sent',
                    text: 'New customer will be created once the customer confirms the account registration.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                })
            @elseif (session()->get('msg') == 'addExist')
                Swal.fire({
                    icon: 'warning',
                    title: 'Request Made',
                    text: 'Account registration is pending verification. Please try again after 10 minutes.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                })
            @endif
        </script>
    @endif

    <a href="/customers" class="btn btn-secondary mb-4" role="button">
        <i class="fas fa-arrow-left"></i>&nbsp; Back to index
    </a>

    <div class="w-50 mx-auto mb-4">
        <form method="post" action="/customers">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email &nbsp;<i class="fas fa-info-circle"
                        title="An email will be sent to the customer for confirming the account registration."></i></label>
                <input type="text" class="form-control" name="email" id="email" aria-describedby="emailHelp"
                    maxlength="50" value="{{ old('email') }}">
                <div id="emailHelp" class="form-text text-danger">
                    @error('email')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp"
                    maxlength="50" value="{{ old('name') }}">
                <div id="nameHelp" class="form-text text-danger">
                    @error('name')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="password-input">
                    <input type="password" class="form-control" name="password" id="password"
                        aria-describedby="passwordHelp" maxlength="20">
                </div>
                <div id="passwordHelp" class="form-text text-danger">
                    @error('password')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <div class="password-input">
                    <input type="password" class="form-control" name="confirmPassword" id="confirmPassword"
                        aria-describedby="confirmPasswordHelp" maxlength="20">
                </div>
                <div id="confirmPasswordHelp" class="form-text text-danger">
                    @error('confirmPassword')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
@endsection
