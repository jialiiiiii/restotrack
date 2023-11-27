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
        async: true,
        success: function (data) {
            // Create a new document using DOMParser
            var parser = new DOMParser();
            var newDoc = parser.parseFromString(data, 'text/html');

            // Replace the current document with the new one
            document.open();
            document.write(newDoc.documentElement.outerHTML);
            document.close();

            // $('body').html(data);
        },
        error: function (xhr, status, error) {
            console.error('Error:', xhr, status, error);
        }
    });
}

//--------------------------------------
// Others
//--------------------------------------

// Date time form
function validateDateTime(date, time) {
    if (date === '' || time === '') {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Please select both date and time.",
            confirmButtonColor: '#3085d6',
        });

        return false;
    } else {
        var selectedDateTime = new Date(date + ' ' + time);
        var currentDate = new Date();

        // Add 1 hour
        currentDate.setHours(currentDate.getHours() + 1);

        // Check if it is at least 1 hour after now
        if (selectedDateTime < currentDate) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "The time should be at least 1 hour later than now.",
                confirmButtonColor: '#3085d6',
            });

            return false;
        }
    }

    return true;
}

// Date time picker
function disableSundays() {
    var inputDate = document.getElementById("date");
    inputDate.addEventListener("input", function () {
        var selectedDate = new Date(inputDate.value);
        if (selectedDate.getDay() === 0) { // 0 corresponds to Sunday
            inputDate.value = '';
        }
    });
}

function disableInvalidTimes() {
    var inputTime = document.getElementById("time");
    inputTime.addEventListener("input", function () {
        var selectedTime = inputTime.value;

        // Parse selected time as a Date object for easier comparison
        var selectedTimeObj = new Date('2000-01-01T' + selectedTime);

        var minTime = new Date('2000-01-01T09:00');
        var maxTime = new Date('2000-01-01T21:00');

        // Check if the selected time is within the valid range
        if (selectedTimeObj < minTime || selectedTimeObj > maxTime) {
            inputTime.value = '';
        }
    });
}

// All functions
function loadBaseJs() {
    // Order page
    $('.orderBox div.status').each(function () {
        var dot = $(this).find('.dot');
        var borderColor = dot.css('border-color');
        $(this).css('color', borderColor);
    });

    $('.orderBar, .reserveBox').on('click', function () {
        var icon = $(this).find('i');
        icon.toggleClass('fa-chevron-down fa-chevron-up');
    });

    $('.dot.small').each(function () {
        var borderColor = $(this).css('border-color');
        $(this).css('background-color', borderColor);
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
        scrollTop.addEventListener('click', function () {
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
}

// Document ready
$(function () {
    loadBaseJs();
});
