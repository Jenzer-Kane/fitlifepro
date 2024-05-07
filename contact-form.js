$("#contactpage").submit(function (e) {
    e.preventDefault();

    $.ajax({
        type: "POST",
        url: "contact-form.php", // Update to your server-side script
        data: $(this).serialize(),
        dataType: "json",
        success: function (response) {
            console.log(response);
            // Handle the response, show success or error messages to the user
            if (response.status === "success") {
                // Display success message
                alert(response.msg);

                // Initiate countdown and redirect after 5 seconds
                var countdown = 5;
                var countdownInterval = setInterval(function () {
                    if (countdown <= 0) {
                        clearInterval(countdownInterval);
                        // Redirect to another page after countdown
                        window.location.href = "contact.php";
                    } else {
                        // Update countdown display
                        document.getElementById("countdown").innerHTML = countdown;
                        countdown--;
                    }
                }, 1000);
            } else {
                // Display error message
                alert(response.msg);
            }
        },
        error: function (error) {
            console.log(error);
            // Handle AJAX error here
            alert("AJAX error");
        }
    });
});
