jQuery(document).ready(function () {

    // Weight threshold for LTL freight
    en_weight_threshold_limit();

    //          JS for edit product nested fields
    jQuery("._nestedMaterials").closest('p').addClass("_nestedMaterials_tr");
    jQuery("._nestedPercentage").closest('p').addClass("_nestedPercentage_tr");
    jQuery("._maxNestedItems").closest('p').addClass("_maxNestedItems_tr");
    jQuery("._nestedDimension").closest('p').addClass("_nestedDimension_tr");
    jQuery("._nestedStakingProperty").closest('p').addClass("_nestedStakingProperty_tr");

    if (!jQuery('._nestedMaterials').is(":checked")) {
        jQuery('._nestedPercentage_tr').hide();
        jQuery('._nestedDimension_tr').hide();
        jQuery('._maxNestedItems_tr').hide();
        jQuery('._nestedDimension_tr').hide();
        jQuery('._nestedStakingProperty_tr').hide();
    } else {
        jQuery('._nestedPercentage_tr').show();
        jQuery('._nestedDimension_tr').show();
        jQuery('._maxNestedItems_tr').show();
        jQuery('._nestedDimension_tr').show();
        jQuery('._nestedStakingProperty_tr').show();
    }

    jQuery("._nestedPercentage").attr('min', '0');
    jQuery("._maxNestedItems").attr('min', '0');
    jQuery("._nestedPercentage").attr('max', '100');
    jQuery("._maxNestedItems").attr('max', '100');
    jQuery("._nestedPercentage").attr('maxlength', '3');
    jQuery("._maxNestedItems").attr('maxlength', '3');

    if (jQuery("._nestedPercentage").val() == '') {
        jQuery("._nestedPercentage").val(0);
    }

    jQuery("._nestedPercentage").keydown(function (eve) {
        saia_lfq_stop_special_characters(eve);
        var nestedPercentage = jQuery('._nestedPercentage').val();
        if (nestedPercentage.length == 2) {
            var newValue = nestedPercentage + '' + eve.key;
            if (newValue > 100) {
                return false;
            }
        }
    });

    jQuery("._nestedDimension").keydown(function (eve) {
        saia_lfq_stop_special_characters(eve);
        var nestedDimension = jQuery('._nestedDimension').val();
        if (nestedDimension.length == 2) {
            var newValue1 = nestedDimension + '' + eve.key;
            if (newValue1 > 100) {
                return false;
            }
        }
    });

    jQuery("._maxNestedItems").keydown(function (eve) {
        saia_lfq_stop_special_characters(eve);
    });

    jQuery("._nestedMaterials").change(function () {
        if (!jQuery('._nestedMaterials').is(":checked")) {
            jQuery('._nestedPercentage_tr').hide();
            jQuery('._nestedDimension_tr').hide();
            jQuery('._maxNestedItems_tr').hide();
            jQuery('._nestedDimension_tr').hide();
            jQuery('._nestedStakingProperty_tr').hide();
        } else {
            jQuery('._nestedPercentage_tr').show();
            jQuery('._nestedDimension_tr').show();
            jQuery('._maxNestedItems_tr').show();
            jQuery('._nestedDimension_tr').show();
            jQuery('._nestedStakingProperty_tr').show();
        }
    });

    jQuery("#saia_label_as ,#saia_freight_freight_maximum_handling_weight, #saia_freight_settings_handling_weight , #saia_handling_fee").focus(function (e) {
        jQuery("#" + this.id).css({'border-color': '#ddd'});
    });

    jQuery('#wc_settings_saia_accountnbr_third_party').attr('data-optional', '1');
    jQuery("#saia_residential").closest('tr').addClass("saia_residential");
    jQuery("#avaibility_auto_residential").closest('tr').addClass("avaibility_auto_residential");
    jQuery("#avaibility_lift_gate").closest('tr').addClass("avaibility_lift_gate");
    jQuery("#saia_liftgate").closest('tr').addClass("saia_liftgate");
    jQuery("#saia_quotes_liftgate_delivery_as_option").closest('tr').addClass("saia_quotes_liftgate_delivery_as_option");
    jQuery("#wc_settings_saia_application").addClass("wc_settings_saia_application_tr");
    jQuery("#wc_settings_saia_application").addClass("wc_settings_saia_application_tr");
    jQuery("#saia_handling_fee").closest('tr').addClass("saia_handling_fee_tr");
    jQuery("#saia_allow_other_plugins").closest('tr').addClass("saia_allow_other_plugins_tr");
    jQuery("#saia_allow_other_plugins").closest('tr').addClass("saia_allow_other_plugins_tr");
    jQuery("#saia_label_as").closest('tr').addClass("saia_label_as_tr");
    jQuery("#saia_ltl_hold_at_terminal_checkbox_status").closest('tr').addClass("saia_ltl_hold_at_terminal_checkbox_status");

    jQuery("#saia_freight_settings_handling_weight").closest('tr').addClass("saia_freight_settings_handling_weight");
    jQuery("#saia_freight_freight_maximum_handling_weight").closest('tr').addClass("saia_freight_freight_maximum_handling_weight");
    //** End: Validat Shipment Offset Days

    /**
     *Weight of Handling Unit field validation
     */
    jQuery("#saia_freight_settings_handling_weight,#saia_freight_freight_maximum_handling_weight").keydown(function (e) {
        // Allow: backspace, delete, tab, escape and enter
        if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 53, 189]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40) ||
            (e.target.id == 'saia_freight_settings_handling_weight' && (e.keyCode == 109)) ||
            (e.target.id == 'saia_freight_settings_handling_weight' && (e.keyCode == 189))) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

        if ((jQuery(this).val().indexOf('.') != -1) && (jQuery(this).val().substring(jQuery(this).val().indexOf('.'), jQuery(this).val().indexOf('.').length).length > 3)) {
            if (e.keyCode !== 8 && e.keyCode !== 46) { //exception
                e.preventDefault();
            }
        }

    });

    jQuery("#saia_handling_fee, #saia_ltl_hold_at_terminal_fee, #en_wd_origin_markup, #en_wd_dropship_markup, ._en_product_markup").keydown(function (e) {

        // Allow: backspace, delete, tab, escape, enter and .
        if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 53, 189]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode === 53 && e.shiftKey) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

        if ((jQuery(this).val().indexOf('.') != -1) && (jQuery(this).val().substring(jQuery(this).val().indexOf('.'), jQuery(this).val().indexOf('.').length).length > 2)) {
            if (e.keyCode !== 8 && e.keyCode !== 46) { //exception
                e.preventDefault();
            }
        }

        if(jQuery(this).val().length > 7){
            e.preventDefault();
        }

    });

    jQuery("#saia_handling_fee, #saia_ltl_hold_at_terminal_fee, #en_wd_origin_markup, #en_wd_dropship_markup, ._en_product_markup").keyup(function (e) {

        var val = jQuery(this).val();

        if (val.split('.').length - 1 > 1) {

            var newval = val.substring(0, val.length - 1);
            var countDots = newval.substring(newval.indexOf('.') + 1).length;
            newval = newval.substring(0, val.length - countDots - 1);
            jQuery(this).val(newval);
        }

        if (val.split('%').length - 1 > 1) {
            var newval = val.substring(0, val.length - 1);
            var countPercentages = newval.substring(newval.indexOf('%') + 1).length;
            newval = newval.substring(0, val.length - countPercentages - 1);
            jQuery(this).val(newval);
        }
        if (val.split('>').length - 1 > 0) {
            var newval = val.substring(0, val.length - 1);
            var countGreaterThan = newval.substring(newval.indexOf('>') + 1).length;
            newval = newval.substring(newval, newval.length - countGreaterThan - 1);
            jQuery(this).val(newval);
        }
        if (val.split('_').length - 1 > 0) {
            var newval = val.substring(0, val.length - 1);
            var countUnderScore = newval.substring(newval.indexOf('_') + 1).length;
            newval = newval.substring(newval, newval.length - countUnderScore - 1);
            jQuery(this).val(newval);
        }
    });

    jQuery("#en_wd_origin_markup,#en_wd_dropship_markup,._en_product_markup").bind("cut copy paste",function(e) {
        e.preventDefault();
     });
     
    jQuery("#en_wd_origin_markup,#en_wd_dropship_markup,._en_product_markup").keypress(function (e) {
     if (!String.fromCharCode(e.keyCode).match(/^[-0-9\d\.%\s]+$/i)) return false;
    });

    //** Start: Validat Shipment Offset Days
    jQuery("#fedex_small_shipmentOffsetDays").keydown(function (e) {
        if (e.keyCode == 8)
            return;

        var val = jQuery("#fedex_small_shipmentOffsetDays").val();
        if (val.length > 1 || e.keyCode == 190) {
            e.preventDefault();
        }
        // Allow: backspace, delete, tab, escape, enter and .
        if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 53, 189]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

    });


    /**
     * Offer lift gate delivery as an option and Always include residential delivery fee
     * @returns {undefined}
     */

    jQuery(".checkbox_fr_add").on("click", function () {
        var id = jQuery(this).attr("id");
        if (id == "saia_liftgate") {
            jQuery("#saia_quotes_liftgate_delivery_as_option").prop({checked: false});
            jQuery("#en_woo_addons_liftgate_with_auto_residential").prop({checked: false});

        } else if (id == "saia_quotes_liftgate_delivery_as_option" ||
            id == "en_woo_addons_liftgate_with_auto_residential") {
            jQuery("#saia_liftgate").prop({checked: false});
        }
    });

    jQuery(".saia_ltl_hold_at_terminal_checkbox_status").on("change", function(){
        if (!jQuery('#saia_ltl_hold_at_terminal_checkbox_status').prop("checked")) {
            jQuery('#saia_ltl_hold_at_terminal_remove_address').prop('disabled', true);
        }else {
            jQuery('#saia_ltl_hold_at_terminal_remove_address').prop('disabled', false);
        }
    });
    if (!jQuery('#saia_ltl_hold_at_terminal_checkbox_status').prop("checked")) {
        jQuery('#saia_ltl_hold_at_terminal_remove_address').prop('disabled', true);
    }else {
        jQuery('#saia_ltl_hold_at_terminal_remove_address').prop('disabled', false);
    }

    var url = getUrlVarsSaiaFreight()["tab"];
    if (url === 'saia_quotes') {
        jQuery('#footer-left').attr('id', 'wc-footer-left');
    }
    /*
     * Add err class on connection settings page
     */
    jQuery('.connection_section_class_saia input[type="text"]').each(function () {
        if (jQuery(this).parent().find('.err').length < 1) {
            jQuery(this).after('<span class="err"></span>');
        }
    });

    /*
     * Show Note Message on Connection Settings Page
     */

    jQuery('.connection_section_class_saia .form-table').before("<div class='warning-msg'><p>Note! You must have a SAIA account to use this application. If you don't have one, contact SAIA at 1-800-765-7242.</p></div>");

    /*
     * Add maxlength Attribute on Handling Fee Quote Setting Page
     */

    jQuery("#saia_handling_fee").attr('maxlength', '8');

    /*
     * Add maxlength Attribute on HAT Fee Quote Setting Page
     */

    jQuery("#saia_ltl_hold_at_terminal_fee").attr('maxlength', '8');

    /*
     * Add Title To Connection Setting Fields
     */
    jQuery('#wc_settings_saia_userid').attr('title', 'Username');
    jQuery('#wc_settings_saia_password').attr('title', 'Password');
    jQuery('#wc_settings_saia_accountnbr').attr('title', 'Account Number');
    jQuery('#wc_settings_saia_accountnbr_third_party').attr('title', 'Third Party Account Number');
    jQuery('#wc_settings_saia_accountnbr_postal_code').attr('title', 'Account Number Postal Code');
    jQuery('#wc_settings_saia_plugin_licence_key').attr('title', 'Eniture API Key ');


    /*
     * Add Title To Qoutes Setting Fields
     */

    jQuery('#saia_label_as').attr('title', 'Label As');
    jQuery('#saia_label_as').attr('maxlength', '50');
    jQuery('#saia_handling_fee').attr('title', 'Handling Fee / Markup');

    jQuery(".connection_section_class_saia .button-primary, .connection_section_class_saia .is-primary").click(function () {
        var input = validateInput('.connection_section_class_saia');
        if (input === false) {
            return false;
        }
    });

    jQuery(".connection_section_class_saia .woocommerce-save-button").before('<a href="javascript:void(0)" class="button-primary saia_test_connection">Test connection</a>');

    /*
     * SAIA Test connection Form Valdating ajax Request
     */

    jQuery('.saia_test_connection').click(function (e) {
        var input = validateInput('.connection_section_class_saia');

        if (input === false) {
            return false;
        }

        var postForm = {
            'action': 'saia_action',
            'saia_userid': jQuery('#wc_settings_saia_userid').val(),
            'saia_password': jQuery('#wc_settings_saia_password').val(),
            'saia_accountnbr': jQuery('#wc_settings_saia_accountnbr').val(),
            'saia_thirdparty_accountnbr': jQuery('#wc_settings_saia_accountnbr_third_party').val(),
            'saia_thirdparty_postalcode': jQuery('#wc_settings_saia_accountnbr_postal_code').val(),
            'saia_plugin_license': jQuery('#wc_settings_saia_plugin_licence_key').val(),
        };

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: postForm,
            dataType: 'json',

            beforeSend: function () {
                jQuery(".connection_save_button").remove();
                jQuery('#wc_settings_saia_accountnbr_postal_code').css('background', 'rgba(255, 255, 255, 1) url("' + en_saia_admin_script.plugins_url + '/ltl-freight-quotes-saia-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#wc_settings_saia_accountnbr_third_party').css('background', 'rgba(255, 255, 255, 1) url("' + en_saia_admin_script.plugins_url + '/ltl-freight-quotes-saia-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#wc_settings_saia_userid').css('background', 'rgba(255, 255, 255, 1) url("' + en_saia_admin_script.plugins_url + '/ltl-freight-quotes-saia-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#wc_settings_saia_password').css('background', 'rgba(255, 255, 255, 1) url("' + en_saia_admin_script.plugins_url + '/ltl-freight-quotes-saia-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#wc_settings_saia_accountnbr').css('background', 'rgba(255, 255, 255, 1) url("' + en_saia_admin_script.plugins_url + '/ltl-freight-quotes-saia-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#wc_settings_saia_plugin_licence_key').css('background', 'rgba(255, 255, 255, 1) url("' + en_saia_admin_script.plugins_url + '/ltl-freight-quotes-saia-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
            },
            success: function (data) {
                jQuery('#wc_settings_saia_userid').css('background', '#fff');
                jQuery('#wc_settings_saia_password').css('background', '#fff');
                jQuery('#wc_settings_saia_accountnbr').css('background', '#fff');
                jQuery('#wc_settings_saia_accountnbr_postal_code').css('background', '#fff');
                jQuery('#wc_settings_saia_accountnbr_third_party').css('background', '#fff');
                jQuery('#wc_settings_saia_plugin_licence_key').css('background', '#fff');

                jQuery(".saia_success_message").remove();
                jQuery(".saia_error_message").remove();
                jQuery("#message").remove();

                if (data.message === "success") {
                    jQuery('.warning-msg').before('<div class="notice notice-success saia_success_message"><p><strong>Success!</strong> The test resulted in a successful connection.</p></div>');
                } else if (data.message == "UserID and Password Incorrect.") {

                    jQuery('.warning-msg').before('<div class="notice notice-error saia_error_message"><p><strong>Error!</strong> Please verify credentials and try again.</p></div>');
                } else if (data.message == "failure") {
                    jQuery('.warning-msg').before('<div class="notice notice-error saia_error_message"><p><strong>Error!</strong> Eniture API Key is invalid for this domain.</p></div>');
                } else {
                    jQuery('.warning-msg').before('<div class="notice notice-error saia_error_message"><p><strong>Error!</strong>  ' + data.message + ' </p></div>');
                }
            }
        });
        e.preventDefault();
    });
    // fdo va
    jQuery('#fd_online_id_saia').click(function (e) {
        var postForm = {
            'action': 'saia_fd',
            'company_id': jQuery('#freightdesk_online_id').val(),
            'disconnect': jQuery('#fd_online_id_saia').attr("data")
        }
        var id_lenght = jQuery('#freightdesk_online_id').val();
        var disc_data = jQuery('#fd_online_id_saia').attr("data");
        if(typeof (id_lenght) != "undefined" && id_lenght.length < 1) {
            jQuery(".saia_error_message").remove();
            jQuery('.user_guide_fdo').before('<div class="notice notice-error saia_error_message"><p><strong>Error!</strong> FreightDesk Online ID is Required.</p></div>');
            return;
        }
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: postForm,
            beforeSend: function () {
                jQuery('#freightdesk_online_id').css('background', 'rgba(255, 255, 255, 1) url("' + en_saia_admin_script.plugins_url + '/ltl-freight-quotes-saia-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
            },
            success: function (data_response) {
                if(typeof (data_response) == "undefined"){
                    return;
                }
                var fd_data = JSON.parse(data_response);
                jQuery('#freightdesk_online_id').css('background', '#fff');
                jQuery(".saia_error_message").remove();
                if((typeof (fd_data.is_valid) != 'undefined' && fd_data.is_valid == false) || (typeof (fd_data.status) != 'undefined' && fd_data.is_valid == 'ERROR')) {
                    jQuery('.user_guide_fdo').before('<div class="notice notice-error saia_error_message"><p><strong>Error! ' + fd_data.message + '</strong></p></div>');
                }else if(typeof (fd_data.status) != 'undefined' && fd_data.status == 'SUCCESS') {
                    jQuery('.user_guide_fdo').before('<div class="notice notice-success saia_success_message"><p><strong>Success! ' + fd_data.message + '</strong></p></div>');
                    window.location.reload(true);
                }else if(typeof (fd_data.status) != 'undefined' && fd_data.status == 'ERROR') {
                    jQuery('.user_guide_fdo').before('<div class="notice notice-error saia_error_message"><p><strong>Error! ' + fd_data.message + '</strong></p></div>');
                }else if (fd_data.is_valid == 'true') {
                    jQuery('.user_guide_fdo').before('<div class="notice notice-error saia_error_message"><p><strong>Error!</strong> FreightDesk Online ID is not valid.</p></div>');
                } else if (fd_data.is_valid == 'true' && fd_data.is_connected) {
                    jQuery('.user_guide_fdo').before('<div class="notice notice-error saia_error_message"><p><strong>Error!</strong> Your store is already connected with FreightDesk Online.</p></div>');

                } else if (fd_data.is_valid == true && fd_data.is_connected == false && fd_data.redirect_url != null) {
                    window.location = fd_data.redirect_url;
                } else if (fd_data.is_connected == true) {
                    jQuery('#con_dis').empty();
                    jQuery('#con_dis').append('<a href="#" id="fd_online_id_saia" data="disconnect" class="button-primary">Disconnect</a>')
                }
            }
        });
        e.preventDefault();
    });

    /*
     * SAIA Qoute Settings Tabs Validation
     */

    jQuery('.quote_section_class_saia .woocommerce-save-button').on('click', function () {
        var Error = true;
        jQuery(".updated").hide();
        jQuery('.error').remove();
        if (!saiaLabelValidation()) {
            return false;
        }
        if (!saiaHandlingFeeValidation()) {
            return false;
        }
        if (!saiaHATFeeValidation()) {
            return false;
        }

        if (!saiaFreightMaxWeightOfHandlingUnit()) {
            return false;
        }

        if (!saiaFreightWeightOfHandlingUnit()) {
            return false;
        }

        /*Custom Error Message Validation*/
        var checkedValCustomMsg = jQuery("input[name='wc_pervent_proceed_checkout_eniture']:checked").val();
        var allow_proceed_checkout_eniture = jQuery("textarea[name=allow_proceed_checkout_eniture]").val();
        var prevent_proceed_checkout_eniture = jQuery("textarea[name=prevent_proceed_checkout_eniture]").val();

        if (checkedValCustomMsg == 'allow' && jQuery.trim(allow_proceed_checkout_eniture) == '') {
            jQuery("#mainform .quote_section_class_saia").prepend('<div id="message" class="error inline saia_custom_error_message"><p><strong>Error!</strong> Custom message field is empty.</p></div>');
            jQuery('html, body').animate({
                'scrollTop': jQuery('.saia_custom_error_message').position().top
            });
            return false;
        } else if (checkedValCustomMsg == 'prevent' && jQuery.trim(prevent_proceed_checkout_eniture) == '') {
            jQuery("#mainform .quote_section_class_saia").prepend('<div id="message" class="error inline saia_custom_error_message"><p><strong>Error! </strong>Custom message field is empty.</p></div>');
            jQuery('html, body').animate({
                'scrollTop': jQuery('.saia_custom_error_message').position().top
            });
            return false;
        }

        return Error;
    });

    var prevent_text_box = jQuery('.prevent_text_box').length;
    if (!prevent_text_box > 0) {
        jQuery("input[name*='wc_pervent_proceed_checkout_eniture']").closest('tr').addClass('wc_pervent_proceed_checkout_eniture');
        jQuery(".wc_pervent_proceed_checkout_eniture input[value*='allow']").after('<div class="allow_custom_message"><span>Allow user to continue to check out and display this message </span></div><br><textarea  name="allow_proceed_checkout_eniture" class="prevent_text_box" title="Message" maxlength="250">' + en_saia_admin_script.allow_proceed_checkout_eniture + '</textarea> <span class="description"> Enter a maximum of 250 characters.</span>');
        jQuery(".wc_pervent_proceed_checkout_eniture input[value*='prevent']").after('<div class="allow_custom_message"><span>Prevent user from checking out and display this message</span></div> <br><textarea name="prevent_proceed_checkout_eniture" class="prevent_text_box" title="Message" maxlength="250">' + en_saia_admin_script.prevent_proceed_checkout_eniture + '</textarea> <span class="description"> Enter a maximum of 250 characters.</span>');
    }
    
    // Product variants settings
    jQuery(document).on("click", '._nestedMaterials', function(e) {
        const checkbox_class = jQuery(e.target).attr("class");
        const name = jQuery(e.target).attr("name");
        const checked = jQuery(e.target).prop('checked');

        if (checkbox_class?.includes('_nestedMaterials')) {
            const id = name?.split('_nestedMaterials')[1];
            setNestMatDisplay(id, checked);
        }
    });

    // Callback function to execute when mutations are observed
    const handleMutations = (mutationList) => {
        let childs = [];
        for (const mutation of mutationList) {
            childs = mutation?.target?.children;
            if (childs?.length) setNestedMaterialsUI();
          }
    };
    const observer = new MutationObserver(handleMutations),
        targetNode = document.querySelector('.woocommerce_variations.wc-metaboxes'),
        config = { attributes: true, childList: true, subtree: true };
    if (targetNode) observer.observe(targetNode, config);

});

