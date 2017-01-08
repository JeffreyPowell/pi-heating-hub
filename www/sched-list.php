<!DOCTYPE HTML>  
<html>
<head>
<meta http-equiv="refresh" content="30">
<style>
.error {color: #FF0000;}
.tcolname {font-family: arial; color: black; font-size: xx-large;}
.ccolname {font-family: arial; color: black; font-size: large;}
.ccoldowun {font-family: courier; color: black; font-size: small;}
.ccoldowse {font-family: courier; color: black; font-size: large;}
</style>
</head>
<body>  

<?php
$servername = "localhost";
$username = "pi";
$password = "password";
$dbname = "pi_heating_db";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    #print_r("------------------------");
    #print_r($_POST);
    #print_r("------------------------");
    #print_r($_GET);
    #print_r("------------------------");
   
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["add"] == "Add new" ) {
    
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
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["delete"] == "Delete" ) {
    
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
  
    echo "<table><tr><th>Status</th><th><span class='tcolname'>Schedule Name</span></th><th>Start Time</th><th>End Time</th><th>Repeat</th><th></th><th></th></tr>";
  
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
        if ( $SCHED_ACTIVE ) {
            echo "<td><img src='/images/dot-green.png' alt='Schedule Active' height='16' width='16'></td>";
        } else {
            echo "<td><img src='/images/dot-red.png' alt='Schedule Inactive' height='16' width='16'></td>";
        }
        
        echo "<td><span class='ccolname'>".$SCHED_NAME."</span></td>";
        echo "<td><span class='ccolstart'>".$SCHED_START."</span></td>";
        echo "<td><span class='ccolend'>".$SCHED_END."</span></td>";
      
        echo "<td><span class='ccoldowun'>";
        
        echo "<table><tr><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th></tr>";
        echo '<tr>';
        echo '<td><input type="checkbox" disabled="disabled" name="repeat_dow[]" value="dow1" '.$SCHED_DOW1_CHK.' /></td>';
        echo '<td><input type="checkbox" disabled="disabled" name="repeat_dow[]" value="dow2" '.$SCHED_DOW2_CHK.' /></td>';
        echo '<td><input type="checkbox" disabled="disabled" name="repeat_dow[]" value="dow3" '.$SCHED_DOW3_CHK.' /></td>';
        echo '<td><input type="checkbox" disabled="disabled" name="repeat_dow[]" value="dow4" '.$SCHED_DOW4_CHK.' /></td>';
        echo '<td><input type="checkbox" disabled="disabled" name="repeat_dow[]" value="dow5" '.$SCHED_DOW5_CHK.' /></td>';
        echo '<td><input type="checkbox" disabled="disabled" name="repeat_dow[]" value="dow6" '.$SCHED_DOW6_CHK.' /></td>';
        echo '<td><input type="checkbox" disabled="disabled" name="repeat_dow[]" value="dow7" '.$SCHED_DOW7_CHK.' /></td>';
        echo '</tr></table>';

        echo "</span></td>";

        echo "<td><form method='post' action='/sched-edit.php?id=".$SCHED_ID."'>";
        echo "<input type='submit' name='edit' value='Edit'></form></td>";
        echo "<td><form method='post' action='/sched-list.php'>";
        echo "<input type='hidden' name='sched_id' value='".$SCHED_ID."'>";
        echo "<input type='submit' name='delete' value='Delete'></form></td>";
        echo "</tr>";
    }    
  
    echo "</table>";
  
} else {
    echo "0 results";
}
  
mysqli_close($conn);
?>  

<form method='post' action='sched-list.php'>
<input type='submit' name='add' value='Add new'>
</form>
  
</body>
</html>
