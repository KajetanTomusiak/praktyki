$(document).ready(function() {
    var btn = $('#button');

    $(window).scroll(function() {
        if ($(this).scrollTop() > 200) {
            btn.addClass('show');
        } else {
            btn.removeClass('show');
        }
    });

    btn.on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, '200');
    });
});