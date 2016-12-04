<!DOCTYPE HTML>  
<html>
<head>
<style>
.error {color: #FF0000;}
.tcolname {font-family: arial; color: black; font-size: xx-large;}
.ccolname {font-family: arial; color: black; font-size: large;}
.ccoldowun {font-family: arial; color: black; font-size: large;}
.ccoldowse {font-family: arial; color: black; font-size: large;}
</style>
</head>
<body>  

<?php
$servername = "localhost";
$username = "pi";
$password = "password";
$dbname = "pi_heating_db";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "SELECT * FROM schedules";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
  
  echo "<table><tr><th><span class='tcolname'>Schedule Name</span></th><th>Start Time</th><th>End Time</th><th>Repeat</th><th>Status</th><th></th><th></th></tr>";
  
  while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td><span class='ccolname'>".$row["name"]."</span></td>";
    echo "<td><span class='ccolstart'>".$row["start"]."</span></td>";
    echo "<td><span class='ccolend'>".$row["end"]."</span></td>";
      
    echo "<td><span class='ccoldow'>";
    sprintf( "%07d", decbin($row["dow"]));

    for ($i=1; $i<8; $i++) {
      echo $i;
      }
    echo "</span></td>";
      
    echo "<td><span class='ccolvalue'>".$row["value"]."</span></td>";
    
    echo "<td><form method='post' action='sched-edit.php?id=".$row["id"]."'>";
    echo "<input type='submit' name='edit' value='Edit'></form></td>";
    echo "<td><form method='post' action='sched-delete.php?id=".$row["id"]."'>";
    echo "<input type='submit' name='delete' value='Delete'></form></td>";
    echo "</tr>";
  }    
  
  echo "</table>";
  
} else {
    echo "0 results";
}
  
mysqli_close($conn);
?>  

<form method='post' action='sched-new.php'>
<input type='submit' name='add' value='Add new'>
</form>
  
</body>
</html>
