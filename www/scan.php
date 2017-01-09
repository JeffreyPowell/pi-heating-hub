
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
    echo '<br>';
 
    $subnet_scan = shell_exec('nmap -sP 192.168.0.0/24 | grep report | grep -v router | cut -d" " -f5');

    #echo "<pre>$subnet_scan</pre>";
    
    $subnet_devices = explode( "\n", $subnet_scan);
    
    
    foreach( $subnet_devices as $device_ip ) {
        echo $device_ip;
        echo '<br>';
        $sensor_count = file_get_contents('http://$device_ip/count:8080');
        echo $sensor_count;
        echo '<br>';
    }
    
    
    
    
    

    mysqli_close($conn);
?>

</body>
</html>
