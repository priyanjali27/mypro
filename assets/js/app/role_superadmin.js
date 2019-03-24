//Role adding 
function addRole(company_id) {
    var rolename = $('.role_name').val();
    $.ajax({
        url: baseurl + "admin/companyUsers/role_add",
        type: "post",
        data: {rolename: rolename, company_id: company_id},
        success: function (result) {
            //alert(result);
            var data = $.parseJSON(result);
            if (data.error == true) {
                $('.response_msg').html('<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button"> × </button>' + data.success_message + '</div>');
            } else {
                $('.response_msg').html('<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button"> × </button>' + data.success_message + '</div>');
                setTimeout(function () {
                    $('.response_msg').fadeOut();
                    window.location.reload();
                }, 4000);
            }
        }
    });

}

//adding privileges on role
function addPrivileges(roleId, company_id) {
    //fetching role id
    var superadmin
    if ($('.superAdmin' + roleId).prop('checked') == true) {
        superadmin = '1';
    } else {
        superadmin = '0';
    }
    //fetching role add permission 
    var add = new Array();
    $("input[name ='add[]']:checked").each(function () {
        add.push($(this).val());
    });

    //fetching role edit permission
    var edit = new Array();
    $("input[name ='edit[]']:checked").each(function () {
        edit.push($(this).val());
    });

    //fetching role delete permission
    var del = new Array();
    $("input[name ='delete[]']:checked").each(function () {
        del.push($(this).val());
    });

    $.ajax({
        url: baseurl + "admin/companyUsers/add",
        type: "post",
        data: {roleId: roleId, add: add, edit: edit, del: del, superadmin: superadmin, company_id: company_id},
        success: function (result) {
            //alert(result);
            var data = $.parseJSON(result);
            if (data.error == true) {
                $('.massage_box' + roleId).html('<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button"> × </button>' + data.success_message + '</div>');
                setTimeout(function () {
                    $('.massage_box' + roleId).fadeOut();
                    window.location.reload();
                }, 4000);
            } else {
                $('.massage_box' + roleId).html('<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button"> × </button>' + data.success_message + '</div>');
                setTimeout(function () {
                    $('.massage_box' + roleId).fadeOut();
                    window.location.reload();
                }, 4000);
            }
        }
    });
}



