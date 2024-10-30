jQuery(document).ready(function ($) {
  $(".cdashmm_show_consent_box").show_fields_if_checked("#wp-consent_box_text-wrap");
  $('.cdashmm_show_consent_box').change(function() {
    $(this).show_fields_if_checked("#wp-consent_box_text-wrap");
  });

  $(".cdashmm_show_referral_dropdown").show_fields_if_checked(".cdashmm_referral_dropdown_list");
  $('.cdashmm_show_referral_dropdown').change(function() {
    $(this).show_fields_if_checked(".cdashmm_referral_dropdown_list");
  });

  $('#cdashmm_no_donation').hide_fields_if_checked("#cdashmm_donation_explanation");
  $('#cdashmm_no_donation').hide_fields_if_checked("#cdashmm_suggested_donation");

  $('#cdashmm_no_donation').change(function() {
    $(this).hide_fields_if_checked("#cdashmm_donation_explanation");
    $(this).hide_fields_if_checked("#cdashmm_suggested_donation");
  });

  $('#enable_user_registration').show_fields_if_checked('#member_rc_info_page_url');
 // $('#enable_user_registration').show_fields_if_checked('#cdashmm_login_logout_link');

  $('#enable_user_registration').change(function() {
    $(this).show_fields_if_checked('#member_rc_info_page_url');
    //$(this).show_fields_if_checked('#cdashmm_login_logout_link');
  });

  $('#cdashmm_login_logout_link').show_fields_if_checked('#cdashmm_selected_menu');

  $('#cdashmm_login_logout_link').change(function() {
    $(this).show_fields_if_checked('#cdashmm_selected_menu');
  });

  $("#paypal_email").on('change', function(){
    var paypal_email = $(this).val();
    //alert(paypal_email);
    if(!$(this).val()){
      $("#cdashmm_disable_check_pay").prop('disabled', true);
      $("#cdashmm_disable_check_pay").next("span.description").html("Please enable PayPal by adding your PayPal email. You will then be able to disable the check payments.");
      $( "#cdashmm_disable_check_pay" ).prop( "checked", false );
      alert("If you do not have a PayPal email, check payments will be enabled by default!");
    }else{
      //alert(paypal_email);
      $("#cdashmm_disable_check_pay").prop('disabled', false);
      $("#cdashmm_disable_check_pay").next("span.description").html("Check this box if you want to disable check payments.");
    }
  });
});


(function($){
    $.fn.hide_fields_if_checked = function(selector) {
      if((this).is(":checked")){
        $(selector).parent().parent().hide("slow");
      }else{
        $(selector).parent().parent().show("slow");

      }
    }

    $.fn.show_fields_if_checked = function(selector) {
      if((this).is(":checked")){
        $(selector).parent().parent().show("slow");
      }else{
        $(selector).parent().parent().hide("slow");
      }
    }
})(jQuery);
