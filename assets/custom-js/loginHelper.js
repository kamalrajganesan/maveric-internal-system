$(document).ready(function() {
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
                    console.log(response);   
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
});