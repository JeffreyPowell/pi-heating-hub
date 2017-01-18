<!DOCTYPE HTML>  
<html>
<head>
<meta http-equiv="refresh" content="30">
<style>
    .sensorvalue {font-family: courier; color: green; font-size:300px;}
    .sensorvaluedec {font-family: courier; color: green; font-size:100px;}
    .sensorname {font-family: courier; color: green; font-size:40px;}
    .pbody { background-color: #080808; font-family: courier; color: red; font-size: small;}
    .debug { font-family: courier; color: red; font-size: large; }
    .error { color: #FF0000; }
    .ttab  { width: 100%; }
    .tcol  { font: 22px arial; }
    .tspan { font: 22px arial; color: grey; }
    .dcolname   { text-align: left; padding: 0 0 0 32px; }
    .dcolstatus { text-align: center; }
    .dspan { font-family: arial; color: grey; font-size: large; display: inline-block; }
    .ptitle { font: bold 32px arial; color: blue; }
    .itextbox { font-family: arial; color: grey; font-size: large; padding: 12px 20px; margin: 8px 30px; width: 80%; }
    .bgrey {  background-color: grey;  border: none; color: white; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial; margin: 12px ; }
    .bblue {  background-color: blue;  border: none; color: white; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial; margin: 12px ; }
    .bgreen { background-color: green; border: none; color: white; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial; margin: 12px ; }
    .bred {   background-color: red;   border: none; color: white; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial; margin: 12px ; }
    table, th, td { border: 5px solid #080808; }
    th, td {  background-color: #1a1a1a; }
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

$img_dir = 'images/chart-status-';
$rrd_dir = '/home/pi/pi-heating-hub/data/s-';
    
#if ( $SENSOR_ID < 1 ) { $SENSOR_ID = 1; }

    
// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("<br><br>Connection failed: " . mysqli_connect_error());
    }

$sql_sensors = "SELECT min(id) AS id FROM sensors;";
$result_sensors = mysqli_query($conn, $sql_sensors);
if (mysqli_num_rows($result_sensors) == 0) {
    echo "0 sensors results"; 
    }
while($row = mysqli_fetch_assoc($result_sensors)) {
    $SENSOR_MAX_ID = $row["id"];
    }

$SENSOR_ID = isset($_GET['sid']) ? $_GET['sid'] : $SENSOR_MAX_ID;
$GRAPH_ID = isset($_GET['gid']) ? $_GET['gid'] : $SENSOR_MAX_ID;
$GRAPH_SP = isset($_GET['gsp']) ? $_GET['gsp'] : '-1h';
    
/*    
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["formSubmit"] == "Done" ) {
    header('Location: /sched-list.php');
    exit();
    }
*/
    
if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
    #print_r("<pre><BR>------------------------<BR>");
    #print_r($_POST);
    #print_r("<BR>------------------------<BR></pre>");
    
    $POST_KEYS = array_keys($_POST);
    
    foreach( $POST_KEYS as $POST_KEY ) { 
        #echo $POST_KEY."<BR>";
        $POST_ACTION = explode( '-', $POST_KEY )[0];
        $POST_TYPE = explode( '-', $POST_KEY )[1];
        $POST_TARGET = explode( '-', $POST_KEY )[2];
        #echo $POST_ACTION."#".$POST_TYPE."#".$POST_TARGET."<BR>";
        
        if ( $POST_TYPE == 'mode' ) {
            if ( $POST_ACTION == 'enable' ) { $VALUE='1';} else {$VALUE='0';}
            $sql = "UPDATE modes SET value = '".$VALUE."' WHERE id = '".$POST_TARGET."';";
            #echo $sql;
            if (!mysqli_query($conn, $sql)) {
                echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
        if ( $POST_TYPE == 'timer' ) {
            if ( $POST_ACTION == 'start' ) { 
                $sql = "UPDATE timers SET value = duration WHERE id = '".$POST_TARGET."';";
            } else {
                $sql = "UPDATE timers SET value = '0' WHERE id = '".$POST_TARGET."';";
            }
            #echo $sql;
            if (!mysqli_query($conn, $sql)) {
                echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }  
        if ( $POST_ACTION == 'gid' ) {
            $GRAPH_ID = isset($_POST['gid']) ? $_POST['gid'] : '1';
            $GRAPH_SP = isset($_POST['gsp']) ? $_POST['gsp'] : '-1h';
            
            #$page = 'status.php?sid='.$SENSOR_ID.'&gid='.$GRAPH_ID.'&gsp='.$GRAPH_SP;
            $page = 'status.php?sid='.$GRAPH_ID.'&gid='.$GRAPH_ID.'&gsp='.$GRAPH_SP;
            #echo $page;
            header('Location: '.$page);
            exit();
            

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
    
echo "<a href='sched-list.php'>Schedules</a>";
echo "<br><br>";
    
echo "<a href='sensors-list.php'>Input Sensors</a>";
echo "<br><br>";
    
echo "<a href='devices-list.php'>Output Devices</a>";
echo "<br><br>";
    
echo "<a href='modes-list.php'>Modes</a>";
echo "<br><br>";
    
echo "<a href='timers-list.php'>Timers</a>";
echo "<br><br>";
    
echo "<a href='netdevices-list.php'>Connected Devices</a>";
echo "<br><br>";
/*
while($row = mysqli_fetch_assoc($result_modes)) {
    $MODE_ID = $row["id"];
    $MODE_NAME = $row["name"];
    $MODE_VALUE = $row["value"];
    #echo $MODE_NAME;
    #echo $MODE_VALUE;
    echo "<br>";
    echo "<form name='modes' method='post' action='status.php?sid=".$SENSOR_ID."&gid=".$GRAPH_ID."&gsp=".$GRAPH_SP."'>";
    if ( $MODE_VALUE =='0' ) {
        echo "<input type='submit' class='button' name='enable-mode-".$MODE_ID."' value='Enable ".$MODE_NAME." mode'>";
    }else{
        echo "<input type='submit' class='button' name='disable-mode-".$MODE_ID."' value='Disable ".$MODE_NAME." mode'>";
    }
    echo "</form>";
    }
*/

echo "</td>";

    
echo "<td width=33%>";
    
while($row = mysqli_fetch_assoc($result_sensor)) {
    $SENSOR_NAME =  $row["name"];
    $SENSOR_VALUE = $row["value"];
    }
    
echo "<span class='sensorname'>".$SENSOR_NAME."</span><br>";
    
if( $SENSOR_VALUE == '' ) {
    #echo "#$SENSOR_VALUE#";
    echo "<span class='sensorvalue'>--</span>"; 
    echo "<span class='sensorvaluedec'>.-</span>"; 
} else {
    #echo "#$SENSOR_VALUE#";
    echo "<span class='sensorvalue'>"; 
    echo explode( ".", number_format($SENSOR_VALUE, 1, '.', ''))[0];
    echo "</span>";
    echo "<span class='sensorvaluedec'>."; 
    echo explode( ".", number_format($SENSOR_VALUE, 1, '.', ''))[1];
    echo "</span>";
}
echo "</td>";

echo "<td width=33%>";
    
echo '<form id="formTimers" method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'?sid='.$SENSOR_ID.'&gid='.$GRAPH_ID.'&gsp='.$GRAPH_SP.'">';

while($row = mysqli_fetch_assoc($result_modes)) {
    $MODE_ID = $row["id"];
    $MODE_NAME = $row["name"];
    $MODE_VALUE = $row["value"];
    #echo $MODE_NAME;
    #echo $MODE_VALUE;
    echo "<br>";
    echo "<form name='modes' method='post' action='status.php?sid=".$SENSOR_ID."&gid=".$GRAPH_ID."&gsp=".$GRAPH_SP."'>";
    if ( $MODE_VALUE =='0' ) {
        echo "<input type='submit' class='button' name='enable-mode-".$MODE_ID."' value='Enable ".$MODE_NAME." mode'>";
    }else{
        echo "<input type='submit' class='button' name='disable-mode-".$MODE_ID."' value='Disable ".$MODE_NAME." mode'>";
    }
    echo "</form>";
    }

while($row = mysqli_fetch_assoc($result_timers)) {
    $TIMER_ID = $row["id"];
    $TIMER_NAME = $row["name"];
    $TIMER_VALUE = $row["value"];
    #echo $TIMER_ID;
    #echo $TIMER_NAME;
    #echo $TIMER_VALUE;
    
    echo "<br>";
    echo "<form name='modes' method='post' action='status.php?sid=".$SENSOR_ID."&gid=".$GRAPH_ID."&gsp=".$GRAPH_SP."'>";
    if ( $TIMER_VALUE =='0' ) {
        echo "<input type='submit' class='button' name='start-timer-".$TIMER_ID."' value='Start ".$TIMER_NAME." timer'>";
    }else{
        echo "<input type='submit' class='button' name='stop-timer-".$TIMER_ID."' value='Stop ".$TIMER_NAME." timer [ ".$TIMER_VALUE." min ]'>";
    }
    echo "</form>";
    }

echo "</td>";

echo "</tr>";
    
echo "</table>";
    
echo '<td>';

echo "<table width='100%' border='1'>";
echo "<tr>";

echo "<td width=100% align=center>";
    

$sql = "SELECT * FROM sensors;";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
        echo "sensors 0 results"; 
    }

echo "<form name='graph' method='post' action='status.php?sid=".$SENSOR_ID."&gid=".$GRAPH_ID."&gsp=".$GRAPH_SP."'>";


echo '<select name="gid">';
    
while($row = mysqli_fetch_assoc($result)) {

    $SENSOR_NAME = $row["name"];
    $SENSOR_ID = $row["id"];
    
    if ( $SENSOR_ID == $GRAPH_ID ) { $SELECTED = 'selected'; }else{ $SELECTED = ''; }

    echo '<option value="'.$SENSOR_ID.'" '.$SELECTED.' >'.$SENSOR_NAME.'</option>';
    
    }
    
echo '</select>';
    
echo '<select name="gsp">';
echo '<option value="-1h" '.($GRAPH_SP=='-1h' ? 'SELECTED' : '').' >One Hour</option>';
echo '<option value="-2h" '.($GRAPH_SP=='-2h' ? 'SELECTED' : '').' >Two Hours</option>';
echo '<option value="-6h" '.($GRAPH_SP=='-6h' ? 'SELECTED' : '').' >Six Hours</option>';
echo '<option value="-12h" '.($GRAPH_SP=='-12h' ? 'SELECTED' : '').' >Twelve Hours</option>';
echo '<option value="-24h" '.($GRAPH_SP=='-24h' ? 'SELECTED' : '').' >One Day</option>';
echo '<option value="-3d" '.($GRAPH_SP=='-3d' ? 'SELECTED' : '').' >Three Days</option>';
echo '<option value="-7d" '.($GRAPH_SP=='-7d' ? 'SELECTED' : '').' >One Week</option>';
echo '<option value="-1m" '.($GRAPH_SP=='-1m' ? 'SELECTED' : '').' >One Month</option>';
echo '<option value="-3m" '.($GRAPH_SP=='-3m' ? 'SELECTED' : '').' >Three Months</option>';
echo '<option value="-6m" '.($GRAPH_SP=='-6m' ? 'SELECTED' : '').' >Six Months</option>';
echo '<option value="-12m" '.($GRAPH_SP=='-12m' ? 'SELECTED' : '').' >One Year</option>';
echo '</select>';

echo '<input type="submit" name="formSubmit" value="Update" />';
    
echo '</form>';

#echo '<br>';
    
#echo "</td>";

#echo "<td width=66%>";
    
create_graph( $rrd_dir.$GRAPH_ID.".rrd", $img_dir.$GRAPH_ID.$GRAPH_SP.".png", 	$GRAPH_SP, 	$row["name"],	 	   "180", "700");
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
