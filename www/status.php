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


$sql = "SELECT * FROM modes LEFT JOIN sched_mode ON modes.id=sched_mode.mode_id AND sched_mode.sched_id=".$SCHED_ID.";";
$result_modes = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
        echo "sensors 0 results"; 
    }

    
echo '<br><br>';
    
    
  
echo "<table width='100%' border='1'>";
echo "<tr>";

echo "<td width=33%>";
    


while($row = mysqli_fetch_assoc($result_modes)) {
    echo ''.$row["name"].'';
    $MODE_OPP = $row["opp"];
    if ( $MODE_OPP == "" )  { $NA_SELECTED = 'selected'; }else{ $NA_SELECTED = ''; }
    if ( $MODE_OPP == "<" ) { $LT_SELECTED = 'selected'; }else{ $LT_SELECTED = ''; }
    if ( $MODE_OPP == "=" ) { $EQ_SELECTED = 'selected'; }else{ $EQ_SELECTED = ''; }
    if ( $MODE_OPP == "!" ) { $NE_SELECTED = 'selected'; }else{ $NE_SELECTED = ''; }
    if ( $MODE_OPP == ">" ) { $GT_SELECTED = 'selected'; }else{ $GT_SELECTED = ''; }
    echo '<select name="mode_opp">';
    echo '<option value="na" '.$NA_SELECTED.' >(IS IGNORED)</option>';
    echo '<option value="lt" '.$LT_SELECTED.' >IS LESS THAN</option>';
    echo '<option value="eq" '.$EQ_SELECTED.' >IS EQUAL TO</option>';
    echo '<option value="ne" '.$NE_SELECTED.' >IS NOT EQUAL TO</option>';
    echo '<option value="gt" '.$GT_SELECTED.' >IS GREATER THAN</option>';
    echo '</select>';
    $MODE_VALUE = $row["value"];
    if ( $MODE_VALUE == "" ) { $NA_SELECTED = 'selected'; }else{ $NA_SELECTED = ''; }
    if ( $MODE_VALUE == "0" ) { $T_SELECTED = 'selected'; }else{ $T_SELECTED = ''; }
    if ( $MODE_VALUE == "1" ) { $F_SELECTED = 'selected'; }else{ $F_SELECTED = ''; }
    
    echo '<select name="mode_value">';
    echo '<option value="na" '.$NA_SELECTED.' >(IS IGNORED)</option>';
    echo '<option value="true" '.$T_SELECTED.' >ON</option>';
    echo '<option value="false" '.$F_SELECTED.' >OFF</option>';;
    echo '</select>';
    
    }


echo "</td>";

    
echo "<td width=33%>";
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

echo '<input type ="checkbox" name="cBox[]" value = "3" onchange="document.getElementById("formTimers").submit()">3</input>';
echo '<input type ="checkbox" name="cBox[]" value = "4" onchange="document.getElementById("formTimers").submit()">4</input>';
echo '<input type ="checkbox" name="cBox[]" value = "5" onchange="document.getElementById("formTimers").submit()">5</input>';

echo '<input type="submit" name="submit" value="Search" />';

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