// Weight threshold for LTL freight
if (typeof en_weight_threshold_limit != 'function') {
    function en_weight_threshold_limit() {
        // Weight threshold for LTL freight
        jQuery("#en_weight_threshold_lfq").keypress(function (e) {
            if (String.fromCharCode(e.keyCode).match(/[^0-9]/g) || !jQuery("#en_weight_threshold_lfq").val().match(/^\d{0,3}$/)) return false;
        });

        jQuery('#en_plugins_return_LTL_quotes').on('change', function () {
            if (jQuery('#en_plugins_return_LTL_quotes').prop("checked")) {
                jQuery('tr.en_weight_threshold_lfq').css('display', 'contents');
            } else {
                jQuery('tr.en_weight_threshold_lfq').css('display', 'none');
            }
        });

        jQuery("#en_plugins_return_LTL_quotes").closest('tr').addClass("en_plugins_return_LTL_quotes_tr");
        // Weight threshold for LTL freight
        var weight_threshold_class = jQuery("#en_weight_threshold_lfq").attr("class");
        jQuery("#en_weight_threshold_lfq").closest('tr').addClass("en_weight_threshold_lfq " + weight_threshold_class);

        // Weight threshold for LTL freight is empty
        if (jQuery('#en_weight_threshold_lfq').length && !jQuery('#en_weight_threshold_lfq').val().length > 0) {
            jQuery('#en_weight_threshold_lfq').val(150);
        }
    }
}

