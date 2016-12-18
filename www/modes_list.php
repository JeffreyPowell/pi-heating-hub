<!DOCTYPE HTML>

<html><head>
<meta http-equiv="refresh" content="30">
</head><body bgcolor='#080808'>
<font color='#808080' size ='4' face='verdana'>
    
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    
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
 
echo "<font color='#808080' size ='9' face='verdana'>Modes</font>";
echo "<div align='center'>";
$sql = "SELECT * FROM modes;";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
  echo '<table>';
    
  echo '<th>ID</th><th>NAME</th><th>VALUE</th><th></th><th></th>';
  while($row = mysqli_fetch_assoc($result)) {
      
    $id = $row["id"];
      
    echo '<tr>';

    echo '<td>';
    echo $row["id"];
    echo '</td>';
    
    echo '<td>';
    echo $row["name"];
    echo '</td>';

    echo '<td>';
    echo $row["value"];
    echo '</td>';

    echo '<td>';
    echo "<form method='post' action='/modes-edit.php?id=".$id."'>";
    echo "<input type='submit' name='edit' value='Edit'></form>";
    echo '</td>';
  
    echo '<td>';
    echo "<form method='post' action='/modes-delete.php?id=".$id."'>";
    echo "<input type='submit' name='delete' value='Delete'></form>";
    echo '</td>';
      
    echo '</tr>';
  }
  echo "</table>";
    
}

?>

<form method='post' action='mode-new.php'>
<input type='submit' name='add' value='Add new'>
</form>
    
</font>
</div>
</body>
</html>
