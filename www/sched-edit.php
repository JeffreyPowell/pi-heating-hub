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
$SCHED_ID = $_GET['id'];
    
#if ( $SCHED_ID < 1 ) { header('Location: /sched-list.php'); exit(); }
    
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["formSubmit"] == "Done" ) {
    header('Location: /sched-list.php');
    exit();
    }

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["formSubmit"] == "Save" ) {
    print_r("<BR>------------------------<BR>");
    print_r($_POST);
    print_r("<BR>------------------------<BR>");

        
    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (!$conn) {
        die("<br><br>Connection failed: " . mysqli_connect_error());
        }

    $sql = "UPDATE schedules SET name = '".$_POST["name"]."', start = '".$_POST["start"]."', end = '".$_POST["end"]."' WHERE id='".$SCHED_ID."';";
    if (mysqli_query($conn, $sql)) {
        echo "<br><br>Schedule updated successfully";
    } else {
        echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    $sql = "UPDATE schedules SET dow1 = '".in_array("dow1", $_POST["repeat_dow"]."' WHERE id='".$SCHED_ID."';";
    if (mysqli_query($conn, $sql)) {
        echo "<br><br>Schedule updated successfully";
    } else {
        echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    
    
    mysqli_close($conn);
}

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("<br><br>Connection failed: " . mysqli_connect_error());
    }

echo '<form method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'?id='.$SCHED_ID.'">';

$sql = "SELECT * FROM schedules WHERE id=".$SCHED_ID;
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
        echo "0 results"; 
    }
    
$row = mysqli_fetch_assoc($result);
    
$SCHED_NAME = $row["name"];
$SCHED_START = $row["start"];
$SCHED_END = $row["end"];
    
echo '<h1>'.$SCHED_NAME.'</h1><br><br>';
echo '<h2>Schedule</h2><br>';
echo '<br><br>';    
echo htmlspecialchars($_SERVER["PHP_SELF"]).'/?id='.$SCHED_ID;
echo '<br><br>';    
echo 'Title: <input type="text" name="name" value="'.$SCHED_NAME.'"><br>';
echo 'Start time: <input type="text" name="start" value="'.$SCHED_START.'"><br>';
echo 'End time: <input type="text" name="end" value="'.$SCHED_END.'"><br>';

$SCHED_DOW1 = $row["dow1"]; if ( $SCHED_DOW1 == '1' ) { $SCHED_DOW1_CHK = 'checked="checked"'; }else{ $SCHED_DOW1_CHK = ''; }
$SCHED_DOW2 = $row["dow2"]; if ( $SCHED_DOW2 == '1' ) { $SCHED_DOW2_CHK = 'checked="checked"'; }else{ $SCHED_DOW2_CHK = ''; }
$SCHED_DOW3 = $row["dow3"]; if ( $SCHED_DOW3 == '1' ) { $SCHED_DOW3_CHK = 'checked="checked"'; }else{ $SCHED_DOW3_CHK = ''; }
$SCHED_DOW4 = $row["dow4"]; if ( $SCHED_DOW4 == '1' ) { $SCHED_DOW4_CHK = 'checked="checked"'; }else{ $SCHED_DOW4_CHK = ''; }
$SCHED_DOW5 = $row["dow5"]; if ( $SCHED_DOW5 == '1' ) { $SCHED_DOW5_CHK = 'checked="checked"'; }else{ $SCHED_DOW5_CHK = ''; }
$SCHED_DOW6 = $row["dow6"]; if ( $SCHED_DOW6 == '1' ) { $SCHED_DOW6_CHK = 'checked="checked"'; }else{ $SCHED_DOW6_CHK = ''; }
$SCHED_DOW7 = $row["dow7"]; if ( $SCHED_DOW7 == '1' ) { $SCHED_DOW7_CHK = 'checked="checked"'; }else{ $SCHED_DOW7_CHK = ''; }
    
echo '<br><br>';
echo 'Repeat every :<br />';
echo "<table><tr><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th></tr>";
echo '<tr>';
echo '<td><input type="checkbox" name="repeat_dow[]" value="dow1" '.$SCHED_DOW1_CHK.' /></td>';
echo '<td><input type="checkbox" name="repeat_dow[]" value="dow2" '.$SCHED_DOW2_CHK.' /></td>';
echo '<td><input type="checkbox" name="repeat_dow[]" value="dow3" '.$SCHED_DOW3_CHK.' /></td>';
echo '<td><input type="checkbox" name="repeat_dow[]" value="dow4" '.$SCHED_DOW4_CHK.' /></td>';
echo '<td><input type="checkbox" name="repeat_dow[]" value="dow5" '.$SCHED_DOW5_CHK.' /></td>';
echo '<td><input type="checkbox" name="repeat_dow[]" value="dow6" '.$SCHED_DOW6_CHK.' /></td>';
echo '<td><input type="checkbox" name="repeat_dow[]" value="dow7" '.$SCHED_DOW7_CHK.' /></td>';
echo '</tr></table>';
echo '<br><br>';


echo '<h2>Activate Devices</h2><br>';

$sql = "SELECT * FROM devices LEFT JOIN sched_device ON devices.id=sched_device.device_id AND sched_device.sched_id=".$SCHED_ID.";";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
        echo "devices 0 results"; 
    }
    
