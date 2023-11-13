<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام الإيداع</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        header {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1em;
        }

        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #333;
            color: white;
        }

        form, .info-container, .remaining-amount, .total-payments, .payments-table {
            margin-top: 20px;
            max-width: 100%;
            margin-left: auto;
            margin-right: auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 15px;
            overflow-x: auto; /* ليتناسب الجدول مع حالة الانسكاب الأفقي */
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
        }

        button {
            background-color: #333;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .error-message {
            background-color: #f44336;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <header>
        <h1>نظام الإيداع</h1>
    </header>

    <div class="container">

        <?php
        // Database connection parameters
        $servername = "localhost";
        $username = "ALI";
        $password = "771415164Aa";
        $database = "a";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo '<div class="error-message">فشل الاتصال: ' . $e->getMessage() . '</div>';
            die();
        }

        // Handle form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            handleDeposit($conn);
        }

        // Display client information and deposit form
        if (isset($_GET["clientID"])) {
            displayClientDetails($conn);
        } else {
            echo '<div class="error-message">لم يتم تحديد عميل.</div>';
        }

        // Display remaining amount, total payments, and payments table
        if (isset($clientID)) {
            displayRemainingAmount($conn);
            displayTotalPayments($conn);
            displayPaymentsTable($conn);
        }
        ?>

    </div>

</body>

</html>

<?php
// Function to handle deposit submission
function handleDeposit($conn)
{
    global $clientID;

    $clientID = $_POST["clientID"];
    $amount = $_POST["amount"];

    try {
        $conn->beginTransaction();

        // Insert the deposit into the payments table
        $insertPaymentQuery = "INSERT INTO payments (ClientID, PaymentAmount, PaymentDateTime) VALUES (:clientID, :amount, NOW())";
        $insertPaymentStmt = $conn->prepare($insertPaymentQuery);
        $insertPaymentStmt->bindParam(":clientID", $clientID, PDO::PARAM_INT);
        $insertPaymentStmt->bindParam(":amount", $amount, PDO::PARAM_STR);
        $insertPaymentStmt->execute();

        // Update the remaining amount
        $updateRemainingAmountQuery = "UPDATE remainingamount SET RemainingAmount = RemainingAmount - :amount WHERE ClientID = :clientID";
        $updateRemainingAmountStmt = $conn->prepare($updateRemainingAmountQuery);
        $updateRemainingAmountStmt->bindParam(":clientID", $clientID, PDO::PARAM_INT);
        $updateRemainingAmountStmt->bindParam(":amount", $amount, PDO::PARAM_STR);
        $updateRemainingAmountStmt->execute();

        $conn->commit();

        // Redirect user to success page or any other page
        header("Location: success.php?success=true");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo '<div class="error-message">حدث خطأ أثناء إجراء الإيداع: ' . $e->getMessage() . '</div>';
    }
}

// Function to get client data from the database
function getClientData($conn, $clientID)
{
    $sql = "SELECT * FROM clientdata WHERE ClientID = :clientID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":clientID", $clientID, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to display client information and deposit form
function displayClientDetails($conn)
{
    global $clientID;

    $clientID = $_GET["clientID"];
    $client = getClientData($conn, $clientID);

    if ($client) {
        ?>
        <div class="info-container">
            <h2>بيانات العميل</h2>
            <table>
                <tr>
                    <th>معرف العميل</th>
                    <td><?php echo $client["ClientID"]; ?></td>
                </tr>
                <tr>
                    <th>اسم العميل</th>
                    <td><?php echo $client["ClientName"]; ?></td>
                </tr>
                <tr>
                    <th>رقم الهاتف</th>
                    <td><?php echo $client["PhoneNumber"]; ?></td>
                </tr>
                <!-- ... إضافة المزيد من الحقول حسب الحاجة -->
            </table>

            <h2>إيداع مبلغ</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="clientID" value="<?php echo $client["ClientID"]; ?>">
                <label for="amount">المبلغ:</label>
                <input type="text" id="amount" name="amount" required>
                <br>
                <button type="submit">إيداع</button>
            </form>
        </div>
        <?php
    } else {
        echo '<div class="error-message">العميل غير موجود.</div>';
    }
}

// Function to display remaining amount
function displayRemainingAmount($conn)
{
    global $clientID;

    $totalPaymentsQuery = "SELECT SUM(PaymentAmount) AS TotalPayments FROM payments WHERE ClientID = :clientID";
    $totalPaymentsStmt = $conn->prepare($totalPaymentsQuery);
    $totalPaymentsStmt->bindParam(":clientID", $clientID, PDO::PARAM_INT);
    $totalPaymentsStmt->execute();
    $totalPayments = $totalPaymentsStmt->fetch(PDO::FETCH_ASSOC)["TotalPayments"];

    $remainingAmountQuery = "SELECT RemainingAmount FROM remainingamount WHERE ClientID = :clientID";
    $remainingAmountStmt = $conn->prepare($remainingAmountQuery);
    $remainingAmountStmt->bindParam(":clientID", $clientID, PDO::PARAM_INT);
    $remainingAmountStmt->execute();
    $remainingAmount = $remainingAmountStmt->fetch(PDO::FETCH_ASSOC)["RemainingAmount"];
    ?>

    <div class="remaining-amount">
        <h2>المبلغ المتبقي</h2>
        <p><?php echo $remainingAmount; ?></p>
    </div>

    <?php
}

// Function to display total payments
function displayTotalPayments($conn)
{
    global $clientID;

    $totalPaymentsQuery = "SELECT SUM(PaymentAmount) AS TotalPayments FROM payments WHERE ClientID = :clientID";
    $totalPaymentsStmt = $conn->prepare($totalPaymentsQuery);
    $totalPaymentsStmt->bindParam(":clientID", $clientID, PDO::PARAM_INT);
    $totalPaymentsStmt->execute();
    $totalPayments = $totalPaymentsStmt->fetch(PDO::FETCH_ASSOC)["TotalPayments"];
    ?>

    <div class="total-payments">
        <h2>إجمالي المبالغ المدفوعة</h2>
        <p><?php echo $totalPayments; ?></p>
    </div>

    <?php
}

// Function to display payments table
function displayPaymentsTable($conn)
{
    global $clientID;

    ?>

    <div class="payments-table">
        <h2>سجل المدفوعات</h2>
        <table>
            <tr>
                <th>معرف العملية</th>
                <th>المبلغ</th>
                <th>التاريخ والوقت</th>
            </tr>
            <?php
            $paymentsQuery = "SELECT PaymentID, PaymentAmount, PaymentDateTime FROM payments WHERE ClientID = :clientID";
            $paymentsStmt = $conn->prepare($paymentsQuery);
            $paymentsStmt->bindParam(":clientID", $clientID, PDO::PARAM_INT);
            $paymentsStmt->execute();

            while ($payment = $paymentsStmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$payment['PaymentID']}</td>";
                echo "<td>{$payment['PaymentAmount']}</td>";
                echo "<td>{$payment['PaymentDateTime']}</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <?php
}
?>
