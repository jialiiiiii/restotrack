@extends('homelayout')
@section('title', 'Menu')

@section('head')
    <style>
        .card {
            background: #fbeee0;
            border: transparent;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease-in-out;
        }

        .card:hover {
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .card-footer {
            border-top: transparent;
            background: transparent;
            padding: 1.5rem 1rem;
            text-align: center;
        }

        .card-title {
            margin-bottom: 0.8rem;
            font-size: 1.1rem;
        }

        .card-text {
            display: -webkit-box;
            -webkit-line-clamp: 5;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .search-icon {
            position: absolute;
            right: 0.5rem;
            top: 0.6rem;
            font-size: 1.1rem;
            color: #1f579c;
            z-index: 5;
            cursor: pointer;
        }

        .cross-icon {
            position: absolute;
            right: 2.5rem;
            top: 0.6rem;
            font-size: 1.2rem;
            color: #1f579c;
            z-index: 5;
            display: none;
            cursor: pointer;
        }

        @media only screen and (max-width: 575px) {
            .custom-pagination {
                display: flex;
                justify-content: center !important;
            }
        }

        @media only screen and (min-width: 1025px) {
            .img-fluid {
                width: 220px !important;
            }
        }
    </style>
@endsection

@section('body')
    <div class="body">
        <div id="result" class="d-flex flex-column mx-3 align-items-center">
            <p class="h2 fw-bold title-blue text-center">Menu</p>
            <form class="w-100 my-4">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <select class="form-select" id="categorySelect">
                            <option value="all">All</option>
                            @if (!empty($categories))
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-12 col-md-4 mt-3 mt-md-0">
                        <select class="form-select" id="sortSelect">
                            <option value="old-new">Old - New</option>
                            <option value="new-old">New - Old</option>
                            <option value="low-high">Low Price - High Price</option>
                            <option value="high-low">High Price - Low Price</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4 mt-3 mt-md-0">
                        <div class="input-group">
                            <input type="text" class="form-control search-bar" placeholder="Search" name="search-menu"
                                id="search-menu" maxlength="50" value="{{ $query }}" aria-describedby="basic-addon1">
                            <span id="search-icon" class="search-icon"><i class="fas fa-search"></i></span>
                            <span id="cross-icon" class="cross-icon"><i class="fas fa-times"></i></span>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-2 g-sm-3 g-md-4 w-100" id="mealList">
                @if (count($meals) > 0)
                    @foreach ($meals as $m)
                        <div class="col">
                            <div class="card h-100">
                                <img src="/img/meals/{{ $m->id }}.png" class="card-img-top" alt="Meal Image">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">{{ $m->name }}</h5>
                                    <p class="card-text">{{ $m->description }}</p>
                                    <p class="card-text">
                                        @if ($m->sales > 0)
                                            <span
                                                class="text-decoration-line-through me-1">RM{{ number_format($m->price, 2) }}</span>
                                            RM {{ number_format(($m->price * (100 - $m->sales)) / 100, 2) }}
                                        @else
                                            RM {{ number_format($m->price, 2) }}
                                        @endif
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 mx-auto my-5 d-flex flex-column align-items-center">
                        <img class="img-fluid w-25" src="/img/empty-result.png" alt="Empty Result" />
                        <div class="mt-4">
                            There is no matching results.
                        </div>
                    </div>
                @endif
            </div>

            @if (count($meals) > 0)
                <div class="custom-pagination w-100 mt-4">
                    {{ $meals->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="fas fa-arrow-up"></i></a>


    <script>
        const scrollTop = document.querySelector('.scroll-top');
        if (scrollTop) {
            const togglescrollTop = function() {
                window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
            }
            window.addEventListener('load', togglescrollTop);
            document.addEventListener('scroll', togglescrollTop);
            scrollTop.addEventListener('click', window.scrollTo({
                top: 0,
                behavior: 'smooth'
            }));
        }

        $(document).ready(function() {

            attachEventHandlers();

            function filterMeals() {
                var selectedCategory = $('#categorySelect').val();
                var sortSelect = $('#sortSelect').val();
                var inputQuery = $('#search-menu').val();

                $.ajax({
                    url: '/menu',
                    type: 'GET',
                    data: {
                        category: selectedCategory,
                        sort: sortSelect,
                        query: inputQuery,
                    },
                    success: function(response) {
                        var updated = $(response).find('#result');
                        $('#result').html(updated.html());

                        $('#categorySelect').val(selectedCategory);
                        $('#sortSelect').val(sortSelect);
                        $('#search-menu').val(inputQuery);

                        attachEventHandlers();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            }

            function attachEventHandlers() {
                $('#categorySelect, #sortSelect').off('change').on('change', function() {
                    filterMeals();
                });

                $('#search-icon').off('click').on('click', function() {
                    filterMeals();
                });

                $('input[name="search-menu"]').on('input', function() {
                    const input = $(this).val();
                    const searchInput = $(this);
                    const crossIcon = $('#cross-icon');

                    if (input !== '') {
                        crossIcon.show();
                        searchInput.css({
                            'padding-right': '3.7rem',
                            'important': 'true'
                        });
                    } else {
                        crossIcon.hide();
                        searchInput.css('padding-right', '');
                    }
                });

                $('#cross-icon').on('click', function() {
                    const searchInput = $('input[name="search-menu"]')
                    const crossIcon = $(this);
                    
                    searchInput.val('');
                    crossIcon.hide();
                    searchInput.css('padding-right', '');
                });
            }

            $(window).keydown(function(event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    filterMeals();
                    return false;
                }
            });

        });
    </script>
@endsection
