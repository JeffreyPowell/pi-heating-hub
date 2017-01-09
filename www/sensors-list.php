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

#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);
    
$servername = "localhost";
$username = "pi";
$password = "password";
$dbname = "pi_heating_db";

    
///////////////////////////////////////////////////////////

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    #print_r("<br>------------------------<br>");
    #print_r($_POST);
    #print_r("<br>------------------------<br>");
    #print_r($_GET);
    #print_r("<br>------------------------<br>");
    
    if ( array_key_exists( 'done', $_POST )) {
        header('Location: /status.php');
        exit();
    }
   
    if ( array_key_exists( 'new', $_POST )) {
        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
            }

        
        echo '<br>';
 
        $subnet_scan = shell_exec('nmap -sP 192.168.0.0/24 | grep report | grep -v router | cut -d" " -f5');
        echo "<pre>$subnet_scan</pre>";
    
        $subnet_devices = explode( "\n", $subnet_scan);

        foreach( $subnet_devices as $device_ip ) {
            #echo $device_ip;
            #echo '<br>';
        
            set_error_handler(function() { $sensor_count = '0'; });
            $sensor_count = file_get_contents("http://".$device_ip.":8080/count.php");
            restore_error_handler();
        
            if( $sensor_count > 0 ) {
                #echo $device_ip;
                #echo '<br>';
                #echo $sensor_count;
                #echo '<br>';
            
                for ($sensor_ref =1 ; $sensor_ref <= $sensor_count; $sensor_ref++) { 
                    #echo $sensor_ref;
                    #echo '<br>';
                    $sensor_name = file_get_contents("http://".$device_ip.":8080/name.php?id=".$sensor_ref);
                    $sensor_unit = "deg C";
                    #echo $sensor_name;
                    #echo '<br>';
                    #echo $sensor_unit;
                    #echo '<br>';
                
                    $sql = "INSERT INTO sensors (ip, ref, name, unit) select '".$device_ip."', '".$sensor_ref."', '".$sensor_name."', '".$sensor_unit."' from  dual WHERE not exists (SELECT 1 FROM sensors WHERE ip='".$device_ip."' AND ref='".$sensor_ref."');";
                    #echo $sql;
                    #echo '<br>';
                
                    $result = mysqli_query($conn, $sql);
                    #echo "{";
                    #print_r( $result );
                    #echo "}";
                    #echo '<br>';
                }
            }
        }

        mysqli_close($conn);
    }
    
    if ( array_key_exists( "delete", $_POST )) {
    
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
    echo "<b>Waiting for initial data</b>";
  }
}


?>
    
<form method='post' action='sensors-list.php'>
<input type='submit' name='new' value='Scan for new sensors'>
<input type="submit" name="done" value="Done" />
</form>

</font>
</body>
</html>
