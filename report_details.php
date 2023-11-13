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

// تحقق من وجود معرف العميل في رابط الصفحة
if (isset($_GET['clientID'])) {
    $clientID = $_GET['clientID'];

    // يمكنك قم بعرض التقارير أو أي معلومات أخرى حسب احتياجاتك هنا

    echo "عرض التقارير للعميل رقم: $clientID";
} else {
    echo "خطأ: لم يتم تحديد معرف العميل.";
}

$conn = null;
?>
