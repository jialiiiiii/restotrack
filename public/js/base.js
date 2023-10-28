// helpers
function goTo(url) {
    window.location.href = url;
}

$(function () {
    // password field
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

    // search field
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
        // do searching
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

    clearIcon.on('click', function () {
        searchInput.val('');
        clearIcon.hide();
        searchInput.trigger('keyup');
        // window.location.href = window.location.origin + window.location.pathname;
    });

    // delete button
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
                form.off('submit'); // remove the submit event listener temporarily
                form.submit(); // submit the form
            }
        })
    });

});
