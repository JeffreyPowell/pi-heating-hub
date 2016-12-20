<!DOCTYPE HTML>  
<html>
<head>
<meta http-equiv="refresh" content="30">
<style>
.sensorvalue {font-family: courier; color: green; font-size:300px;}
.sensorvaluedec {font-family: courier; color: green; font-size:100px;}
.sensorname {font-family: courier; color: green; font-size:40px;}
.fixedsmall {font-family: courier; color: black; font-size: xx-small}
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
$GRAPH_ID = isset($_GET['gid']) ? $_GET['gid'] : '1';
$GRAPH_SP = isset($_GET['gsp']) ? $_GET['gsp'] : '-1h';

$img_dir = 'images/chart-status-';
$rrd_dir = '/home/pi/pi-heating-hub/data/s-';
    
if ( $SENSOR_ID < 1 ) { $SENSOR_ID = 1; }

    
// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("<br><br>Connection failed: " . mysqli_connect_error());
    }

    
    
    
/*    
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["formSubmit"] == "Done" ) {
    header('Location: /sched-list.php');
    exit();
    }
*/
    
if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
    print_r("<pre><BR>------------------------<BR>");
    print_r($_POST);
    print_r("<BR>------------------------<BR></pre>");
    
    if ( isset($_POST["modes"]) ) {        
        foreach( $_POST["devices"] as $DEVICE_ID ) { 
            $sql = "INSERT INTO sched_device ( sched_id, device_id ) VALUES ( ".$SCHED_ID.", ".$DEVICE_ID.");";
            if (!mysqli_query($conn, $sql)) {
                echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    
    
    
    
    
    
    
    
    
    
    
    
    }
}
    
    
    

$sql_modes = "SELECT * FROM modes;";
$result_modes = mysqli_query($conn, $sql_modes);
if (mysqli_num_rows($result_modes) == 0) {
    echo "0 modes results"; 
    }

$sql_sensor = "SELECT * from sensors WHERE id = '".$SENSOR_ID."';";
$result_sensor = mysqli_query($conn, $sql_sensor);
if (mysqli_num_rows($result_sensor) == 0) {
    echo "0 sensors results"; 
    }

$sql_timers = "SELECT * FROM timers;";
$result_timers = mysqli_query($conn, $sql_timers);
if (mysqli_num_rows($result_timers) == 0) {
    echo "0 timers results"; 
    }





echo '<br><br>';



echo "<table width='100%' border='1'>";
echo "<tr>";

echo "<td width=33%>";
    


while($row = mysqli_fetch_assoc($result_modes)) {
    $MODE_ID = $row["id"];
    $MODE_NAME = $row["name"];
    $MODE_VALUE = $row["value"];
    echo $MODE_NAME;
    echo $MODE_VALUE;
    echo "<br>";
    echo "<form name='modes' method='post' action='status.php?sid=".$SENSOR_ID."&gid=".$GRAPH_ID."&gsp=".$GRAPH_SP."'>";
    echo "<input type='submit' class='button' name='enable-".$MODE_ID."' value='Enable ".$MODE_NAME." mode'>";
    echo "<input type='submit' class='button' name='disable-".$MODE_ID."' value='Disable ".$MODE_NAME." mode'>";
    echo "</form>";
    }


echo "</td>";

    
echo "<td width=33%>";
    
while($row = mysqli_fetch_assoc($result_sensor)) {
    $SENSOR_NAME =  $row["name"];
    $SENSOR_VALUE = $row["value"];
    }
    
echo "<span class='sensorname'>".$SENSOR_NAME."</span><br>";

echo "<span class='sensorvalue'>"; 
echo substr(sprintf('%2.1f', $SENSOR_VALUE),0,2);
echo "</span>";
echo "<span class='sensorvaluedec'>"; 
echo substr(sprintf('%2.1f', $SENSOR_VALUE),-2,2);
echo "</span>";
    
echo "</td>";

echo "<td width=33%>";
    
echo '<form id="formTimers" method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'?sid='.$SENSOR_ID.'&gid='.$GRAPH_ID.'&gsp='.$GRAPH_SP.'">';

while($row = mysqli_fetch_assoc($result_timers)) {
    $TIMER_NAME = $row["name"];
    $TIMER_VALUE = $row["value"];
    echo $TIMER_NAME;
    echo $TIMER_VALUE;
    echo "<br>";

    }

echo '</form>';

echo "</td>";

echo "</tr>";
    
echo "</table>";
    
echo '<td>';

echo "<table width='100%' border='1'>";
echo "<tr>";

echo "<td width=33%>";
echo "</td>";

echo "<td width=66%>";
    
create_graph( $rrd_dir.$GRAPH_ID.".rrd", $img_dir.$GRAPH_ID.$GRAPH_SP.".png", 	$GRAPH_SP, 	$row["name"],	 	   "200", "800");
echo "<img src='".$img_dir.$GRAPH_ID.$GRAPH_SP.".png' alt='RRD image'>";  
    
echo "</td></tr>";
echo "</table>";
mysqli_close($conn);
    
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

</body>
</html>
