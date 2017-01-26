<!DOCTYPE HTML>  
<html>
<head>
<meta http-equiv="refresh" content="30">
<style>
    .sensorvalue {font-family: courier; color: green; font-size:200pt;}
    .sensorvaluedec {font-family: courier; color: green; font-size:80pt;}
    .sensorname {font-family: courier; color: green; font-size:28pt;}
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
    .bgrey {  background-color: grey;  border: none; color: white; padding: 4px; text-align: center; text-decoration: none; display: inline-block; font-size: 10pt; font-family: arial; margin: 8px ; width: 120px; }
    .bblue {  background-color: blue;  border: none; color: white; padding: 4px; text-align: center; text-decoration: none; display: inline-block; font-size: 10pt; font-family: arial; margin: 8px ; width: 120px; }
    .bgreen { background-color: green; border: none; color: white; padding: 4px; text-align: center; text-decoration: none; display: inline-block; font-size: 10pt; font-family: arial; margin: 8px ; width: 120px; }
    .bred {   background-color: red;   border: none; color: white; padding: 4px; text-align: center; text-decoration: none; display: inline-block; font-size: 10pt; font-family: arial; margin: 8px ; width: 120px; }
    table, th, td { border: 5px solid #080808; }
    th, td {  background-color: #1a1a1a; text-align: center;}
</style>
</head>
<body class='pbody'>
    
<?php
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $ini_array = parse_ini_file("/home/pi/pi-heating-hub/config/config.ini", true);
    
    $servername = $ini_array['db']['server'];
    $username =$ini_array['db']['user'];
    $password = $ini_array['db']['password'];
    $dbname = $ini_array['db']['database'];
       
    $img_dir = 'images/chart-status-';
    $rrd_dir = '/home/pi/pi-heating-hub/data/s-';
 
    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (!$conn) {
        die("<br><br>Connection failed: " . mysqli_connect_error());
    }

    $sql_sensors = "SELECT min(id) AS id FROM sensors;";
    $result_sensors = mysqli_query($conn, $sql_sensors);
    if (mysqli_num_rows($result_sensors) == 0) {
        #echo "0 sensors results"; 
    }
    while($row = mysqli_fetch_assoc($result_sensors)) {
        $SENSOR_MAX_ID = $row["id"];
    }

    $GET_SENSOR_ID = isset($_GET['sid']) ? $_GET['sid'] : $SENSOR_MAX_ID;
    $GET_GRAPH_ID = isset($_GET['gid']) ? $_GET['gid'] : $SENSOR_MAX_ID;
    $GET_GRAPH_SP = isset($_GET['gsp']) ? $_GET['gsp'] : '-24h';
    
 
    if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
    
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
                $POST_GRAPH_ID = isset($_POST['gid']) ? $_POST['gid'] : '1';
                $POST_GRAPH_SP = isset($_POST['gsp']) ? $_POST['gsp'] : '-1h';
            
                #$page = 'status.php?sid='.$SENSOR_ID.'&gid='.$GRAPH_ID.'&gsp='.$GRAPH_SP;
                $page = 'status.php?sid='.$POST_GRAPH_ID.'&gid='.$POST_GRAPH_ID.'&gsp='.$POST_GRAPH_SP;
                #echo $page;
                header('Location: '.$page);
                exit();

            }
        }
    }


    $sql_modes = "SELECT * FROM modes;";
    $result_modes = mysqli_query($conn, $sql_modes);
    if (mysqli_num_rows($result_modes) == 0) {
        #echo "0 modes results"; 
    }

    $sql_sensor = "SELECT * from sensors WHERE id = '".$GET_SENSOR_ID."';";
    $result_sensor = mysqli_query($conn, $sql_sensor);
    if (mysqli_num_rows($result_sensor) == 0) {
        #echo "0 sensors results"; 
    }

    $sql_timers = "SELECT * FROM timers;";
    $result_timers = mysqli_query($conn, $sql_timers);
    if (mysqli_num_rows($result_timers) == 0) {
        #echo "0 timers results"; 
    }


    echo "<table class='ttab'>";
    echo "<tr>";

    echo "<td width=33%>";

    echo "<input type='button' onclick='location.href=\"sched-list.php\";' value='Schedules' class='bgrey' />";
    echo "<input type='button' onclick='location.href=\"sensors-list.php\";' value='Input Sensors' class='bgrey' />";
    echo "<input type='button' onclick='location.href=\"devices-list.php\";' value='Output Devices' class='bgrey' />";
    echo "<input type='button' onclick='location.href=\"modes-list.php\";' value='Modes' class='bgrey' />";    
    echo "<input type='button' onclick='location.href=\"timers-list.php\";' value='Timers' class='bgrey' />";    
    echo "<input type='button' onclick='location.href=\"netdevices-list.php\";' value='Connected Devices' class='bgrey' />";    

    echo "</td>";

    echo "<td width=33%>";

    $SENSOR_NAME =  '';
    $SENSOR_VALUE = '';
    
    while($row = mysqli_fetch_assoc($result_sensor)) {
        $SENSOR_NAME =  $row["name"];
        $SENSOR_VALUE = $row["value"];
    }

    echo "<span class='sensorname'>".$SENSOR_NAME."</span><br>";

    if( $SENSOR_VALUE == '' ) {
        #echo "#$SENSOR_VALUE#";
        echo "<span class='sensorvalue'>-</span>"; 
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

    echo '<form id="formTimers" method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'?sid='.$GET_SENSOR_ID.'&gid='.$GET_GRAPH_ID.'&gsp='.$GET_GRAPH_SP.'">';

    while($row = mysqli_fetch_assoc($result_modes)) {
        $MODE_ID = $row["id"];
        $MODE_NAME = $row["name"];
        $MODE_VALUE = $row["value"];


        #echo "<form name='modes' method='post' action='status.php?sid=".$SENSOR_ID."&gid=".$GRAPH_ID."&gsp=".$GRAPH_SP."'>";
        if ( $MODE_VALUE =='0' ) {
            echo "<input type='submit' name='enable-mode-".$MODE_ID."' value='$MODE_NAME' class='bgrey' />";
        }else{
            echo "<input type='submit' name='disable-mode-".$MODE_ID."' value='$MODE_NAME' class='bgreen' />";
        }
        #echo "</form>";
    }

    while($row = mysqli_fetch_assoc($result_timers)) {
        $TIMER_ID = $row["id"];
        $TIMER_NAME = $row["name"];
        $TIMER_VALUE = $row["value"];


        #echo "<form name='modes' method='post' action='status.php?sid=".$SENSOR_ID."&gid=".$GRAPH_ID."&gsp=".$GRAPH_SP."'>";
        if ( $TIMER_VALUE =='0' ) {
            echo "<input type='submit' name='start-timer-$TIMER_ID' value='$TIMER_NAME' class='bgrey' />";
        }else{
            echo "<input type='submit' name='stop-timer-$TIMER_ID' value='$TIMER_NAME [$TIMER_VALUE]' class='bgreen' />";
        }
        #echo "</form>";
    }

    echo "</form>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";

    echo '<td>';
    
    echo "<table class='ttab'>";
    echo "<tr>";
    echo "<td width=1% align=center>";

    $sql = "SELECT * FROM sensors;";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        #echo "sensors 0 results"; 
    }

    while($row = mysqli_fetch_assoc($result)) {

        $LOOP_SENSOR_NAME = $row["name"];
        $LOOP_SENSOR_ID = $row["id"];

        if ( $LOOP_SENSOR_ID == $GET_GRAPH_ID ) { $SELECTED = 'selected'; }else{ $SELECTED = ''; }

        echo "<input type='button' onclick='location.href=\"status.php?sid=$LOOP_SENSOR_ID&gid=$LOOP_SENSOR_ID&gsp=$GET_GRAPH_SP\";' value='$LOOP_SENSOR_NAME' class='bgrey' />";
    } 

    echo "</td>";

    echo "<td width=99% align=center>";

    if( $SENSOR_NAME != '' ) {
        create_graph( $rrd_dir.$GET_GRAPH_ID.".rrd", $img_dir.$GET_GRAPH_ID.$GET_GRAPH_SP.".png", 	$GET_GRAPH_SP, 	$SENSOR_NAME,	 	   "180", "700");
        echo "<img src='".$img_dir.$GET_GRAPH_ID.$GET_GRAPH_SP.".png' alt='RRD image'>";  
    }
    
    echo "</td></tr>";
    echo "</table>";

    echo "<table class='ttab'><tr><td>";
    echo "<input type='button' onclick='location.href=\"status.php?sid=$GET_SENSOR_ID&gid=$GET_SENSOR_ID&gsp=-1h\";' value='One hour' class='bgrey' />";
    echo "<input type='button' onclick='location.href=\"status.php?sid=$GET_SENSOR_ID&gid=$GET_SENSOR_ID&gsp=-3h\";' value='Three hours' class='bgrey' />";
    echo "<input type='button' onclick='location.href=\"status.php?sid=$GET_SENSOR_ID&gid=$GET_SENSOR_ID&gsp=-12h\";' value='Twelve hours' class='bgrey' />";
    echo "<input type='button' onclick='location.href=\"status.php?sid=$GET_SENSOR_ID&gid=$GET_SENSOR_ID&gsp=-24h\";' value='One Day' class='bgrey' />";
    echo "<input type='button' onclick='location.href=\"status.php?sid=$GET_SENSOR_ID&gid=$GET_SENSOR_ID&gsp=-3d\";' value='Three Days' class='bgrey' />";
    echo "<input type='button' onclick='location.href=\"status.php?sid=$GET_SENSOR_ID&gid=$GET_SENSOR_ID&gsp=-1w\";' value='One week' class='bgrey' />";
    echo "<input type='button' onclick='location.href=\"status.php?sid=$GET_SENSOR_ID&gid=$GET_SENSOR_ID&gsp=-1m\";' value='One month' class='bgrey' />";
    echo "<input type='button' onclick='location.href=\"status.php?sid=$GET_SENSOR_ID&gid=$GET_SENSOR_ID&gsp=-1y\";' value='One year' class='bgrey' />";

    echo "</td></tr></table>";

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
        "-cBACK#1a1a1a",
        "-cCANVAS#1e1e1e",
        "-cSHADEA#1a1a1a",
        "-cSHADEB#1a1a1a",
        "-cFONT#c7c7c7",
        "-cGRID#888800",
        "-cMGRID#ffffff",
        "-nTITLE:10",
        "-nAXIS:12",
        "-nUNIT:10",
        "-y 1:5",
        "-cFRAME#1a1a1a",
        "-cARROW#1a1a1a",
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
