@extends('mgmtlayout')
@section('title', 'Add Meal')

@section('body')
    @if (session()->has('msg') && session()->has('id'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Successful',
                text: 'New meal added successfully!',
                showCancelButton: true,
                cancelButtonText: 'Close',
                confirmButtonText: 'View',
                confirmButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/meals/' + '{{ session()->get('id') }}';
                }
            })
        </script>
    @endif

    <a href="/meals" class="btn btn-secondary mb-4" role="button">
        <i class="fas fa-arrow-left"></i>&nbsp; Back to index
    </a>

    <div class="w-50 mx-auto mb-4">
        <form method="post" action="/meals" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="mb-3">
                <label for="mealImage" class="form-label">Image</label>
                <div id="imageContainer">
                    <img id="imagePreview" class="img-thumbnail" src="/img/default-meal.png" alt="Default">
                    <input type="file" class="form-control visually-hidden" name="mealImage" id="mealImage"
                        accept="image/*" aria-describedby="mealImageHelp">
                </div>
                <div id="mealImageHelp" class="form-text text-danger text-center">
                    @error('mealImage')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="mealName" class="form-label">Name</label>
                <input type="text" class="form-control" name="mealName" id="mealName" aria-describedby="mealNameHelp"
                    maxlength="100" value="{{ old('mealName') }}">
                <div id="mealNameHelp" class="form-text text-danger">
                    @error('mealName')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="mealCategory" class="form-label">Category</label>
                <div class="input-group">
                    @if (!empty($categories))
                        <select class="form-select" id="categorySelect">
                            <option>Select a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    @endif
                    <input type="text" class="form-control" name="mealCategory" id="mealCategory"
                        aria-describedby="mealCategoryHelp" maxlength="30" value="{{ old('mealCategory') }}">
                </div>
                <div id="mealCategoryHelp" class="form-text text-danger">
                    @error('mealCategory')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="mealDescription" class="form-label">Description</label>
                <textarea class="form-control" name="mealDescription" id="mealDescription" rows="3"
                    aria-describedby="mealDescriptionHelp" maxlength="500">{{ old('mealDescription') }}</textarea>
                <div id="mealDescriptionHelp" class="form-text text-danger">
                    @error('mealDescription')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="mealPrice" class="form-label">Price (RM)</label>
                <input class="form-control" type="number" step="0.10" name="mealPrice" id="mealPrice"
                    aria-describedby="mealPriceHelp" value="{{ old('mealPrice') }}" />
                <div id="mealPriceHelp" class="form-text text-danger">
                    @error('mealPrice')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="mealSales" class="form-label">Sales (%)</label>
                <input type="number" step="1" class="form-control" name="mealSales" id="mealSales"
                    aria-describedby="mealNameHelp" value="{{ old('mealSales') }}">
                <div id="mealSalesHelp" class="form-text text-danger">
                    @error('mealSales')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <script>
        $(function() {
            $('#categorySelect').on('change', function() {
                var selectedIndex = $(this).prop('selectedIndex');
                if (selectedIndex !== 0) {
                    var selectedOption = $(this).find('option:selected').text();
                    $('#mealCategory').val(selectedOption);
                }
            });

            $('#mealPrice').on('change', function() {
                var value = parseFloat($(this).val());
                var formattedValue = value.toFixed(2);
                $(this).val(formattedValue);
            });

        });
    </script>
@endsection
