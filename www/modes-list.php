<!DOCTYPE HTML>

<html><head>
<meta http-equiv="refresh" content="30">
<style>
.error {color: #FF0000;}
.tab {font-family: arial; color: blue; font-size: xx-large;}

table {
    width: 100%;
}
th {
    padding: 12px;
    background-color: #202020;
    font-family: arial; color: blue; font-size: xx-large;
}
td {
    padding: 10px;
    background-color: #101010;
    color: #808080;
}
.col-1 {
  width: 50%;
  text-align: center;
}
.col-1-txt {
  font-family: arial; color: #808080; font-size: large;
  text-align: left;
}
.col-2 {
  width: 50%;
  text-align: center;
}
.col-2-txt {
  font-family: arial; color: #808080; font-size: x-large;
  text-align: center;
}
.col-3 {
  width: 5%;
}
.col-but {
  width: 5%;
  text-align: center;
}
.button {
    padding: 2px;
    float: left;
    background-color: #4CAF50; /* Green */
    border: 2px solid green
}
.button:hover {
    background-color: #3e8e41;
}
</style>
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

  #echo '<th>ID</th>';
  echo "<th class='col-1'>NAME</th>";
  echo "<th class='col-2'>VALUE</th>";
  echo "<th class='col-but'></th><th class='col-but'></th>";
    
  while($row = mysqli_fetch_assoc($result)) {
      
    $id = $row["id"];
    $name = $row["name"];
    $value = $row["value"];
 
    echo '<tr>';

    #echo '<td>';
    #echo $id;
    #echo '</td>';
    
    echo "<td class='col-1-txt'>";
    echo $name;
    echo '</td>';

    echo "<td class='col-2-txt'>";
    echo $value;
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
<input type='submit' class='buttonX' name='add' value='Add new'>
</form>
    
</font>
</div>
</body>
</html>
