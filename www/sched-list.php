<!DOCTYPE HTML>  
<html>
<head>
<meta http-equiv="refresh" content="30">
<style>
    .pbody { background-color: #080808; font-family: courier; color: red; font-size: small;}
    .debug { font-family: courier; color: red; font-size: large; }
    .error { color: #FF0000; }
    .ttab  { width: 100%; }
    .tcol  { font: 22px arial; }
    .tspan { font: 22px arial; color: grey; }
    .dcolname   { text-align: left; padding: 8px 32px; }
    .dcolstatus { text-align: center; }
    .dspan { font-family: arial; color: grey; font-size: large; display: inline-block; }
    .ptitle { font: bold 32px arial; color: blue; }
    .itextbox { font-family: arial; color: grey; font-size: large; padding: 12px 20px; margin: 8px 30px; width: 80%; }
    .bgrey {  background-color: grey;  border: none; color: white; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial; margin: 12px ; }
    .bblue {  background-color: blue;  border: none; color: white; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial; margin: 12px ; }
    .bgreen { background-color: green; border: none; color: white; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial; margin: 12px ; }
    .bred {   background-color: red;   border: none; color: white; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial; margin: 12px ; }
    table, th, td { border: 5px solid #080808; }
    th, td {  background-color: #1a1a1a; }
</style>
</head>
<body class='pbody'>

<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $ini_array = parse_ini_file("/home/pi/pi-heating-hub/config/config.ini", true);
    
    $servername = $ini_array['db']['server'];
    $username =$ini_array['db']['user'];
    $password = $ini_array['db']['password'];
    $dbname = $ini_array['db']['database'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if ( array_key_exists( 'add', $_POST ) ) {
            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            // Check connection
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            $sql = "INSERT INTO schedules (name, start, end) VALUES ('new', '00:00:00', '23:59:59')";

            if (!mysqli_query($conn, $sql)) {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }

            mysqli_close($conn);
        }

        if ( array_key_exists( 'delete', $_POST ) ) {

            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            // Check connection
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            $SCHED_ID = $_POST["sched_id"];

            #echo $SCHED_ID;

            $sql = "DELETE FROM sched_device WHERE sched_id='".$SCHED_ID."';";
            if (!mysqli_query($conn, $sql)) {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
            $sql = "DELETE FROM sched_sensor WHERE sched_id='".$SCHED_ID."';";
            if (!mysqli_query($conn, $sql)) {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
            $sql = "DELETE FROM sched_mode WHERE sched_id='".$SCHED_ID."';";
            if (!mysqli_query($conn, $sql)) {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
            $sql = "DELETE FROM sched_network WHERE sched_id='".$SCHED_ID."';";
            if (!mysqli_query($conn, $sql)) {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
            $sql = "DELETE FROM sched_timer WHERE sched_id='".$SCHED_ID."';";
            if (!mysqli_query($conn, $sql)) {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
            $sql = "DELETE FROM schedules WHERE id='".$SCHED_ID."';";
            if (!mysqli_query($conn, $sql)) {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }

            mysqli_close($conn);
        }
    }

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $sql = "SELECT * FROM schedules order by name asc";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // output data of each row

        echo "<span class='ptitle'>Schedules</span><br><br>";

        echo "<table class='ttab' ><tr>";
        echo "<th class='tcol'><span class='tspan'>Name</span></th>";
        echo "<th width=1%><span class='tspan'>Start Time</span></th>";
        echo "<th width=1%><span class='tspan'>End Time</span></th>";
        echo "<th width=1%><span class='tspan'>Repeat</span></th>"; 
        echo "<th width=1%><span class='tspan'>Status</span></th>";
        echo "<th width=1%></th>";
        echo "<th width=1%></th>";
        echo "</tr>";

        while($row = mysqli_fetch_assoc($result)) {
            $SCHED_ID = $row["id"];
            $SCHED_NAME = $row["name"];
            $SCHED_ACTIVE = $row["active"];
            $SCHED_START = $row["start"];
            $SCHED_END = $row["end"];

            $SCHED_DOW1 = (bool)$row["dow1"]; if ( $SCHED_DOW1 ) { $SCHED_DOW1_CHK = 'checked="checked"'; }else{ $SCHED_DOW1_CHK = ''; }
            $SCHED_DOW2 = (bool)$row["dow2"]; if ( $SCHED_DOW2 ) { $SCHED_DOW2_CHK = 'checked="checked"'; }else{ $SCHED_DOW2_CHK = ''; }
            $SCHED_DOW3 = (bool)$row["dow3"]; if ( $SCHED_DOW3 ) { $SCHED_DOW3_CHK = 'checked="checked"'; }else{ $SCHED_DOW3_CHK = ''; }
            $SCHED_DOW4 = (bool)$row["dow4"]; if ( $SCHED_DOW4 ) { $SCHED_DOW4_CHK = 'checked="checked"'; }else{ $SCHED_DOW4_CHK = ''; }
            $SCHED_DOW5 = (bool)$row["dow5"]; if ( $SCHED_DOW5 ) { $SCHED_DOW5_CHK = 'checked="checked"'; }else{ $SCHED_DOW5_CHK = ''; }
            $SCHED_DOW6 = (bool)$row["dow6"]; if ( $SCHED_DOW6 ) { $SCHED_DOW6_CHK = 'checked="checked"'; }else{ $SCHED_DOW6_CHK = ''; }
            $SCHED_DOW7 = (bool)$row["dow7"]; if ( $SCHED_DOW7 ) { $SCHED_DOW7_CHK = 'checked="checked"'; }else{ $SCHED_DOW7_CHK = ''; }

            echo "<tr>";

            echo "<td class='dcolname' ><span class='dspan'>$SCHED_NAME</span></td>";
            echo "<td class='dcolname' ><span class='dspan'>$SCHED_START</span></td>";
            echo "<td class='dcolname' ><span class='dspan'>$SCHED_END</span></td>";

            echo "<td>";

            $DOT_SIZE='12.6';
            echo "<span class='dspan'>MTWTFSS</span><br>";
            if ( $SCHED_DOW1 ) { echo "<img src='/images/dot-green.png' alt='Schedule Active' height='$DOT_SIZE' width='$DOT_SIZE'>"; }else{ echo "<img src='/images/dot-red.png' alt='Schedule Inactive' height='$DOT_SIZE' width='$DOT_SIZE'>"; }
            if ( $SCHED_DOW2 ) { echo "<img src='/images/dot-green.png' alt='Schedule Active' height='$DOT_SIZE' width='$DOT_SIZE'>"; }else{ echo "<img src='/images/dot-red.png' alt='Schedule Inactive' height='$DOT_SIZE' width='$DOT_SIZE'>"; }
            if ( $SCHED_DOW3 ) { echo "<img src='/images/dot-green.png' alt='Schedule Active' height='$DOT_SIZE' width='$DOT_SIZE'>"; }else{ echo "<img src='/images/dot-red.png' alt='Schedule Inactive' height='$DOT_SIZE' width='$DOT_SIZE'>"; }
            if ( $SCHED_DOW4 ) { echo "<img src='/images/dot-green.png' alt='Schedule Active' height='$DOT_SIZE' width='$DOT_SIZE'>"; }else{ echo "<img src='/images/dot-red.png' alt='Schedule Inactive' height='$DOT_SIZE' width='$DOT_SIZE'>"; }
            if ( $SCHED_DOW5 ) { echo "<img src='/images/dot-green.png' alt='Schedule Active' height='$DOT_SIZE' width='$DOT_SIZE'>"; }else{ echo "<img src='/images/dot-red.png' alt='Schedule Inactive' height='$DOT_SIZE' width='$DOT_SIZE'>"; }
            if ( $SCHED_DOW6 ) { echo "<img src='/images/dot-green.png' alt='Schedule Active' height='$DOT_SIZE' width='$DOT_SIZE'>"; }else{ echo "<img src='/images/dot-red.png' alt='Schedule Inactive' height='$DOT_SIZE' width='$DOT_SIZE'>"; }
            if ( $SCHED_DOW7 ) { echo "<img src='/images/dot-green.png' alt='Schedule Active' height='$DOT_SIZE' width='$DOT_SIZE'>"; }else{ echo "<img src='/images/dot-red.png' alt='Schedule Inactive' height='$DOT_SIZE' width='$DOT_SIZE'>"; }

            echo "</td>";

            if ( $SCHED_ACTIVE ) {
                echo "<td class='dcolstatus' ><img src='/images/dot-green.png' alt='Schedule Active' height='32' width='32'></td>";
            } else {
                echo "<td class='dcolstatus' ><img src='/images/dot-red.png' alt='Schedule Inactive' height='32' width='32'></td>";
            }

            echo "</td>";

            echo "<td>";
            echo "<input type='button' onclick='location.href=\"/sched-edit.php?id=$SCHED_ID\";' value='Edit' class='bblue' />";
            echo "</td>";

            echo "<td><form method='post' action='/sched-list.php'>";
            echo "<input type='hidden' name='sched_id' value='".$SCHED_ID."' />";
            echo "<input type='submit' name='delete' value='Delete' class='bred' /></form></td>";
            echo "</tr>";       

            echo "</tr>";
        }    

        echo "</table>";

    } else {
        echo "0 results";
    }

    mysqli_close($conn);
?>

<form method='post' action='sched-list.php'>
<input type='submit' name='add' value='Add new' class='bgreen' />
<input type='button' onclick='location.href="/status.php";' value='Done' class='bgrey' />
</form>
    
</body>
</html>
