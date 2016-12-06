<!DOCTYPE HTML>  
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>  

<?php
$servername = "localhost";
$username = "pi";
$password = "password";
$dbname = "pi_heating_db";
$SCHED_ID = '1';
    
// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    }
    
$sql = "SELECT * FROM schedules WHERE id=".$SCHED_ID;
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        echo var_dump($row)."<br>";
    }
    } else {
        echo "0 results"; 
    }

echo '<br><br>';

$sql = "SELECT * FROM devices LEFT JOIN sched_device ON devices.id=sched_device.device_id AND sched_device.sched_id=".$SCHED_ID.";";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        echo var_dump($row)."<br>";
    }
    } else {
        echo "0 results"; 
    }
echo '<br><br>';
      
$sql = "SELECT * FROM sensors;";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        echo var_dump($row)."<br>";
    }
    } else {
        echo "0 results"; 
    }
echo '<br><br>';
      
$sql = "SELECT * FROM sensors LEFT JOIN sched_sensor ON sensors.id=sched_sensor.sensor_id AND sched_sensor.sched_id=".$SCHED_ID.";";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        echo var_dump($row)."<br>";
    }
    } else {
        echo "0 results"; 
    }
echo '<br><br>';

$sql = "SELECT * FROM modes LEFT JOIN sched_mode ON modes.id=sched_mode.mode_id AND sched_mode.sched_id=".$SCHED_ID.";";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        echo var_dump($row)."<br>";
    }
    } else {
        echo "0 results"; 
    }
echo '<br><br>';
      
$sql = "SELECT * FROM timers LEFT JOIN sched_timer ON timers.id=sched_timer.timer_id AND sched_timer.sched_id=".$SCHED_ID.";";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        echo var_dump($row)."<br>";
    }
    } else {
        echo "0 results"; 
    }
echo '<br><br>';
      
     
    
    
mysqli_close($conn);
?>


</body>
</html>