// Wait for the DOM to be ready
$(function () {
    // Initialize form validation on the registration form.
    // It has the name attribute "registration"
    $("form[name='userregistration']").validate({
        // Specify validation rules
        rules: {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
            user_name: "required",
            user_username: "required",
            user_phoneno: {
                required: true,
                number: true
            },
            user_gender: "required",
            user_address: "required",
            user_dob: "required",
            user_city: "required",
            user_zipcode: {
                required: true,

            },

            user_email: {
                required: true,
                // Specify that email should be validated
                // by the built-in "email" rule
                email: true
            },
            user_password: {
                required: true,
                minlength: 5
            }
        },
        // Specify validation error messages
        messages: {
            user_name: "Please enter your Name",
            user_username: "Please enter your username",
            user_phoneno: {
                required: "Please enter phone number",
                number: "Your Phone no  must be numeric"
            },
            user_gender: "Please select your gender",
            user_city: "Please enter your city",
            user_address: "Please enter your address",
            user_dob: "Enter your date of birth",

            user_password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 5 characters long",
            },

            user_zipcode: {
                required: "Please enter zipcode",
            },
            user_email: {
                required: "Please enter  email address",
                // Specify that email should be validated
                // by the built-in "email" rule
                email: "Please enter a valid email address",
            },

        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function (form) {
            var formData = $(form).serialize();
            var urlData = $(form).attr('action');
            $.ajax(
                    {
                        url: urlData,
                        type: "POST",
                        crossDomain: true,
                        data: formData,
                        success: function (data)
                        {
                            var data = $.parseJSON(data);
                            if (data.error == 'true') {
                                if (data.msg == 'false') {
                                    $.each(data.error_msg, function (index, value) {
                                        $('#' + index).after('<label id="' + index + '-error" class="error" for="user_name">' + value + '</label>');
                                    });
                                } else {
                                    $('form[name="userregistration"]')[0].reset();
                                    $('form[name="userregistration"]').before('<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button"> × </button>' + data.success_message + '</div>');
                                    if ($('.alert').delay(10000).fadeOut(100)) {
                                        window.location.reload();
                                    }
                                }
                            } else {
                                $('form[name="userregistration"]')[0].reset();
                                $('form[name="userregistration"]').before('<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button"> × </button>' + data.success_message + '</div>');
                                if ($('.alert').delay(10000).fadeOut(100)) {
                                    window.location.reload();
                                }
                            }
                        }
                    });
        }
    });


    $(".staff-edit").click(function (e) {
        e.preventDefault();
        $('#user_name2').attr('value', $(this).data('name'));
        $('#user_username2').attr('value', $(this).data('username'));
        $('#user_dob2').attr('value', $(this).data('dob'));
        $('#user_email2').attr('value', $(this).data('email'));
        $('#user_city2').attr('value', $(this).data('city'));
        $('#user_zipcode2').attr('value', $(this).data('zipcode'));
        $('#user_phoneno2').attr('value', $(this).data('phone'));
        $('#user_address2').text($(this).data('address'));
        var srole = $(this).data('srole');
        $('#user_staffrole2').find('option[value="' + srole + '"]').prop("selected", true);
        var gender = $(this).data('gender');
        $('#user_gender2').find('option[value="' + gender + '"]').prop("selected", true);
        $('#edit_dept_form').attr('action', $(this).data('id'));
    });

    $("form[name='editregistration']").validate({
        // Specify validation rules
        rules: {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
            user_name: "required",
            user_username: "required",
            user_phoneno: {
                required: true,
                number: true
            },
            user_gender: "required",
            user_address: "required",
            user_dob: "required",
            user_city: "required",
            user_zipcode: {
                required: true,
            },

            user_email: {
                required: true,
                // Specify that email should be validated
                // by the built-in "email" rule
                email: true
            }

        },
        // Specify validation error messages
        messages: {
            user_name: "Please enter your Name",
            user_username: "Please enter your username",
            user_phoneno: {
                required: "Please enter phone number",
                number: "Your Phone no  must be numeric"
            },
            user_gender: "Please select your gender",
            user_city: "Please enter your city",
            user_address: "Please enter your address",
            user_dob: "Enter your date of birth",

            user_zipcode: {
                required: "Please enter zipcode",
            },
            user_email: {
                required: "Please enter  email address",
                // Specify that email should be validated
                // by the built-in "email" rule
                email: "Please enter a valid email address",
            },

        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function (form) {
            var formData = $(form).serialize();
            var urlData = $(form).attr('action');
            $.ajax(
                    {
                        url: urlData,
                        type: "POST",
                        crossDomain: true,
                        data: formData,
                        success: function (data)
                        {
                            var data = $.parseJSON(data);
                            if (data.error == 'true') {

                                $.each(data.error_msg, function (index, value) {
                                    $('#' + index).after('<label id="' + index + '-error" class="error" for="user_name">' + value + '</label>');
                                });

                            } else {
                                $('form[name="editregistration"]')[0].reset();
                                $('form[name="editregistration"]').before('<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button"> × </button>' + data.success_message + '</div>');
                                if ($('.alert').delay(10000).fadeOut(100)) {
                                    window.location.reload();
                                }
                            }
                        }
                    });
        }
    });


});



function confirmDeleteModal(id) {
    $('#deleteModal').modal();
    $('#deleteButton').html('<a class="btn btn-danger" onclick="deleteData(' + id + ')">{{constant("DELETE")}}</a>');
}
function deleteData(id) {
    // do your stuffs with id
    $("#successMessage").html("Record With id " + id + " Deleted successfully!");
    var urlData = baseurl + 'admin/companyUsers/staffdelete/' + id;
    $.ajax(
            {
                url: urlData,
                type: "POST",
                success: function ()
                {

                    $('#deleteModal').modal('hide'); // now close modal
                    window.location.reload();
                }
            });


}


