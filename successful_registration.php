<!DOCTYPE html>
<html lang="en">
<head>
    <title>Redirecting...</title>
    <script>
        var countdown = 5;
        function updateCountdown() {
            document.getElementById("countdown").innerHTML = countdown;
            countdown--;
            if (countdown < 0) {
                window.location.href = "index.html";
            } else {
                setTimeout(updateCountdown, 1000);
            }
        }
        setTimeout(updateCountdown, 1000);
    </script>
</head>
<body>
    <p>Redirecting in <span id="countdown">5</span> seconds...</p>
</body>
</html>
