<!DOCTYPE HTML>  
<html>
<head>
<meta http-equiv="refresh" content="30">
<style>
        .pbody { background-color: #080808; }
        .debug {font-family: courier; color: red; font-size: large;}
        .error {color: #FF0000;}
        .tcol {font: 22px arial; color: grey;}
        .dcol {font-family: arial; color: grey; font-size: large; text-align: center;}
        .ptitle {font: bold 32px arial; color: blue;}
        .itextbox {font-family: arial; color: grey; font-size: large; padding: 12px 20px; margin: 8px 30px; width: 80%;}
        .bgrey {  background-color: grey;  border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial;}
        .bblue {  background-color: blue;  border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial;}
        .bgreen { background-color: green; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial;}
        .bred {   background-color: red;   border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial;}
table, th, td {   border: 1px solid red; }
</style>
</head>
<body class='pbody'>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    
$servername = "localhost";
$username = "pi";
$password = "password";
$dbname = "pi_heating_db";
    
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    echo "<br><span class='debug'><br>------------------------<br>";
    #print_r( $_POST );
    #echo "<br>------------------------<br>";
    #print_r( $_GET );
    #echo "<br>------------------------<br></span><br>";
   
    
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
        $sql = "INSERT INTO devices (name, pin, active_level, value) VALUES ('new', null, 0, null)";
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
        
        $sql = "DELETE FROM sched_device WHERE device_id='$DEVICE_ID';";
        if (!mysqli_query($conn, $sql)) {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        $sql = "DELETE FROM devices WHERE d_id='$DEVICE_ID';";
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
        
        $sql = "UPDATE devices SET value='1' WHERE d_id='$DEVICE_ID';";
        
        if (!mysqli_query($conn, $sql)) {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        mysqli_commit($conn);
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
        
        $sql = "UPDATE devices SET value='0' WHERE d_id='$DEVICE_ID';";
        if (!mysqli_query($conn, $sql)) {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        mysqli_commit($conn);
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

    echo "<span class='ptitle'>Available Devices</span><br><br>";
    
    echo "<table width=100% ><tr><th width=1%></th><th width=1%></th>";
    echo "<th width=1%><span class='tcol'>Status</span></th>";
    echo "<th><span class='tcol'>Device Name</span></th>";
    #echo "<th><span class='tcol'>GPIO Pin</span></th>";
    #echo "<th><span class='tcol'>Active level</span></th>";
    echo "</tr>";
        
    while($row = mysqli_fetch_assoc($result)) {
        $DEVICE_ID = $row["d_id"];
        $DEVICE_NAME = $row["name"];
        $DEVICE_PIN = $row["pin"];
        $DEVICE_ACTIVE_LEVEL = $row["active_level"];
        $DEVICE_VALUE = $row["value"];
        
        #echo "<br><span class='debug'>$DEVICE_ID $DEVICE_NAME $DEVICE_PIN $DEVICE_ACTIVE_LEVEL $DEVICE_VALUE</span><br>";
        
        echo "<tr>";
        echo "<td><form method='post' action='/device-edit.php?id=".$DEVICE_ID."'>";
        echo "<input type='submit' name='edit' value='Edit' class='bblue'/></form></td>";
        
        echo "<td><form method='post' action='/devices-list.php'>";
        echo "<input type='hidden' name='device_id' value='".$DEVICE_ID."' />";
        echo "<input type='submit' name='delete' value='Delete' class='bred' /></form></td>";
        
        if ( $DEVICE_VALUE ) {
        #    echo "<td><form method='post' action='/devices-list.php'>";
        #    echo "<input type='hidden' name='device_id' value='".$DEVICE_ID."'>";
        #    echo "<input type='submit' name='deactivate' value='Deactivate'></form></td>";
            echo "<td style="text-align: center;"><img src='/images/dot-green.png' alt='Schedule Active' height='16' width='16'></td>";
        } else {
        #    echo "<td><form method='post' action='/devices-list.php'>";
        #    echo "<input type='hidden' name='device_id' value='".$DEVICE_ID."'>";
        #    echo "<input type='submit' name='activate' value='Activate'></form></td>";
            echo "<td style="text-align: center;"><img src='/images/dot-red.png' alt='Schedule Inactive' height='16' width='16'></td>";
        }
        
        echo "<td><span class='dcol'>".$DEVICE_NAME."</span></td>";
        #echo "<td><span class='dcol'>".$DEVICE_PIN."</span></td>";
        #echo "<td><span class='dcol'>".$DEVICE_ACTIVE_LEVEL."</span></td>";
      
        echo "</tr>";
    }    
  
    echo "</table>";
  
} else {
    echo "<span class='ptitle'>No Available Devices</span><br><br>";
}
  
mysqli_close($conn);
?>  
<br><br>
<form method='post' action='devices-list.php'>
<input type='submit' name='add' value='Add new' class='bgreen' />
<input type="submit" name="done" value="Done" class='bgrey'/>
</form>
  
</body>
</html>
