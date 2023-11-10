//--------------------------------------
// Helpers
//--------------------------------------
function goTo(url) {
    window.location.href = url;
}

function reload(path) {
    $.ajax({
        url: path ?? window.location.href,
        type: 'GET',
        success: function(data) {
            $('body').html(data);
        },
        error: function(xhr, status, error) {
            console.error('Error:', xhr, status, error);
        }
    });
}

//--------------------------------------
// Others
//--------------------------------------
$(function () {

    // Custom navbar dropdown
    $('[data-bs-toggle="myDropdown"]').on('click', function () {
        // Toggle the dropdown menu visibility
        $(this).next('.dropdown-menu').toggle();
    });

    $(document).on('click', function (event) {
        var dropdown = $('[data-bs-toggle="myDropdown"]');
        var dropdownMenu = dropdown.next('.dropdown-menu');

        // Check if the click is outside the dropdown and not on the dropdown link
        if (!dropdownMenu.is(event.target) && dropdownMenu.has(event.target).length === 0 &&
            !dropdown.is(event.target) && dropdown.has(event.target).length === 0) {
            // Hide the dropdown menu
            dropdownMenu.hide();
        }
    });

    // Scroll top
    const scrollTop = document.querySelector('.scroll-top');
    if (scrollTop) {
        const togglescrollTop = function () {
            window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
        }
        togglescrollTop();
        window.addEventListener('load', togglescrollTop);
        document.addEventListener('scroll', togglescrollTop);
        scrollTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Image preview
    $("#imagePreview").click(function () {
        $("#mealImage").trigger("click");
    });

    $("#mealImage").change(function () {
        const file = this.files[0];
        const imagePreview = $("#imagePreview");

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                imagePreview.attr("src", e.target.result);
            };

            reader.readAsDataURL(file);
        } else {
            imagePreview.attr("src", "/img/default-meal.png");
        }
    });

    // Password field
    const toggleIcon = '<i class="fas fa-eye-slash"></i>';
    $('.password-input').append(toggleIcon);

    $(document).on('click', '.fa-eye:not(.btn-view), .fa-eye-slash', function () {
        var passwordInput = $(this).prevAll("input:first");
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            $(this).addClass('fa-eye').removeClass('fa-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            $(this).addClass('fa-eye-slash').removeClass('fa-eye');
        }
    });

    // Search field
    const searchInput = $('input[name="search"]');
    const clearIcon = $('#clear-icon');
    const form = $('input[name="search"]').closest('form');

    $(form).on('keydown', function (e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    searchInput.on('input', function () {
        const input = $(this).val();
        if (input !== '') {
            clearIcon.show();
        } else {
            clearIcon.hide();
        }
    });

    searchInput.on('keyup', function () {
        const input = $(this).val().trim();
        // Do searching
        $.ajax({
            url: window.location.href + '?page=1',
            method: 'GET',
            data: { query: input },
            success: function (response) {
                var updated = $(response).find('#result');
                $('#result').html(updated.html());
            },
            error: function (xhr, status, error) {
                console.log(error);
            }
        });
    });

    // Clear icon
    clearIcon.on('click', function () {
        searchInput.val('');
        clearIcon.hide();
        searchInput.trigger('keyup');
    });

    // Delete button
    $('.delete-form').on('submit', function (e) {
        e.preventDefault();

        var form = $(this);
        var name = form.data('name');

        Swal.fire({
            icon: 'warning',
            title: 'Caution',
            html: 'Are you sure to delete <strong>' + name + '</strong>?',
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Yes',
            confirmButtonColor: '#3085d6',
        }).then((result) => {
            if (result.isConfirmed) {
                form.off('submit'); // Remove the submit event listener temporarily
                form.submit();      // Submit the form
            }
        })
    });

});
