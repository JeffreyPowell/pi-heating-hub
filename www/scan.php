
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
#    $SCHED_ID = $_GET['id'];

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (!$conn) {
        die("<br><br>Connection failed: " . mysqli_connect_error());
       }


    #echo 'start';  
 
    $subnet_scan = shell_exec('nmap -sP 192.168.0.0/24 | grep report | grep -v router | cut -d" " -f5');

    echo "<pre>$subnet_scan</pre>";
    
    foreach( $subnet_scan as $device_ip ) {
        echo $device_ip;
        echo '';
    }
    
    
    

    mysqli_close($conn);
?>

</body>
</html>
