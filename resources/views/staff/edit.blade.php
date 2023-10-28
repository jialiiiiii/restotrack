@extends('mgmtlayout')
@section('title', 'Edit Staff')

@section('body')
    @if (session()->has('msg') && session()->has('id'))
        <script>
            Swal.fire({
                @if (session()->get('msg') == 'updateSuccess')
                    icon: 'success',
                    title: 'Successful',
                    text: 'Staff updated successfully!',
                @elseif (session()->get('msg') == 'updateNoChanges')
                    icon: 'info',
                    title: 'No Changes Made',
                    text: 'Staff remain unchanged.',
                @endif
                showCancelButton: true,
                cancelButtonText: 'Close',
                confirmButtonText: 'View',
                confirmButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/staff/' + '{{ session()->get('id') }}';
                }
            })
        </script>
    @endif

    <a href="/staff" class="btn btn-secondary mb-4" role="button">
        <i class="fas fa-arrow-left"></i>&nbsp; Back to index
    </a>

    <div class="w-50 mx-auto mb-4">
        <form method="post" action="/staff/{{ $s->id }}">
            @csrf
            @method('put')

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp"
                    maxlength="50" value="{{ old('name', $s) }}">
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
                <button type="submit" class="btn btn-primary">Confirm Changes</button>
            </div>
        </form>
    </div>
@endsection
