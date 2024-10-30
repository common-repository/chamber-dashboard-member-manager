jQuery(document).ready(function ($) {
    $(".hide_payment_button, #show_payment_info").hide();

    $(".show_payment_button").click(function(event){
        event.preventDefault();
        $('#show_payment_info').toggle();

        $(this).html($(this).html() == 'Show Payment Information' ? 'Hide Payment Information' : 'Show Payment Information');
    });

});
