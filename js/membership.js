jQuery(document).ready(function ($) {
    // When someone picks a membership level, add the price to the total
	$('#level, #donation').change(function (evt) {
        var url = membershipformajax.ajaxurl;
        var level = $('#level').val();
        var donation = $('#donation').val();
        var form_action = $('#cdashmm_form_action').val();
        var nonce = $('#cdashmm_membership_nonce').val();
		var data = {
            'action': 'cdashmm_update_total_amount',
            'level_id': level,
            'donation': donation,
            'form_action': form_action,
            'nonce': nonce,
        };
        $.post(url, data, function (response) {
            response = JSON.parse(response);
            // membership cost
            var membership = response['membership_fee'];
            $("#membership_amt").val(membership);

            //$(".paypal.recurring-field.total").val(membership);
            // processing fee
            var fee = response['processing_fee'];
            $('#processing').val(fee);
            // tax rate
            var tax_rate = response['tax_rate'] / 100;
            $('#tax_rate').val(tax_rate);
            // donation
            var donation = +$("#donation").val();
            if( isNaN( donation ) ) {
                donation = 0;
            }
            // subtotal
            var subtotal = response['taxable_amount'];
            $("#subtotal").val(subtotal);
            // tax
            var tax = response['tax'];
            $("#tax").val( tax );
            // total
            var total = response['total'];
			$("#total").val( total );
            $(".paypal.recurring-field.total").val(total);

            if(total == 0){
                $('.recurring').hide();
                $('.method').hide();
            }else{
                $('.recurring').show();
                $('.method').show();
                $('.method.wc_payments').hide();
            }
            // paypal fields
            $("#amount_1").val(membership);
            $("#amount_2").val(donation);
            $("#amount_3").val(fee);
            $("#amount_4").val(tax);
        });
    });

    // when business name changes, check to see whether the business is already in the database
    $('#name').change(function (evt) {
        var url = membershipformajax.ajaxurl;
        var name = $('#name').val();
        var nonce = $('#cdashmm_membership_nonce').val();
        var data = {
            'action': 'cdashmm_find_existing_business',
            'nonce': nonce,
            'name': name
        };
        // insert the business selection form into the page
        $.post(url, data, function (response) {
            jQuery("#business-picker").html(response);
        });
    });

    // when a business is selected, fill in the form
    $('#business-picker').on('change', 'input[name=business_id]:radio', function (evt) {
        var url = membershipformajax.ajaxurl;
        var business_id = $('input[name=business_id]:checked', '#membership_form').val()
        var nonce = $('#cdashmm_membership_nonce').val();
        var data = {
            'action': 'cdashmm_prefill_membership_form',
            'nonce': nonce,
            'business_id': business_id,
        };
        // fill in the form
        $.post(url, data, function (response) {
            $("#address").val(response.address);
            $("#city").val(response.city);
            $("#state").val(response.state);
            $("#zip").val(response.zip);
			$("#country").val(response.country);
            $("#phone").val(response.phone);
            $("#email").val(response.email);
            $("#business_id").val(response.business_id);
            $("#name").val(response.business_name);
        });
    });

    // if user pays by check, automatic payment option is hidden
    $('.method').click(function(){
        if($(this).attr("value")=="paypal"){
            $(".recurring").show('slow');
        }
        if($(this).attr("value")=="check"){
            $(".recurring").hide('slow');
        }
    });

    // if user selects recurring payments, a bunch of stuff happens
    $('.recurring-option').click(function(){
        var donation = +$('#donation').val();
        if( isNaN( donation ) ) {
            donation = 0;
        }
        var fee = +$("#processing").val();
        if( isNaN( fee ) ) {
            fee = 0;
        }
        var amount_1 = +$("#membership_amt").val();
        if( isNaN( amount_1 ) ) {
            amount_1 = 0;
        }
        var tax_rate = +$("#tax-rate").val();
        tax_rate = tax_rate / 100;
        if( isNaN( tax_rate ) ) {
            tax_rate = 0;
        }
        //var subtotal = amount_1+donation+fee;
        var subtotal = +$('#subtotal').val();
        if(isNaN(subtotal)) {
            subtotal = 0;
        }
        var tax = $('#tax').val();
        if(isNaN(tax)) {
            tax = 0;
        }
        var total_amount = $('#total').val();
        if(isNaN(total_amount)) {
            total_amount = 0;
        }

        if($(this).attr("value")=="yes"){
            if($(this).hasClass("paypal_recurring")){
                $('.cart').remove();
                display_paypal_subscription_fields(total_amount);
            }else if($(this).hasClass("annual")){
                $(".recurring-field").remove();
                display_paypal_cart_fields(amount_1, donation, fee, tax);
            }else{
                $('.cart').remove();
                display_paypal_subscription_fields(total_amount);
            }
        }

      	if($(this).attr("value")=="no"){
            if($(this).hasClass("paypal_recurring")){
                $(".recurring-field").remove();
                display_paypal_cart_fields(amount_1, donation, fee, tax);
            }else if($(this).hasClass("annual")){
                $(".cart").remove();
                display_paypal_subscription_fields(total_amount);
            }else{
                $(".recurring-field").remove();
                display_paypal_cart_fields(amount_1, donation, fee, tax);
            }
      	}
    });

    // When user fills in username, check if that username is more than 4 characters, is available and valid
    $('#username').change(function (evt) {
        var url = membershipformajax.ajaxurl;
        var username = $('#username').val();
        var nonce = $('#cdashmm_membership_nonce').val();
        var data = {
            'action': 'cdashmm_check_username',
            'nonce': nonce,
            'username': username
        };
        // If the username exists, show the error below the field
        $.post(url, data, function (response) {
            jQuery("#username_error").removeClass('ui-state-valid');
            jQuery("#username_error").removeClass('ui-state-error');
            jQuery("#username_error").addClass(response.error);
            jQuery("#username_error").html(response.text);
        });
    });

    // When user fills in password, check if the password is more than 5 characters
    $('#password').change(function (evt) {
        var url = membershipformajax.ajaxurl;
        var password = $('#password').val();
        var nonce = $('#cdashmm_membership_nonce').val();
        var data = {
            'action': 'cdashmm_check_password',
            'nonce': nonce,
            'password': password
        };
        // If the password is less than 5 characters, show the error below the field
        $.post(url, data, function (response) {
            jQuery("#password, #password_error").removeClass('ui-state-error');
            jQuery("#password, #password_error").removeClass('ui-state-valid');
            jQuery("#password, #password_error").addClass(response.error);
            jQuery("#password_error").html(response.text);
        });
    });

    // When user fills in email, check if it is valid
    $('#email').change(function (evt) {
        var url = membershipformajax.ajaxurl;
        var email = $('#email').val();
        var nonce = $('#cdashmm_membership_nonce').val();
        var cdashmm_form_action = $('#cdashmm_form_action').val();
        var data = {
            'action': 'cdashmm_check_email',
            'nonce': nonce,
            'email': email,
            'cdashmm_form_action': cdashmm_form_action
        };
        // If the email is not valid, show the error below the field
        $.post(url, data, function (response) {
            jQuery("#email, #email_error").removeClass('ui-state-error');
            jQuery("#email, #email_error").removeClass('ui-state-valid');
            jQuery("#email, #email_error").addClass(response.error);
            jQuery("#email_error").html(response.text);
        });
    });

    //Get referral value on change
    $('#referral_dropdown').on('change', function (e) {
	    var optionSelected = $("option:selected", this);
	    var valueSelected = this.value;
    });

    // when user hits the submit button on the become a member page, create/update the business and create the invoice
    $('#membership_form').on('submit', function (e) {
        if ($("#cdashmm_frontend_consent_box").hasClass('ui-state-error')) {
            alert("Please check the consent box before submitting the form. Thank you!");
        }
        var form_action = $('#cdashmm_form_action').val();
        if( (form_action == "renewal") && (!($('#business_id').hasClass('ui-state-valid'))) ) {
            alert("Please enter an existing business name and select it from the list to renew.");
            $("#name.renewal").removeClass('ui-state-valid');
            $("#name.renewal").addClass('ui-state-error');
            return false;
		}

		if($("#membership_form")[0].checkValidity()) {
            var username_error = $("#username_error").attr("class");
            var password_error = $("#password_error").attr("class");
            var email_error = $("#email_error").attr("class");
            if((username_error == 'ui-state-error') || (password_error == 'ui-state-error') || (email_error == 'ui-state-error')) {
                alert("Please correct the errors before proceeding. Thank you!");
                return false;
            }
            $("#membership_form").addClass("loading");
            console.log("valid");
            var url = membershipformajax.ajaxurl;
            var method = $("input[name=method]:checked").val();
			var business_id = $('#business_id').val();
            var name = $('#name').val();
            
            var business_category = $('select[name="business_category"]').val();
            var business_description = $('#memb_form_desc_ifr').contents().find('#tinymce').html();
            if($('#bus_logo').val()){
                var bus_logo = $('#bus_logo')[0].files[0];
            }else{
                var bus_logo = '';
            }
            
            var address = $('#address').val();
            var city = $('#city').val();
            var state = $('#state').val();
            var zip = $('#zip').val();
			var country = $('#country').val();
            var phone = $('#phone').val();
            var email = $('#email').val();
			var biz_main_address = $('#biz_main_address:checked').val();
            var membership_level = $('select[name="level"]').val();
            var member_amt = $('#membership_amt').val();
            var donation = $('#donation').val();
            var total = $('#total').val();
			var tax = $('#tax').val();
            var invoice_id = $('#invoice_id').val();
            var first_name = $('#first_name').val();
            var last_name = $('#last_name').val();
            var username = $('#username').val();
            var password = $('#password').val();
            var referral = $('select[name="referral_dropdown"]').val();
            var mailchimp_subscribe = $("#cdashmc_subscribe_checkbox:checked").val();
            var nonce = $('#cdashmm_membership_nonce').val();
            var custom_field_names = new Array();
            var custom_field_values = new Array();
            $( ".custom_field" ).each(function( index ) {
              custom_field_values.push( $( this ).val() );
              custom_field_names.push( $(this).attr("name"));
            });
            //alert(custom_field_values);
            //alert(custom_field_names);
            var data = new FormData();
            var appendData = function(data, name, value) {
                if(value) {
                    data.append(name, value);
                }
            };
            appendData(data, 'action', 'cdashmm_process_membership_form');
            appendData(data, 'method', method);
            appendData(data, 'business_id', business_id);
            appendData(data, 'name', name);
            appendData(data, 'business_category', business_category);
            appendData(data, 'business_description' , business_description);
            appendData(data, 'bus_logo', bus_logo);
            appendData(data, 'address', address);
            appendData(data, 'city', city);
            appendData(data, 'state', state);
            appendData(data, 'zip', zip);
			appendData(data, 'country', country);
            appendData(data, 'phone', phone);
            appendData(data, 'email', email);
			appendData(data, 'biz_main_address' , biz_main_address);
            appendData(data, 'membership_level', membership_level);
            appendData(data, 'member_amt', member_amt);
            appendData(data, 'donation', donation);
			appendData(data, 'tax', tax);
            appendData(data, 'total', total);
            appendData(data, 'invoice_id', invoice_id);
            appendData(data, 'first_name' , first_name);
            appendData(data, 'last_name' , last_name);
            appendData(data, 'username' , username);
            appendData(data, 'password' , password);
            appendData(data, 'referral'	, referral);
            appendData(data, 'cdashmc_subscribe_checkbox' , mailchimp_subscribe);
            appendData(data, 'cdashmm_form_action' , form_action);
            //var arr = ['this', 'is', 'an', 'array'];
            for (var i = 0; i < custom_field_values.length; i++) {
                appendData(data, 'custom_field_values[]', custom_field_values[i]);
            }
            for (var i = 0; i < custom_field_names.length; i++) {
                appendData(data, 'custom_field_names[]', custom_field_names[i]);
            }
            //appendData(data, 'custom_field_values[]' , custom_field_values);
            //appendData(data, 'custom_field_names[]'  , custom_field_names);
            appendData(data, 'nonce', nonce);

            $.ajax({
                type: 'post',
                url: url,
                processData: false,
                contentType: false,
                data: data,
                // Performing this AJAX call synchronously because we want to get the
                // response back and the response will indicate whether we need to go 
                // to the invoice page, order page, free membership custom page or paypal. 
                async: false,
                success: function(response) {
                    response = JSON.parse(response);
                    if(response['redirect']) {
                        e.preventDefault();
                        window.location = response['redirect'];
                    } else {
                        $('#invoice').val(response['invoice_id']);
                        // Note don't put e.preventDefault because after this AJAX call succeeds
                        // We want the original form to be submitted so that the user can get
                        // redirected to the paypal page for submitting the payment.
                    }
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }else console.log("invalid form");
    });

// make membership form validate
    $('form').h5Validate();
	});

	function display_paypal_subscription_fields(total){
		// note to self - this code is also in cdash-recurring-payments.php, function cdashrp_paypal_subscription_fields

		var fields = "";
		fields = fields + "<input type='hidden' class='paypal recurring-field cmd' name='cmd' value='_xclick-subscriptions'>";
		fields = fields + "<input type='hidden' class='paypal recurring-field item_name' name='item_name' value='Membership'>";
		fields = fields + "<input type='hidden' class='paypal recurring-field a3 price total' name='a3' value='" + total + "'>";
		//fields = fields + "<input type='hidden' class='paypal recurring-field a3 price total' name='a3' value='1000'>";
		fields = fields + "<input type='hidden' class='paypal recurring-field p3 duration' name='p3' value='1'>";
		fields = fields + "<input type='hidden' class='paypal recurring-field t3 duration-unit' name='t3' value='Y'>";
		fields = fields + "<input type='hidden' class='paypal recurring-field src' name='src' value='1'>";
		fields = fields + "<input type='hidden' class='paypal recurring-field no_note' name='no_note' value='1'>";
		fields = fields + "<input type='hidden' class='paypal recurring-field sra' name='sra' value='1'>";
		jQuery('.hidden-paypal-fields').append(fields);
	}

	function display_paypal_cart_fields(amount_1, donation, fee, tax){
		// note to self - this code is also in views.php, function cdashmm_paypal_hidden_fields
		var fields = "";
		fields = fields + "<input type='hidden' class='paypal cart cmd' name='cmd' value='_cart' />";
		fields = fields + "<input type='hidden' class='paypal cart upload' name='upload' value='1' />";
		fields = fields + "<input type='hidden' class='paypal cart item_name_1' name='item_name_1' value='Membership' />";
		fields = fields + "<input type='hidden' class='paypal cart amount_1' name='amount_1' id='amount_1' value='" + amount_1 + "' />";
		fields = fields + "<input type='hidden' class='paypal cart item_name_2' name='item_name_2' value='Donation' />";
		fields = fields + "<input type='hidden' class='paypal cart amount_2' name='amount_2' id='amount_2' value='" + donation + "' />";
		fields = fields + "<input type='hidden' class='paypal cart item_name_3' name='item_name_3' value='Processing Fee' />";
		fields = fields + "<input type='hidden' class='paypal cart amount_3' name='amount_3' id='amount_3' value='" + fee + "' />";
		fields = fields + "<input type='hidden' class='paypal cart item_name_4' name='item_name_4' value='Tax' />";
		fields = fields + "<input type='hidden' class='paypal cart amount_4' name='amount_4' id='amount_4' value='" + tax + "' />";
		jQuery('.hidden-paypal-fields').append(fields);
	}
