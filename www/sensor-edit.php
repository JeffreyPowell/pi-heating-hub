<!DOCTYPE HTML>

<html><head>
<meta http-equiv="refresh" content="30">
</head><body bgcolor='#080808'>
<font color='#808080' size ='4' face='verdana'>
    
<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    $ini_array = parse_ini_file("/home/pi/pi-heating-hub/config/config.ini", true);
    
    $servername = $ini_array['db']['server'];
    $username =$ini_array['db']['user'];
    $password = $ini_array['db']['password'];
    $dbname = $ini_array['db']['database'];
    
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
            echo '<tr>';

            echo '<td>';
            $span = "-24h";
            create_graph( "/home/pi/pi-heating-hub/data/s-".$row["id"].".rrd", "/var/www/pi-heating-hub/images/chart-sensor-".$row["id"].$span.".png", 	$span, 	$row["name"]." last 24 hours",	 	   "120", "500");
            echo "<img src='images/chart-sensor-".$row["id"].$span.".png' alt='RRD image'>";
            echo '</td>';

            echo '<td>';
            $span = "-7d";
            create_graph( "/home/pi/pi-heating-hub/data/s-".$row["id"].".rrd", "/var/www/pi-heating-hub/images/chart-sensor-".$row["id"].$span.".png", 	$span, 	$row["name"]." last 7 days",	 	   "120", "300");
            echo "<img src='images/chart-sensor-".$row["id"].$span.".png' alt='RRD image'>";
            echo '</td>';

            echo '<td>';
            $span = "-90d";
            create_graph( "/home/pi/pi-heating-hub/data/s-".$row["id"].".rrd", "/var/www/pi-heating-hub/images/chart-sensor-".$row["id"].$span.".png", 	$span, 	$row["name"]." last 3 months",	 	   "120", "200");
            echo "<img src='images/chart-sensor-".$row["id"].$span.".png' alt='RRD image'>";
            echo '</td>';

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
        
        if (! $ret) { echo "<b>Graph error: </b>".rrd_error()."\n"; }
    }
    
?>

</font>
</div>
</body>
</html>
