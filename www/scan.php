
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="refresh" content="30">
<style>
.fixedsmall {font-family: courier; color: black; font-size: xx-small;}
</style>
</head>
<body class='fixedsmall'>

<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $servername = "localhost";
    $username = "pi";
    $password = "password";
    $dbname = "pi_heating_db";
    $SCHED_ID = $_GET['id'];

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (!$conn) {
        die("<br><br>Connection failed: " . mysqli_connect_error());
       }


    echo 'start';  
 
    $output = shell_exec('ls -lart');

    echo "<pre>$output</pre>"

    mysqli_close($conn);
?>

</body>
</html>
