<!DOCTYPE HTML>  
<html>
<head>
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
    
$SENSOR_ID = $_GET['sid'];
$GRAPH_ID = $_GET['gid'];
$GRAPH_SP = $_GET['gsp'];
    
if ( $SENSOR_ID < 1 ) { $SENSOR_ID = 1; }
    
/*    
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["formSubmit"] == "Done" ) {
    header('Location: /sched-list.php');
    exit();
    }
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["formSubmit"] == "Save" ) {
    print_r("<pre><BR>------------------------<BR>");
    print_r($_POST);
    print_r("<BR>------------------------<BR></pre>");
*/        
    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (!$conn) {
        die("<br><br>Connection failed: " . mysqli_connect_error());
        }
    # schedules
    
    $sql = "SELECT * from sensors WHERE id = '".$SENSOR_ID."';";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        echo "devices 0 results"; 
    }
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        echo var_dump($row)."<br>";
    }
/*
    
    
    $sql = "SELECT * FROM sensors WHERE id = '".$SENSOR_ID."';";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        echo "devices 0 results"; 
    }   
    while($row = mysqli_fetch_assoc($result)) {
        $SENSOR_NAME = $row["name"]; if ( $DEVICE_ACTIVE != null ) { $DEVICE_ACTIVE_CHK = 'checked="checked"'; }else{ $DEVICE_ACTIVE_CHK = ''; }
        echo '<input type="checkbox" name="devices[]" value="'.$row["d_id"].'" '.$DEVICE_ACTIVE_CHK.' />'.$row["name"].'<br>';
    }
*/    
    echo '<br><br>'; 
    
  
    
    
    
    
    mysqli_close($conn);



?>

</body>
</html>
