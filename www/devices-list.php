<!DOCTYPE HTML>  
<html>
<head>
<meta http-equiv="refresh" content="30">
<style>
.error {color: #FF0000;}
.tcolname {font-family: arial; color: grey; font-size: x-large;}
.ccolname {font-family: arial; color: grey; font-size: large;}
.ccoldowun {font-family: arial; color: grey; font-size: x-small;}
.ccoldowse {font-family: arial; color: grey; font-size: large;}
</style>
</head>
<body bgcolor='#080808'>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$servername = "localhost";
$username = "pi";
$password = "password";
$dbname = "pi_heating_db";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    print_r("------------------------");
    print_r($_POST);
    print_r("------------------------");
    print_r($_GET);
    print_r("------------------------");
    
    if ( array_key_exists( 'done', $_POST ) ) {
        header('Location: /status.php');
        exit();
    }
   
    if ( array_key_exists( 'add', $_POST ) ) {
        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
            }
        $sql = "INSERT INTO devices (name, pin, active_level, value) VALUES ('new', null, 0, null, 0)";
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
        
        $DEVICE_ID = $_POST["device_id"];
        
        #echo $SCHED_ID;
        
        $sql = "DELETE FROM sched_device WHERE device_id='".$DEVICE_ID."';";
        if (!mysqli_query($conn, $sql)) {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        $sql = "DELETE FROM devices WHERE id='".$DEVICE_ID."';";
        if (!mysqli_query($conn, $sql)) {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        mysqli_close($conn);
    }
    
    if ( array_key_exists( 'activate', $_POST ) ) {
    
        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
            }
        
        $DEVICE_ID = $_POST["device_id"];
        
        #echo $SCHED_ID;
        
        $sql = "UPDATE devices SET value='1' WHERE device_id='".$DEVICE_ID."';";
        if (!mysqli_query($conn, $sql)) {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        mysqli_close($conn);
    }
    
    if ( array_key_exists( 'deactivate', $_POST ) ) {
    
        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
            }
        
        $DEVICE_ID = $_POST["device_id"];
        
        #echo $SCHED_ID;
        
        $sql = "UPDATE devices SET value='0' WHERE device_id='".$DEVICE_ID."';";
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
$sql = "SELECT * FROM devices order by name asc";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
  
    echo "<table><tr><th></th><th></th><th>Status</th><th><span class='tcolname'>Device Name</span></th><th>Start Time</th><th>End Time</th><th>Repeat</th></tr>";
  
    while($row = mysqli_fetch_assoc($result)) {
        $DEVICE_ID = $row["d_id"];
        $DEVICE_NAME = $row["name"];
        $DEVICE_PIN = $row["pin"];
        $DEVICE_ACTIVE_LEVEL = $row["active_level"];
        $DEVICE_VALUE = $row["value"];
        
        echo "<tr>";
        echo "<td><form method='post' action='/device-edit.php?id=".$DEVICE_ID."'>";
        echo "<input type='submit' name='edit' value='Edit'></form></td>";
        
        echo "<td><form method='post' action='/delices-list.php'>";
        echo "<input type='hidden' name='device_id' value='".$DEVICE_ID."'>";
        echo "<input type='submit' name='delete' value='Delete'></form></td>";
        
        if ( $DEVICE_VALUE ) {
            echo "<td><form method='post' action='/devices-list.php'>";
            echo "<input type='hidden' name='device_id' value='".$DEVICE_ID."'>";
            echo "<input type='submit' name='deactivate' value='Deactivate'></form></td>";
            echo "<td><img src='/images/dot-green.png' alt='Schedule Active' height='16' width='16'></td>";
        } else {
            echo "<td><form method='post' action='/devices-list.php'>";
            echo "<input type='hidden' name='device_id' value='".$DEVICE_ID."'>";
            echo "<input type='submit' name='activate' value='Activate'></form></td>";
            echo "<td><img src='/images/dot-red.png' alt='Schedule Inactive' height='16' width='16'></td>";
        }
        
        echo "<td><span class='ccolname'>".$DEVICE_NAME."</span></td>";
        echo "<td><span class='ccolstart'>".$DEVICE_PIN."</span></td>";
        echo "<td><span class='ccolend'>".$DEVICE_ACTIVE_LEVEL."</span></td>";
      
        echo "</tr>";
    }    
  
    echo "</table>";
  
} else {
    echo "0 results";
}
  
mysqli_close($conn);
?>  

<form method='post' action='devices-list.php'>
<input type='submit' name='add' value='Add new'>
<input type="submit" name="done" value="Done" />
</form>
  
</body>
</html>
