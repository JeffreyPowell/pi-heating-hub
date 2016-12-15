<!DOCTYPE HTML>  
<html>
<head>
<style>
.sensorvalue {font-family: courier; color: green; font-size:80px;}
.sensorname {font-family: courier; color: green; font-size:40px;}
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
    
$SENSOR_ID = isset($_GET['sid']) ? $_GET['sid'] : '1';
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

    
$sql = "SELECT * from sensors WHERE id = '".$SENSOR_ID."';";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
    echo "devices 0 results"; 
    }
  
while($row = mysqli_fetch_assoc($result)) {
    $SENSOR_NAME =  $row["name"];
    $SENSOR_VALUE = $row["value"];
    }
    
echo $SENSOR_NAME ;
    
printf('%2.1f', $SENSOR_VALUE);
sprintf('%2.1f', $SENSOR_VALUE);
    
echo '<br><br>';
    
    
  
echo "<table width='100%' border='1'>";
echo "<tr><td width=30%>1</td><td width=30%>";
echo "<span class='sensorname'>".$SENSOR_NAME."</span><br>";
echo "<span class='sensorvalue'>".$SENSOR_VALUE."</span></td>";
echo "<td width=30%>3</td></tr>";
echo "<tr><td width=30%>4</td><td width=30%>5</td><td width=30%>6</td></tr>";
echo "<tr><td width=30%>7</td><td width=30%>8</td><td width=30%>9</td></tr>";
echo "</table>";
    
    
    
mysqli_close($conn);
?>

</body>
</html>
