$(document).ready(function(){

    $("#frmNewUser").validate({
        onkeyup: false,
        errorElement: 'span',
        errorClass: 'error',
        rules:{
            txtFirst:{
                required:true,
                maxlength: 20
            },
            txtLast:{
                required:true,
                maxlength: 20
            },
            txtEmail:{
                required:true,
                email: true
            },
            txtPhone:{
                required:true,
                phoneNumber:true
            },
            txtUser:{
                required:true,
                remote: {
                    url: "scripts/checkUserName.php",
                    type: "post",
                    data: {
                        txtUser:function()
                        {
                            return $( "#txtUser" ).val();
                        }
                    },
                    error: function(xhr, textStatus, errorThrown)
                    {
                        alert('ajax loading error... ... '+ errorThrown);
                    }
                },
            },
            txtPass1:{
                required:true,
                minlength: 6
            },
            txtPass2:{
                required:true,
                minlength: 6,
                equalTo: txtPass1
            }
        },
        messages:{
            txtFirst:{
                required:"Please enter a first name",
                maxlength: "Max length of 20"
            },
            txtLast:{
                required:"Please enter a last name",
                maxlength: "Max length of 20"
            },
            txtEmail:{
                required:"Please enter an email",
                email: "Use format abc@abc.com"
            },
            txtPhone:{
                required:"Please enter a phone number"
            },
            txtUser:{
                required:"Please enter a username",
                remote:"User Already Exists"
            },
            txtPass1:{
                required:"Please enter a password"
            },
            txtPass2:{
                required:"Please enter a matching password",
                equalTo: "Passwords Must Match"
            }
        },
        submitHandler: function(form)
        {
            form.submit();
        }
    });
    jQuery.validator.addMethod("phoneNumber", function(value, element) {
        return /^(\d{3}?)[\s|-]\d{3}[\s|-]\d{4}$/.test(value); 
    }, "Phone Number must be in format 111-111-1111");

    
});


