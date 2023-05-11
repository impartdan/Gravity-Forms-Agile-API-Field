console.log('loaded org_field.js');
$(window).on('load', function () {
    var xhr;

    window.orgSelector = {
        makeRequest: function (zipCode, org_el, onComplete) {
            // If there is a current request, abort it
            if (xhr && xhr.readyState !== XMLHttpRequest.DONE) {
                xhr.abort();
            }

            // Make a new AJAX request to the specified URL
            var base_url = $('base').attr('href');
            xhr = $.ajax({
                type: 'POST',
                url: base_url + '/wp-admin/admin-ajax.php',
                data: {
                    action: 'get_buildings',
                    zipCode: zipCode,
                },
                beforeSend: function () {
                    org_el.empty().prop('disabled', true);
                    org_el.append('<option>Loading...</option>');
                },
                success: function (response) {
                    // If the request is successful, populate the select field with the response
                    var data = JSON.parse(response);

                    org_el.empty().prop('disabled', false);
                    org_el.append('<option>Please Select</option>');
                    if (data && data.length > 0) {
                        $.each(data, function (i, v) {
                            org_el.append(
                                '<option value="' +
                                    v.instUid +
                                    '">' +
                                    v.institutionNameProper +
                                    '</option>'
                            );
                        });
                    }
                    org_el.append('<option value="other">Other</option>');

                    if (onComplete && typeof onComplete === 'function') {
                        onComplete();
                    }
                },
                error: function (jqXhr, status, error) {
                    if (jqXhr.status === 0 || jqXhr.readyState === 0) {
                        return;
                    }

                    console.log(jqXhr, status, error);
                },
            });
        },
        resetOrg: function (org_el) {
            org_el.empty().prop('disabled', true);
            org_el.append('<option>First enter a zipcode</option>');
        },
        showName: function (school_name_el) {
            school_name_el.show();
        },
        hideName: function (school_name_el) {
            school_name_el.hide();
        },
        handleOrgSelect: function () {
            $('.ginput_container_org').each(function () {
                var zip_el = $('.org_field_zipcode input', this);
                var org_el = $('.org_field_schools select', this);
                var school_id_el = $('.org_field_school_id input', this);
                var school_name_el = $('.org_field_school_name input', this);
                var school_is_custom_el = $(
                    '.org_field_school_is_custom input',
                    this
                );

                // reset the org dropdown
                orgSelector.resetOrg(org_el);

                zip_el.on('keyup', function () {
                    var zipCode = $(this).val();
                    if (zipCode.length == 5) {
                        orgSelector.makeRequest(zipCode, org_el);
                    } else {
                        orgSelector.resetOrg(org_el);
                    }
                });

                org_el.on('change', function () {
                    var org = $(this).val() || '';
                    console.log(org);
                    if (org == 'other') {
                        school_id_el.val('');
                        school_name_el.val(''); // TODO manage hide show
                        school_is_custom_el.val('1');
                        return;
                    }

                    if ($.isNumeric(org) && org.length > 1) {
                        school_id_el.val($(this).val());
                        school_name_el.val(
                            $(this).find('option:selected').text()
                        );
                        school_is_custom_el.val('0');
                        return;
                    }

                    school_id_el.val('');
                    school_name_el.val('');
                    school_is_custom_el.val('0');
                });
            });
        },
    };
    orgSelector.handleOrgSelect();
});

if (typeof orgSelector != 'undefined' && typeof orgSelector === 'object') {
    console.log('reloaded loaded org_field.js');
    orgSelector.handleOrgSelect();

    var zip_el = $('.org_field_zipcode input', this);
    var org_el = $('.org_field_schools select', this);
    var school_id_el = $('.org_field_school_id input', this);
    var school_name_el = $('.org_field_school_name input', this);
    var school_is_custom_el = $('.org_field_school_is_custom input', this);

    var zipCode = zip_el.val();
    if (zipCode.length == 5) {
        console.log('has zip', zipCode);

        orgSelector.makeRequest(zipCode, org_el, function () {
            console.log('made request', school_id_el.val());
            $('option[value="' + school_id_el.val() + '"]', org_el).prop(
                'selected',
                true
            );
        });
        // } else {
        //     resetOrg(org_el);
    }
}