// Update plan
if (typeof en_update_plan != 'function') {
    function en_update_plan(input) {
        let action = jQuery(input).attr('data-action');
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {action: action},
            success: function (data_response) {
                window.location.reload(true);
            }
        });
    }
}

function saiaLabelValidation() {
    var label_value = jQuery('#saia_label_as').val();
    var labelRegex = /^[a-zA-Z0-9\-\s]+$/;
    if (label_value.length > 50) {
        jQuery("#mainform .quote_section_class_saia").prepend('<div id="message" class="error inline saia_spec_label_error"><p><strong>Error! </strong>Maximum 50 alpha characters are allowed for label field.</p></div>');
        jQuery('html, body').animate({
            'scrollTop': jQuery('.saia_spec_label_error').position().top
        });
        jQuery("#saia_label_as").css({'border-color': '#e81123'});
        return false;
    } else if (label_value != '' && !labelRegex.test(label_value)) {
        jQuery("#mainform .quote_section_class_saia").prepend('<div id="message" class="error inline saia_label_error"><p><strong>Error! </strong>No special characters allowed for label field.</p></div>');
        jQuery('html, body').animate({
            'scrollTop': jQuery('.saia_label_error').position().top
        });
        jQuery("#saia_label_as").css({'border-color': '#e81123'});
        return false;
    } else {
        return true;
    }
}

