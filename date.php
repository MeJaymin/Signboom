<html>
<head>
</head>
<body>
<?php
$today_vancouver = mktime(date("H")-3, date("i"), date("s"), date("m"), date("d"), date("Y"));
$today = date("Y-m-d H:i:s", $today_vancouver);   // e.g.2001-03-10 17:16:18 (the mysql DATETIME format)

echo "The current date and time the server gives (for Vancouver) is $today<br><br>";
?>
</body>
</html>
