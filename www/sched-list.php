<!DOCTYPE HTML>  
<html>
<head>
<style>
.error {color: #FF0000;}
.tcolname {font-family: arial; color: black; font-size: xx-large;}
.ccolname {font-family: arial; color: black; font-size: large;}
.ccoldowun {font-family: courier; color: black; font-size: large;}
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
  
    echo "<table><tr><th>Status</th><th><span class='tcolname'>Schedule Name</span></th><th>Start Time</th><th>End Time</th><th>Repeat<br>MTWTFSS</th><th>Status</th><th></th><th></th></tr>";
  
    while($row = mysqli_fetch_assoc($result)) {
        $SCHED_ID = $row["id"];
        $SCHED_NAME = $row["name"];
        $SCHED_STATUS = $row["value"];
        $SCHED_START = $row["start"];
        $SCHED_END = $row["end"];
      
        echo "<tr>";
        if ( $SCHED_STATUS ) {
            echo "<td><img src='/images/dot-red.png' alt='Red' height='16' width='16'></td>";
        } else {
            echo "<td><img src='/images/dot-green.png' alt='Red' height='16' width='16'></td>";
        }
        
        echo "<td><span class='ccolname'>".$SCHED_NAME.$SCHED_STATUS."</span></td>";
        echo "<td><span class='ccolstart'>".$SCHED_START."</span></td>";
        echo "<td><span class='ccolend'>".$SCHED_END."</span></td>";
      
        echo "<td><span class='ccoldowun'>";

        echo str_pad(decbin($row["dow"]), 7, "0", STR_PAD_LEFT);
      
        for ($i=1; $i<8; $i++) {
            //echo $i;
        }
        echo "</span></td>";
      
        echo "<td><span class='ccolvalue'>".$SCHED_STATUS."</span></td>";
    
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
