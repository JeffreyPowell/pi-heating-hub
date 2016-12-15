<!DOCTYPE HTML>

<html><head>
<meta http-equiv="refresh" content="30">
</head><body bgcolor='#080808'>
<font color='#808080' size ='4' face='verdana'>
    
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
 
echo "<font color='#808080' size ='9' face='verdana'>Sensors</font>";
echo "<div align='center'>";

$sql = "SELECT * FROM sensors;";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {

  echo '<table>';

  while($row = mysqli_fetch_assoc($result)) {

    echo '<tr><td>'.$row["name"].$row["id"].'<br>';
    $span = "-12h";
    
    create_graph( "home/pi/pi-heating-hub/data/s-".$row["id"].".rrd", "images/chart-sensor-".$row["id"].$span.".png", 	$span, 	$row["name"]." 12 hours",	 	   "200", "1100");

    echo "<img src='images/chart-sensor-".$row["id"].$span.".png' alt='RRD image'>";

    echo '</td></tr>';
      
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
    "--vertical-label=Calls",
    "--lower=0",
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
    "DEF:callmax=$rrdfile:callstot:MAX",
    "CDEF:transcalldatamax=callmax,1,*",
    "AREA:transcalldatamax#a0b84240",
    "LINE4:transcalldatamax#a0b842:Calls",
    "COMMENT:\\n",
#    "GPRINT:transcalldatamax:LAST:Calls Now %6.2lf",
    "GPRINT:transcalldatamax:MAX:Calls Max %6.2lf"
  );

 $ret = rrd_graph( $output, $options );

  if (! $ret) {
    echo "<b>Graph error: </b>".rrd_error()."\n";
  }
}


?>

</font>
</div>
</body>
</html>
