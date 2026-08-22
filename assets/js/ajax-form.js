$(function () {

    var form = $('#contact-form');
    var formMessages = $('.ajax-response');

    $(form).submit(function (e) {
        e.preventDefault();

        var formData = $(form).serialize();

        $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: formData,
            dataType: 'json'   // ✅ MUST be json
        })
            .done(function (response) {
                $(formMessages).removeClass('error').addClass('success');
                $(formMessages).text(response.message);

                $('#contact-form input, #contact-form textarea').val('');
            })
            .fail(function (xhr) {

                $(formMessages)
                    .removeClass('success')
                    .addClass('error');

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    $(formMessages).text(xhr.responseJSON.message);
                } else {
                    $(formMessages).text('Oops! Something went wrong.');
                }
            });
    });

});