function saiaFreightMaxWeightOfHandlingUnit() {
    var max_weight_of_handling_unit = jQuery('#saia_freight_freight_maximum_handling_weight').val();
    if (max_weight_of_handling_unit.length > 0) {
        var validResponse = isValidDecimal(max_weight_of_handling_unit, 'saia_freight_freight_maximum_handling_weight');
    } else {
        validResponse = true;
    }
    if (validResponse) {
        return true;
    } else {
        jQuery("#mainform .quote_section_class_saia").prepend('<div id="message" class="error inline saia_freight_max_wieght_of_handling_unit_error"><p><strong>Error! </strong>Maximum Weight per Handling Unit format should be like, e.g. 48.5 and only 3 digits are allowed after decimal point. The value can be up to 20,000.</p></div>');
        jQuery('html, body').animate({
            'scrollTop': jQuery('.saia_freight_max_wieght_of_handling_unit_error').position().top
        });
        jQuery("#saia_freight_freight_maximum_handling_weight").css({'border-color': '#e81123'});
        return false;
    }
}

function saiaFreightWeightOfHandlingUnit() {
    var weight_of_handling_unit = jQuery('#saia_freight_settings_handling_weight').val();
    if (weight_of_handling_unit.length > 0) {
        var validResponse = isValidDecimal(weight_of_handling_unit, 'saia_freight_settings_handling_weight');
    } else {
        validResponse = true;
    }
    if (validResponse) {
        return true;
    } else {
        jQuery("#mainform .quote_section_class_saia").prepend('<div id="message" class="error inline saia_freight_wieght_of_handling_unit_error"><p><strong>Error! </strong>Weight of Handling Unit format should be like, e.g. 48.5 and only 3 digits are allowed after decimal point. The value can be up to 20,000.</p></div>');
        jQuery('html, body').animate({
            'scrollTop': jQuery('.saia_freight_wieght_of_handling_unit_error').position().top
        });
        jQuery("#saia_freight_settings_handling_weight").css({'border-color': '#e81123'});
        return false;
    }
}

