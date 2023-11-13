<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام الإيداع - نجاح الإيداع</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            margin-top: 50px;
        }

        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .error-message {
            background-color: #f44336;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        button {
            background-color: #333;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 150px;
        }
    </style>
</head>

<body>

    <div class="container">

        <?php
        // Check if the success parameter is set in the URL and has the value "true"
        if (isset($_GET["success"]) && $_GET["success"] == "true") {
            // Display success message and button to return to the homepage
            ?>
            <div class="success-message">
                <h2>تم إيداع المبلغ بنجاح!</h2>
                <p>للعودة إلى صفحة العملاء وعرض الإيداعات، انقر على الزر أدناه.</p>
                <button onclick="redirectToClientsPage()">العودة إلى صفحة العملاء</button>
            </div>
            <script>
                // JavaScript function to redirect to the clients page
                function redirectToClientsPage() {
                    window.location.href = "http://localhost/عرض%20العملا%20+%20ايداع/";
                }
            </script>
            <?php
        } else {
            // Display an error message if the success parameter is not set or has a different value
            ?>
            <div class="error-message">
                <h2>حدث خطأ أثناء معالجة الطلب.</h2>
                <p>يرجى المحاولة مرة أخرى أو الاتصال بالدعم الفني.</p>
            </div>
            <?php
        }
        ?>

    </div>

</body>

</html>