while($row = mysqli_fetch_assoc($result)) {
        $DEVICE_ACTIVE = $row["sched_id"]; if ( $DEVICE_ACTIVE != null ) { $DEVICE_ACTIVE_CHK = 'checked="checked"'; }else{ $DEVICE_ACTIVE_CHK = ''; }
        echo '<input type="checkbox" name="devices[]" value="'.$row["id"].'" '.$DEVICE_ACTIVE_CHK.' />'.$row["name"].'<br>';
    }

echo '<br><br>'; 
    
#$sql = "SELECT * FROM devices LEFT JOIN sched_device ON devices.id=sched_device.device_id AND sched_device.sched_id=".$SCHED_ID.";";
#$result = mysqli_query($conn, $sql);
#if (mysqli_num_rows($result) > 0) {
#    // output data of each row
#    while($row = mysqli_fetch_assoc($result)) {
#        echo var_dump($row)."<br>";
#    }
#    } else {
#        echo "devices 0 results"; 
#    }

    
echo '<h2>When Sensors</h2><br>';

$sql = "SELECT * FROM sensors LEFT JOIN sched_sensor ON sensors.id=sched_sensor.sensor_id AND sched_sensor.sched_id=".$SCHED_ID.";";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
        echo "sensors 0 results"; 
    }
echo '<table>';

while($row = mysqli_fetch_assoc($result)) {

    echo '<tr><td>'.$row["name"].'</td>';

    $SENSOR_OPP = $row["opp"];
    if ( $SENSOR_OPP == "" ) { $NA_SELECTED = 'selected'; }else{ $NA_SELECTED = ''; }
    if ( $SENSOR_OPP == "<" ) { $LT_SELECTED = 'selected'; }else{ $LT_SELECTED = ''; }
    if ( $SENSOR_OPP == "=" ) { $EQ_SELECTED = 'selected'; }else{ $EQ_SELECTED = ''; }
    if ( $SENSOR_OPP == "!" ) { $NE_SELECTED = 'selected'; }else{ $NE_SELECTED = ''; }
    if ( $SENSOR_OPP == ">" ) { $GT_SELECTED = 'selected'; }else{ $GT_SELECTED = ''; }

    echo '<td><select name="sensor_opp">';
    echo '<option value="na" '.$NA_SELECTED.' >(IS IGNORED)</option>';
    echo '<option value="lt" '.$LT_SELECTED.' >IS LESS THAN</option>';
    echo '<option value="eq" '.$EQ_SELECTED.' >IS EQUAL TO</option>';
    echo '<option value="ne" '.$NE_SELECTED.' >IS NOT EQUAL TO</option>';
    echo '<option value="gt" '.$GT_SELECTED.' >IS GREATER THAN</option>';
    echo '</select></td>';

    echo '<td><input type="text" name="sensor_value" value="'.$row["value"].'"></td></tr>';
    }
echo '</table>';
    
echo '<br><br>'; 
   
      
#$sql = "SELECT * FROM sensors LEFT JOIN sched_sensor ON sensors.id=sched_sensor.sensor_id AND sched_sensor.sched_id=".$SCHED_ID.";";
#$result = mysqli_query($conn, $sql);
#if (mysqli_num_rows($result) > 0) {
#    // output data of each row
#    while($row = mysqli_fetch_assoc($result)) {
#        echo var_dump($row)."<br>";
##    }
#    } else {
#        echo "sensors LEFT JOIN sched_sensor 0 results"; 
#    }
#echo '<br><br>';

echo '<h2>AND Modes</h2><br>';

$sql = "SELECT * FROM modes LEFT JOIN sched_mode ON modes.id=sched_mode.mode_id AND sched_mode.sched_id=".$SCHED_ID.";";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
        echo "sensors 0 results"; 
    }
echo '<table>';

