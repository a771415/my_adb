<!-- sidebar.php -->

<?php
function generateMenuItem($label, $url, $currentPage) {
    $isActive = ($currentPage == $url) ? 'active' : '';
    echo "<li class='$isActive'><a href='$url'>$label</a></li>";
}

$menuItems = array(
    "الرئيسية" => "index.php",
    "الصفحة الثانية" => "page2.php",
    "الصفحة الثالثة" => "page3.php"
);

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحة القائمة الجانبية</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #333;
            color: #fff;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            height: 100vh;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: width 0.5s;
        }

        .logo {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .sidebar ul {
            padding: 0;
            margin: 0;
            width: 100%;
        }

        .sidebar li {
            list-style: none;
            margin-bottom: 10px;
        }

        .sidebar a {
            text-decoration: none;
            color: #fff;
            font-size: 1.2em;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .sidebar a:hover {
            background-color: #555;
        }

        .sidebar .active a {
            color: #ffcc00;
        }

        .sidebar-footer {
            margin-top: auto;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #666;
            width: 100%;
            color: #ccc;
        }

        .sidebar-footer a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s;
        }

        .sidebar-footer a:hover {
            color: #ffcc00;
        }

        /* زر لإظهار/إخفاء القائمة الجانبية */
        #toggleSidebarBtn {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 1.2em;
            margin: 10px;
            transition: background-color 0.3s, color 0.3s;
        }

        #toggleSidebarBtn:hover {
            background-color: #555;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 0;
            }

            #toggleSidebarBtn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- زر لإظهار/إخفاء القائمة الجانبية -->
<button id="toggleSidebarBtn">☰ القائمة</button>

<!-- القائمة الجانبية -->
<div class="sidebar" id="sidebar">
    <div class="logo">حـــلب للكهرباء</div>
    <ul>
        <?php
        foreach ($menuItems as $label => $url) {
            generateMenuItem($label, $url, $currentPage);
        }
        ?>
    </ul>
    <div class="sidebar-footer">
        <p>&copy; تصميم واعداد</p>
        <p>&copy; علي الفقيه</p>
        <p>&copy; 771415164</p>
    </div>
</div>

<!-- JavaScript لتحكم في إظهار/إخفاء القائمة الجانبية -->
<script>
    document.getElementById('toggleSidebarBtn').addEventListener('click', function () {
        var sidebar = document.getElementById('sidebar');
        sidebar.style.width = sidebar.style.width === '250px' ? '0' : '250px';
    });
</script>

</body>
</html>
