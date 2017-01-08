<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="refresh" content="30">
<style>
.error {color: #FF0000;}
.tcolname {font-family: arial; color: black; font-size: xx-large;}
.ccolname {font-family: arial; color: black; font-size: large;}
.ccoldowun {font-family: courier; color: darkgrey; font-size: x-small;}
.ccoldowse {font-family: courier; color: black; font-size: large;}
</style>
</head>
<body bgcolor='#080808'>
<font color='#808080' size ='4' face='verdana'>
    
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    
$servername = "localhost";
$username = "pi";
$password = "password";
$dbname = "pi_heating_db";

    
///////////////////////////////////////////////////////////

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    print_r("------------------------");
    print_r($_POST);
    print_r("------------------------");
    print_r($_GET);
    print_r("------------------------");
    
    if ( $_POST["done"] == "Done" ) {
        header('Location: /status.php');
        exit();
    }
   
    if ( $_POST["add"] == "Add new" ) {
        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
            }

        $sql = "INSERT INTO sensors (ip, ref) VALUES ('000.000.000.000', '0')";

        if (!mysqli_query($conn, $sql)) {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        mysqli_close($conn);
    }
    
    if ( $_POST["delete"] == "Delete" ) {
    
        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
            }
        
        $SENSOR_ID = $_POST["sensor_id"];
        
        #echo $SCHED_ID;
        
        $sql = "DELETE FROM sched_sensor WHERE sensor_id='".$SENSOR_ID."';";
        if (!mysqli_query($conn, $sql)) {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        $sql = "DELETE FROM sensors WHERE id='".$SENSOR_ID."';";
        if (!mysqli_query($conn, $sql)) {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        mysqli_close($conn);
    }
}
///////////////////////////////////////////////////////////
    
// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
 
echo "<font color='#808080' size ='9' face='verdana'>Sensors</font>";
echo "<div align='center'>";

$sql = "SELECT * FROM sensors;";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {

  echo '<table>';

  while($row = mysqli_fetch_assoc($result)) {
      
    #$id = $row["id"];
    #$name = $row["name"];
      
    $SENSOR_ID = $row["id"];
    $SENSOR_NAME = $row["name"];
      
    $img_dir = 'images/chart-sensor-';
    $rrd_dir = '/home/pi/pi-heating-hub/data/s-';

    echo '<tr>';
      
    echo '<td>';
    echo "<form method='post' action='sensor-edit.php?id=".$SENSOR_ID."'>";
    echo "<input type='submit' name='edit' value='Edit'></form>";
    echo '</td>';

    echo '<td>';
    echo "<form method='post' action='sensors-list.php'>";
    echo "<input type='hidden' name='sensor_id' value='".$SENSOR_ID."'>";
    echo "<input type='submit' name='delete' value='Delete'></form>";
    echo '</td>';
      
    echo '<td>';
    $span = "-12h";
    create_graph( $rrd_dir.$SENSOR_ID.".rrd", $img_dir.$SENSOR_ID.$span.".png", 	$span, 	$row["name"]." last 12 hours",	 	   "60", "400");
    echo "<img src='".$img_dir.$SENSOR_ID.$span.".png' alt='RRD image'>";
    echo '</td>';
      
    #echo '<td>';
    #$span = "-7d";
    #create_graph( $rrd_dir.$SENSOR_ID.".rrd", $img_dir.$SENSOR_ID.$span.".png", 	$span, 	$row["name"]." last 7 days",	 	   "120", "300");
    #echo "<img src='".$img_dir.$SENSOR_ID.$span.".png' alt='RRD image'>";
    #echo '</td>';
  
    #echo '<td>';
    #$span = "-90d";
    #create_graph( $rrd_dir.$id.".rrd", $img_dir.$id.$span.".png", 	$span, 	$row["name"]." last 3 months",	 	   "120", "200");
    #echo "<img src='".$img_dir.$id.$span.".png' alt='RRD image'>";
    #echo '</td>';
      
    echo '</tr>';

  }

  echo "</table>";
    
}


mysqli_close($conn);

//exit;


function create_graph($rrdfile, $output, $start, $title, $height, $width) {
    
  $options = array(
    "--slope-mode",
    "--start", $start,
    "--title=$title",
    "--vertical-label=Temperature",
#    "--lower=0",
    "--height=$height",
    "--width=$width",
    "-cBACK#161616",
    "-cCANVAS#1e1e1e",
    "-cSHADEA#000000",
    "-cSHADEB#000000",
    "-cFONT#c7c7c7",
    "-cGRID#888800",
    "-cMGRID#ffffff",
    "-nTITLE:10",
    "-nAXIS:12",
    "-nUNIT:10",
    "-y 1:5",
    "-cFRAME#ffffff",
    "-cARROW#000000",
    "DEF:callmax=$rrdfile:data:MAX",
    "CDEF:transcalldatamax=callmax,1,*",
    "AREA:transcalldatamax#a0b84240",
    "LINE4:transcalldatamax#a0b842",
#    "LINE4:transcalldatamax#a0b842:Calls",
#    "COMMENT:\\n",
#    "GPRINT:transcalldatamax:LAST:Calls Now %6.2lf",
#    "GPRINT:transcalldatamax:MAX:Data %6.2lf"
    "COMMENT:\\n"
  );

 $ret = rrd_graph( $output, $options );

  if (! $ret) {
    echo "<b>Graph error: </b>".rrd_error()."\n";
  }
}


?>
    
<form method='post' action='sensors-list.php'>
<input type='submit' name='add' value='Add new'>
<input type="submit" name="done" value="Done" />
</form>

</font>
</body>
</html>
