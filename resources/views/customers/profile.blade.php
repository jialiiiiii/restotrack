@extends('homelayout')
@section('title', 'Profile')

@section('body')
    @if (session()->has('msg'))
        <script>
            Swal.fire({
                @if (session()->get('msg') == 'updateSuccess')
                    icon: 'success',
                    title: 'Successful',
                    text: 'Profile updated successfully!',
                @elseif (session()->get('msg') == 'updateNoChanges')
                    icon: 'info',
                        title: 'No Changes Made',
                        text: 'Profile remain unchanged.',
                @endif
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
            })
        </script>
    @endif

    <div class="body">
        <div class="d-flex flex-column mx-auto custom-form">
            <p class="h2 fw-bold title-blue text-center">Profile</p>
            <form method="post" action="/customers/profile">
                @csrf

                <div class="my-4">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Name"
                        aria-describedby="nameHelp" maxlength="50" value="{{ old('name', $c) }}" disabled>
                    <div id="nameHelp" class="form-text text-danger">
                        @error('name')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="my-4">
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" value="{{ $c->email }}" disabled>
                </div>
                <div class="my-4">
                    <label class="form-label">Point</label>
                    <input type="text" class="form-control" value="{{ $c->point }}" disabled>
                </div>
                <div class="my-4">
                    <label class="form-label">Joining Date</label>
                    <input type="text" class="form-control" value="{{ $c->created_at }}" disabled>
                </div>
                <div class="my-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-input">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password"
                            aria-describedby="passwordHelp" maxlength="20" disabled>
                    </div>
                    <div id="passwordHelp" class="form-text text-danger">
                        @error('password')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="my-4">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <div class="password-input">
                        <input type="password" class="form-control" name="confirmPassword" id="confirmPassword"
                            placeholder="Confirm Password" aria-describedby="confirmPasswordHelp" maxlength="20" disabled>
                    </div>
                    <div id="confirmPasswordHelp" class="form-text text-danger">
                        @error('confirmPassword')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="text-center mt-5 mb-4">
                    <button id="update" class="btn btn-primary w-100">Update</button>
                    <button id="submit" class="btn btn-primary w-100" type="submit">Confirm Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#submit').hide();

            $('#update').click(function(e) {
                e.preventDefault();
                $('#name, #password, #confirmPassword').prop('disabled', false);
                $('#submit').show();
                $(this).hide();

                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            @if ($errors->any())
                $('#update').trigger('click');
            @endif
        });
    </script>
@endsection