while($row = mysqli_fetch_assoc($result)) {

    echo '<tr><td>'.$row["name"].'</td>';

    $MODE_OPP = $row["opp"];
    if ( $MODE_OPP == "" )  { $NA_SELECTED = 'selected'; }else{ $NA_SELECTED = ''; }
    if ( $MODE_OPP == "<" ) { $LT_SELECTED = 'selected'; }else{ $LT_SELECTED = ''; }
    if ( $MODE_OPP == "=" ) { $EQ_SELECTED = 'selected'; }else{ $EQ_SELECTED = ''; }
    if ( $MODE_OPP == "!" ) { $NE_SELECTED = 'selected'; }else{ $NE_SELECTED = ''; }
    if ( $MODE_OPP == ">" ) { $GT_SELECTED = 'selected'; }else{ $GT_SELECTED = ''; }

    echo '<td><select name="mode_opp">';
    echo '<option value="na" '.$NA_SELECTED.' >(IS IGNORED)</option>';
    echo '<option value="lt" '.$LT_SELECTED.' >IS LESS THAN</option>';
    echo '<option value="eq" '.$EQ_SELECTED.' >IS EQUAL TO</option>';
    echo '<option value="ne" '.$NE_SELECTED.' >IS NOT EQUAL TO</option>';
    echo '<option value="gt" '.$GT_SELECTED.' >IS GREATER THAN</option>';
    echo '</select></td>';

    $MODE_VALUE = $row["value"];
    if ( $MODE_VALUE == "" ) { $NA_SELECTED = 'selected'; }else{ $NA_SELECTED = ''; }
    if ( $MODE_VALUE == "0" ) { $T_SELECTED = 'selected'; }else{ $T_SELECTED = ''; }
    if ( $MODE_VALUE == "1" ) { $F_SELECTED = 'selected'; }else{ $F_SELECTED = ''; }
    
    echo '<td><select name="mode_value">';
    echo '<option value="na" '.$NA_SELECTED.' >(IS IGNORED)</option>';
    echo '<option value="true" '.$T_SELECTED.' >ON</option>';
    echo '<option value="false" '.$F_SELECTED.' >OFF</option>';;
    echo '</select></td>';
    
    }
echo '</table>';

#$sql = "SELECT * FROM modes LEFT JOIN sched_mode ON modes.id=sched_mode.mode_id AND sched_mode.sched_id=".$SCHED_ID.";";
#$result = mysqli_query($conn, $sql);
#if (mysqli_num_rows($result) > 0) {
#    // output data of each row
#    while($row = mysqli_fetch_assoc($result)) {
#        echo var_dump($row)."<br>";
#    }
#    } else {
#        echo "modes LEFT JOIN sched_mode 0 results"; 
#    }
echo '<br><br>';

echo '<h2>AND Timers</h2><br>';

$sql = "SELECT * FROM timers LEFT JOIN sched_timer ON timers.id=sched_timer.timer_id AND sched_timer.sched_id=".$SCHED_ID.";";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
        echo "sensors 0 results"; 
    }
echo '<table>';

while($row = mysqli_fetch_assoc($result)) {

    echo '<tr><td>'.$row["name"].'</td>';

    $TIMER_OPP = $row["opp"];
    if ( $TIMER_OPP == "" )  { $NA_SELECTED = 'selected'; }else{ $NA_SELECTED = ''; }
    if ( $TIMER_OPP == "<" ) { $LT_SELECTED = 'selected'; }else{ $LT_SELECTED = ''; }
    if ( $TIMER_OPP == "=" ) { $EQ_SELECTED = 'selected'; }else{ $EQ_SELECTED = ''; }
    if ( $TIMER_OPP == "!" ) { $NE_SELECTED = 'selected'; }else{ $NE_SELECTED = ''; }
    if ( $TIMER_OPP == ">" ) { $GT_SELECTED = 'selected'; }else{ $GT_SELECTED = ''; }

    echo '<td><select name="timer_opp">';
    echo '<option value="na" '.$NA_SELECTED.' >(IS IGNORED)</option>';
    echo '<option value="lt" '.$LT_SELECTED.' >IS LESS THAN</option>';
    echo '<option value="eq" '.$EQ_SELECTED.' >IS EQUAL TO</option>';
    echo '<option value="ne" '.$NE_SELECTED.' >IS NOT EQUAL TO</option>';
    echo '<option value="gt" '.$GT_SELECTED.' >IS GREATER THAN</option>';
    echo '</select></td>';

    $TIMER_VALUE = $row["value"];
    if ( $TIMER_VALUE == "" ) { $NA_SELECTED = 'selected'; }else{ $NA_SELECTED = ''; }
    if ( $TIMER_VALUE == "0" ) { $T_SELECTED = 'selected'; }else{ $T_SELECTED = ''; }
    if ( $TIMER_VALUE == "1" ) { $F_SELECTED = 'selected'; }else{ $F_SELECTED = ''; }
    
    echo '<td><select name="mode_value">';
    echo '<option value="na" '.$NA_SELECTED.' >(IS IGNORED)</option>';
    echo '<option value="true" '.$T_SELECTED.' >RUNNING</option>';
    echo '<option value="false" '.$F_SELECTED.' >STOPPED</option>';;
    echo '</select></td>';
    
    }
echo '</table>';

#$sql = "SELECT * FROM timers LEFT JOIN sched_timer ON timers.id=sched_timer.timer_id AND sched_timer.sched_id=".$SCHED_ID.";";
#$result = mysqli_query($conn, $sql);
#if (mysqli_num_rows($result) > 0) {
#    // output data of each row
#    while($row = mysqli_fetch_assoc($result)) {
#        echo var_dump($row)."<br>";
#    }
#    } else {
#        echo "timers LEFT JOIN sched_timer 0 results"; 
#    }
echo '<br><br>';

echo '<input type="submit" name="formSubmit" value="Save" />';
echo '<input type="submit" name="formSubmit" value="Done" />';
echo '</form>';     

mysqli_close($conn);
?>

</body>
</html>
