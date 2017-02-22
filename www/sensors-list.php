<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="refresh" content="30">
<style>
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

    #ini_set('display_errors', 1);
    #ini_set('display_startup_errors', 1);
    #error_reporting(E_ALL);
    
    $ini_array = parse_ini_file("/home/pi/pi-heating-hub/config/config.ini", true);
    
    $servername = $ini_array['db']['server'];
    $username =$ini_array['db']['user'];
    $password = $ini_array['db']['password'];
    $dbname = $ini_array['db']['database'];

    
///////////////////////////////////////////////////////////

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if ( array_key_exists( 'new', $_POST )) {
            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            // Check connection
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
                }

            echo '<br>';

            $subnet_inet = shell_exec('ifconfig wlan0 | grep "inet " | cut -d":" -f2 | cut -d" " -f1');
            $subnet_mask = shell_exec('ifconfig wlan0 | grep "inet " | cut -d":" -f4 | cut -d" " -f1');
            
            $subnet_inet_octets = explode( '.', $subnet_inet );
            $subnet_mask_octets = explode( '.', $subnet_mask );
            
            $subnet_inet_octets_b = [];
            $subnet_mask_octets_b = [];
            
            for ( $n = 1; $n <= 4; $n++ ) {
                $subnet_inet_octets_b[$n] = decbin($subnet_inet_octets[$n]);
                $subnet_mask_octets_b[$n] = decbin($subnet_mask_octets[$n]);
            }
            
            
            
            $subnet_cidr = '192.168.0.0/24';
            
            $subnet_scan = shell_exec('nmap -sP $subnet_cidr | grep report | grep -v router | cut -d" " -f5');

            $subnet_devices = explode( "\n", $subnet_scan);

            foreach( $subnet_devices as $device_ip ) {

                set_error_handler(function() { $sensor_count = '0'; });
                $sensor_count = file_get_contents("http://".$device_ip.":8080/count.php");
                restore_error_handler();

                if( $sensor_count > 0 ) {

                    for ($sensor_ref =1 ; $sensor_ref <= $sensor_count; $sensor_ref++) { 

                        $sensor_name = file_get_contents("http://".$device_ip.":8080/name.php?id=".$sensor_ref);
                        $sensor_unit = "deg C";

                        $sql = "INSERT INTO sensors (ip, ref, name, unit) select '".$device_ip."', '".$sensor_ref."', '".$sensor_name."', '".$sensor_unit."' from  dual WHERE not exists (SELECT 1 FROM sensors WHERE ip='".$device_ip."' AND ref='".$sensor_ref."');";

                        $result = mysqli_query($conn, $sql);
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

    $sql = "SELECT * FROM sensors;";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        
        echo "<span class='ptitle'>Available Input Sensors</span>";

        echo "<table class='ttab'><tr>";
        echo "<th class='tcol'><span class='tspan'>Name</span></th>";
        echo "<th class='tcol' width=auto><span class='tspan'>Value</span></th>";
        echo "<th class='tcol' width=1%><span class='tspan'>History</span></th>";
        echo "<th class='tcol' width=1%></th></tr>";

        while($row = mysqli_fetch_assoc($result)) {

            #$id = $row["id"];
            #$name = $row["name"];
            $SENSOR_ID = $row["id"];
            $SENSOR_REF = $row["ref"];
            $SENSOR_NAME = $row["name"];
            $SENSOR_VALUE = $row["value"];
            $SENSOR_UNIT = $row["unit"];

            $img_dir = 'images/chart-sensor-';
            $rrd_dir = '/home/pi/pi-heating-hub/data/s-';

            echo '<tr>';

            echo "<td class='dcolname'><span class='dspan'>$SENSOR_NAME</span></td>";

            echo "<td class='dcolstatus'><span class='dspan'>$SENSOR_VALUE $SENSOR_UNIT</span></td>";

            echo "<td class='dcolstatus'>";
            $span = "-24h";
            create_graph( $rrd_dir.$SENSOR_ID.".rrd", $img_dir.$SENSOR_ID.$span.".png", 	$span, 	$row["name"]." last 24 hours",	 	   "80", "300");
            if ( file_exists( $img_dir.$SENSOR_ID.$span.".png") ){
                echo "<img src='".$img_dir.$SENSOR_ID.$span.".png' alt='RRD image'>";
            }
            echo "</td>";


            echo "<td class='dcolstatus'>";

            echo "<form method='post' action='sensors-list.php'>";
            echo "<input type='hidden' name='sensor_id' value='".$SENSOR_ID."'>";
            echo "<input type='submit' name='delete' value='Delete' class='bred' /></form>";
            echo '</td>';

            echo '</tr>';
        }

        echo "</table>";

    } else {
        echo "<span class='ptitle'>No Available Input Sensors</span><br><br>";
    }


    mysqli_close($conn);

    //exit;


    function create_graph($rrdfile, $output, $start, $title, $height, $width) {

        $options = array(
            "--slope-mode",
            "--start", $start,
    #        "--title=$title",
    #        "--vertical-label=Temperature",
    #        "--lower=0",
            "--height=$height",
            "--width=$width",
            "-cBACK#161616",
            "-cCANVAS#1e1e1e",
            "-cSHADEA#080808",
            "-cSHADEB#080808",
            "-cFONT#c7c7c7",
            "-cGRID#888800",
            "-cMGRID#ffffff",
            "-nTITLE:10",
            "-nAXIS:9",
            "-nUNIT:8",
            "-y 1:5",
    #        "-cFRAME#ffffff",
            "-cARROW#000000",
            "DEF:callmax=$rrdfile:data:MAX",
            "CDEF:transcalldatamax=callmax,1,*",
            "AREA:transcalldatamax#a0b84240",
            "LINE4:transcalldatamax#a0b842",
    #        "LINE4:transcalldatamax#a0b842:Calls",
    #        "COMMENT:\\n",
    #        "GPRINT:transcalldatamax:LAST:Calls Now %6.2lf",
    #        "GPRINT:transcalldatamax:MAX:Data %6.2lf"
            "COMMENT:\\n"
        );

        $ret = rrd_graph( $output, $options );

        if (! $ret) { echo "<b>Waiting for initial data</b>"; }
    }

?>
    
<form method='post' action='sensors-list.php'>
<input type='submit' name='new' value='Scan for new sensors' class='bgreen' />
&nbsp;&nbsp;
<input type='button' onclick='location.href="/status.php";' value='Done' class='bgrey' />
</form>
       
</body>
</html>
