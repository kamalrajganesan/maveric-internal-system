$(document).ready(function() {

    $(".make-text-visible").hide();
    $(".make-text-visible, .make-text-invisible").on('click', function() {
        var password = $(this).parents('div').find('input');
        if ($(this).hasClass('make-text-visible')) {
            $(password).attr("type", "text");
            $(this).parent().find(".make-text-visible").hide();
            $(this).parent().find(".make-text-invisible").show();
        } else {
            $(password).attr("type", "password");
            $(this).parent().find(".make-text-invisible").hide();
            $(this).parent().find(".make-text-visible").show();
        }
    });

    $('#loginForm').on('submit', function(event) {
        
        event.preventDefault(); // Prevent the default form submission
        var formData = $(this).serialize(); // Serialize the form data

        $.ajax({
            url: './services/loginService.php', // The URL to send the data to
            type: 'POST', // The HTTP method to use
            data: formData, // The serialized form data
            dataType: 'json', // The type of data that is being returned
            success: function(response) {
                console.log(response);
                if(response.success == true) {
                    window.location.href = "./dashboard.php"; // Redirect to the index page
                } else {
                    console.log(response);
                    alert("Invalid username or password");
                }
            },
            error: function(xhr, status, error) {
                // Handle any errors that occurred during the request
                console.error(error);
                // You can add more code here to handle the error
            }
        });
    });

    $('#adminLoginForm').on('submit', function(event) {
        
        event.preventDefault(); // Prevent the default form submission
        var formData = $(this).serialize(); // Serialize the form data

        $.ajax({
            url: './services/loginService_admin.php', // The URL to send the data to
            type: 'POST', // The HTTP method to use
            data: formData, // The serialized form data
            dataType: 'json', // The type of data that is being returned
            success: function(response) {
                console.log(response);
                if(response.success == true) {
                    window.location.href = "./dashboard.php"; // Redirect to the index page
                } else {
                    console.log(response);
                    alert("Invalid admin username or password");
                }
            },
            error: function(xhr, status, error) {
                // Handle any errors that occurred during the request
                console.error(error);
                // You can add more code here to handle the error
            }
        });
    });
});