function saiaHandlingFeeValidation() {
    var handling_fee = jQuery('#saia_handling_fee').val();
    var handling_fee_regex = /^(-?[0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;
    if (handling_fee != '' && !handling_fee_regex.test(handling_fee) || handling_fee.split('.').length - 1 > 1) {
        jQuery("#mainform .quote_section_class_saia").prepend('<div id="message" class="error inline saia_handlng_fee_error"><p><strong>Error! </strong>Handling fee format should be 100.20 or 10%.</p></div>');
        jQuery('html, body').animate({
            'scrollTop': jQuery('.saia_handlng_fee_error').position().top
        });
        jQuery("#saia_handling_fee").css({'border-color': '#e81123'});
        return false;
    } else {
        return true;
    }
}
function saiaHATFeeValidation() {
    var hat_fee = jQuery('#saia_ltl_hold_at_terminal_fee').val();
    var hat_fee_regex = /^(-?[0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;
    if (hat_fee != '' && !hat_fee_regex.test(hat_fee) || hat_fee.split('.').length - 1 > 1) {
        jQuery("#mainform .quote_section_class_saia").prepend('<div id="message" class="error inline saia_hat_fee_error"><p><strong>Error! </strong>Hold at terminal fee format should be 100.20 or 10%.</p></div>');
        jQuery('html, body').animate({
            'scrollTop': jQuery('.saia_hat_fee_error').position().top
        });
        jQuery("#saia_ltl_hold_at_terminal_fee").css({'border-color': '#e81123'});
        return false;
    } else {
        return true;
    }
}

/**
 * Check is valid number
 * @param num
 * @param selector
 * @param limit | LTL weight limit 20K
 * @returns {boolean}
 */
function isValidDecimal(num, selector, limit = 20000) {
    // validate the number:
    // positive and negative numbers allowed
    // just - sign is not allowed,
    // -0 is also not allowed.
    if (parseFloat(num) === 0) {
        // Change the value to zero
        return false;
    }

    const reg = /^(-?[0-9]{1,5}(\.\d{1,4})?|[0-9]{1,5}(\.\d{1,4})?)$/;
    let isValid = false;
    if (reg.test(num)) {
        isValid = inRange(parseFloat(num), -limit, limit);
    }
    if (isValid === true) {
        return true;
    }
    return isValid;
}

/**
 * Check is the number is in given range
 *
 * @param num
 * @param min
 * @param max
 * @returns {boolean}
 */
function inRange(num, min, max) {
    return ((num - min) * (num - max) <= 0);
}

/*
 * SAIA Form Validating Inputs
 */
function validateInput(form_id) {
    var has_err = true;
    jQuery(form_id + " input[type='text']").each(function () {
        var input = jQuery(this).val();
        var response = validateString(input);
        var errorText = jQuery(this).attr('title');
        var optional = jQuery(this).data('optional');

        var errorElement = jQuery(this).parent().find('.err');
        jQuery(errorElement).html('');
        optional = (optional === undefined) ? 0 : 1;

        if (errorText == 'Third Party Account Number') {
            optional = 1;
        }
        errorText = (errorText != undefined) ? errorText : '';

        if ((optional == 0) && (response == false || response == 'empty')) {
            errorText = (response == 'empty') ? errorText + ' is required.' : 'Invalid input.';

            jQuery(errorElement).html(errorText);

        }
        has_err = (response != true && optional == 0) ? false : has_err;
    });
    return has_err;
}

/*
 * SAIA Validating Numbers
 */
function isValidNumber(value, noNegative) {
    if (typeof (noNegative) === 'undefined')
        noNegative = false;
    var isValidNumber = false;
    var validNumber = (noNegative == true) ? parseFloat(value) >= 0 : true;
    if ((value == parseInt(value) || value == parseFloat(value)) && (validNumber)) {
        if (value.indexOf(".") >= 0) {
            var n = value.split(".");
            if (n[n.length - 1].length <= 4) {
                isValidNumber = true;
            } else {
                isValidNumber = 'decimal_point_err';
            }
        } else {
            isValidNumber = true;
        }
    }
    return isValidNumber;
}

/*
 * SAIA Validating String
 */
function validateString(string) {
    if (string == '')
        return 'empty';
    else
        return true;

}

/**
 * Read a page's GET URL variables and return them as an associative array.
 */
function getUrlVarsSaiaFreight() {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

function saia_lfq_stop_special_characters(e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if (jQuery.inArray(e.keyCode, [46, 9, 27, 13, 110, 190, 189]) !== -1 ||
        // Allow: Ctrl+A, Command+A
        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
        // let it happen, don't do anything
        e.preventDefault();
        return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 90)) && (e.keyCode < 96 || e.keyCode > 105) && e.keyCode != 186 && e.keyCode != 8) {
        e.preventDefault();
    }
    if (e.keyCode == 186 || e.keyCode == 190 || e.keyCode == 189 || (e.keyCode > 64 && e.keyCode < 91)) {
        e.preventDefault();
        return;
    }
}

if (typeof setNestedMaterialsUI != 'function') {
    function setNestedMaterialsUI() {
        const nestedMaterials = jQuery('._nestedMaterials');
        const productMarkups = jQuery('._en_product_markup');
        
        if (productMarkups?.length) {
            for (const markup of productMarkups) {
                jQuery(markup).attr('maxlength', '7');

                jQuery(markup).keypress(function (e) {
                    if (!String.fromCharCode(e.keyCode).match(/^[0-9.%-]+$/))
                        return false;
                });
            }
        }

        if (nestedMaterials?.length) {
            for (let elem of nestedMaterials) {
                const className = elem.className;

                if (className?.includes('_nestedMaterials')) {
                    const checked = jQuery(elem).prop('checked'),
                        name = jQuery(elem).attr('name'),
                        id = name?.split('_nestedMaterials')[1];
                    setNestMatDisplay(id, checked);
                }
            }
        }
    }
}

if (typeof setNestMatDisplay != 'function') {
    function setNestMatDisplay (id, checked) {
        
        jQuery(`input[name="_nestedPercentage${id}"]`).attr('min', '0');
        jQuery(`input[name="_nestedPercentage${id}"]`).attr('max', '100');
        jQuery(`input[name="_nestedPercentage${id}"]`).attr('maxlength', '3');
        jQuery(`input[name="_maxNestedItems${id}"]`).attr('min', '0');
        jQuery(`input[name="_maxNestedItems${id}"]`).attr('max', '100');
        jQuery(`input[name="_maxNestedItems${id}"]`).attr('maxlength', '3');

        jQuery(`input[name="_nestedPercentage${id}"], input[name="_maxNestedItems${id}"]`).keypress(function (e) {
            if (!String.fromCharCode(e.keyCode).match(/^[0-9]+$/))
                return false;
        });

        jQuery(`input[name="_nestedPercentage${id}"]`).closest('p').css('display', checked ? '' : 'none');
        jQuery(`select[name="_nestedDimension${id}"]`).closest('p').css('display', checked ? '' : 'none');
        jQuery(`input[name="_maxNestedItems${id}"]`).closest('p').css('display', checked ? '' : 'none');
        jQuery(`select[name="_nestedStakingProperty${id}"]`).closest('p').css('display', checked ? '' : 'none');
    }
}