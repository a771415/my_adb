<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض العملاء</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        #container {
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 1200px;
            margin-top: 30px;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border-radius: 8px;
            background-color: #fff;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 15px;
            text-align: center;
            font-weight: bold;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        form {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        input[type=text] {
            padding: 10px;
            margin-right: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .count {
            font-size: 18px;
            margin-left: 10px;
            color: #777;
        }

        /* تحسين شكل الجدول */
        tbody tr:hover {
            background-color: #f5f5f5;
        }

        tbody tr td {
            white-space: nowrap;
        }

        tbody tr td:first-child,
        tbody tr td:last-child {
            min-width: 150px;
        }

        @media screen and (max-width: 600px) {
            table {
                overflow-x: auto;
                display: block;
            }

            tbody, thead {
                display: block;
            }

            tbody tr {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin: 10px 0;
            }

            tbody tr td {
                width: 100%;
                box-sizing: border-box;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div id="container">

<?php
$servername = "localhost";
$username = "ALI";
$password = "771415164Aa";
$database = "a";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "فشل الاتصال: " . $e->getMessage();
}

$sql = "SELECT ClientID, ClientName, PreviousReading, CurrentReading, PricePerKilo, TotalAmount FROM clientdata";
$stmt = $conn->prepare($sql);

try {
    if ($stmt->execute()) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $rowCount = $stmt->rowCount(); // عدد العملاء
        ?>
        <h1>عرض العملاء</h1>
        <form method="post" action="">
            <label for="searchTerm">ابحث عن العملاء:</label>
            <input type="text" name="searchTerm" id="searchTerm" oninput="searchClients()" placeholder="ابحث هنا...">
            <button type="submit">ابحث</button>
            <span class="count">(عدد العملاء: <?= $rowCount ?>)</span>
        </form>

        <table>
            <thead>
                <tr>
                    <th>رقم العميل</th>
                    <th>اسم العميل</th>
                    <th>القراءة السابقة</th>
                    <th>القراءة الحالية</th>
                    <th>سعر الكيلو</th>
                    <th>المبلغ الإجمالي</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody id="resultsTableBody">
                <?php foreach ($result as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["ClientID"]) ?></td>
                        <td><?= htmlspecialchars($row["ClientName"]) ?></td>
                        <td><?= htmlspecialchars($row["PreviousReading"]) ?></td>
                        <td><?= htmlspecialchars($row["CurrentReading"]) ?></td>
                        <td><?= htmlspecialchars($row["PricePerKilo"]) ?></td>
                        <td><?= htmlspecialchars($row["TotalAmount"]) ?></td>
                        <td>
                            <button onclick='openDepositPage(<?= $row["ClientID"] ?>)'>إيداع</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    } else {
        echo "خطأ في تنفيذ الاستعلام: " . $stmt->errorInfo()[2];
    }
} catch (PDOException $e) {
    echo "خطأ: " . $e->getMessage();
}

$stmt = null;
?>

</div>

<script>
    function searchClients() {
        var searchTerm = document.getElementById('searchTerm').value.toLowerCase();
        var rows = document.getElementById('resultsTableBody').rows;

        for (var i = 0; i < rows.length; i++) {
            var clientName = rows[i].cells[1].textContent.toLowerCase();
            if (clientName.startsWith(searchTerm)) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }

    function openDepositPage(clientID) {
        window.location.href = "deposit.php?clientID=" + encodeURIComponent(clientID);
    }
</script>

</body>
</html>
