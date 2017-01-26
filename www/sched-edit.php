<!DOCTYPE HTML>
<html>
<head>
<style>
    .pbody { background-color: #080808; font-family: courier; color: red; font-size: small; }
    .debug { font-family: courier; color: red; font-size: large; }
    .error { color: #FF0000; }
    .ttab  { width: 100%; vertical-align: top; }
    .ttabsub  { width: 100%; border: 0px; text-align: center; font-family: arial; color: grey; font-size: small; vertical-align: middle;}
    .tcol  { font: 22pt arial; }
    .tspan { font: 16pt arial; color: grey; margin: 16px; display: inline-block; }
    .dcolname   { text-align: left; padding: 8px 8px 8px 32px; }
    .dcolstatus { text-align: center; }
    .dspan { font-family: arial; color: grey; font-size: large; display: inline-block; }
    .ptitle { font: bold 32px arial; color: blue; }
    .ptitlesub { font: bold 24px arial; color: navy; }
    .itextbox { font-family: arial; color: grey; font-size: 12pt; padding: 8px; margin: 4px; display: block; }
    .itextboxsub { font-family: arial; color: grey; font-size: 11pt; padding: 4px; margin: 0px; display: block; width:  90%; }
    .bgrey {  background-color: grey;  border: none; color: white; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial; margin: 12px ; }
    .bblue {  background-color: blue;  border: none; color: white; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial; margin: 12px ; }
    .bgreen { background-color: green; border: none; color: white; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial; margin: 12px ; }
    .bred {   background-color: red;   border: none; color: white; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial; margin: 12px ; }
    table, th, td { border: 5px solid #080808; }
    th, td {  background-color: #1a1a1a; vertical-align: top; padding: 8px;  }
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
    
    $SCHED_ID = $_GET['id'];


    if ($_SERVER["REQUEST_METHOD"] == "POST" ) {

        if ( isset($_POST["save"]) ) {

            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            // Check connection
            if (!$conn) {
                die("<br><br>Connection failed: " . mysqli_connect_error());
            }

            # Update schedules with post data

            $sql = "UPDATE schedules SET name = '".$_POST["name"]."', start = '".$_POST["start"]."', end = '".$_POST["end"]."' WHERE id='".$SCHED_ID."';";
            if (mysqli_query($conn, $sql)) {
                #echo "<br><br>Schedule updated successfully";
            } else {
                echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
            }

            if ( !isset($_POST["repeat_dow"]) ) {
                $sql = "UPDATE schedules SET dow1 = '0', dow2 = '0', dow3 = '0', dow4 = '0', dow5 = '0', dow6 = '0', dow7 = '0' WHERE id='".$SCHED_ID."';";
            } else {
                $sql = "UPDATE schedules SET dow1 = '".in_array("dow1", $_POST["repeat_dow"])."', dow2 = '".in_array("dow2", $_POST["repeat_dow"])."', dow3 = '".in_array("dow3", $_POST["repeat_dow"])."', dow4 = '".in_array("dow4", $_POST["repeat_dow"])."', dow5 = '".in_array("dow5", $_POST["repeat_dow"])."', dow6 = '".in_array("dow6", $_POST["repeat_dow"])."', dow7 = '".in_array("dow7", $_POST["repeat_dow"])."' WHERE id='".$SCHED_ID."';";
            }

            if (mysqli_query($conn, $sql)) {
                #echo "<br><br>Schedule updated successfully";
            } else {
                echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
            }

            # Update devices

            $sql = "DELETE FROM sched_device WHERE sched_id = '".$SCHED_ID."';";
            if (!mysqli_query($conn, $sql)) {
                echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
            }

            if ( isset($_POST["devices"]) ) {
                foreach( $_POST["devices"] as $DEVICE_ID ) {
                    $sql = "INSERT INTO sched_device ( sched_id, device_id ) VALUES ( ".$SCHED_ID.", ".$DEVICE_ID.");";
                    if (!mysqli_query($conn, $sql)) {
                        echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
                    }
                }
             }

            # Update sensors

            $sql = "DELETE FROM sched_sensor WHERE sched_id = '".$SCHED_ID."';";
            if (!mysqli_query($conn, $sql)) {
                    echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
            }

            foreach( $_POST as $key => $val )
            {
                if( preg_match( '/sensor.*opp/', $key ) )
                {
                    $post_sched_sensor_sensor_id = explode( '_', $key )[1];

                    if( $val !== 'na' )
                    {
                        if( $val == 'eq' ) { $val = '='; }
                        if( $val == 'lt' ) { $val = '<'; }
                        if( $val == 'gt' ) { $val = '>'; }
                        if( $val == 'ne' ) { $val = '!'; }
                        $post_sched_sensor_sensor_value = $_POST["sensor_".$post_sched_sensor_sensor_id."_value"];
                        $sql = "INSERT INTO sched_sensor ( sched_id, sensor_id, opp, value ) VALUES ( '".$SCHED_ID."', '".$post_sched_sensor_sensor_id."', '".$val."', '".$post_sched_sensor_sensor_value."');";
                        #print_r( $sql );
                        if (!mysqli_query($conn, $sql)) {
                            echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
                        }
                    }
                }
            }

            # Update Modes

            $sql = "DELETE FROM sched_mode WHERE sched_id = '".$SCHED_ID."';";
            if (!mysqli_query($conn, $sql)) {
                    echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
            }

            foreach( $_POST as $key => $val )
            {
                if( preg_match( '/mode.*value/', $key ) )
                {
                    $post_sched_mode_mode_id = explode( '_', $key )[1];

                    if( $val !== 'na' )
                    {
                        if( $val == 'false' ) { $val = '0'; }
                        if( $val == 'true' ) { $val = '1'; }
                        #$post_sched_mode_mode_value = $_POST["mode_".$post_sched_mode_mode_id."_value"];
                        $sql = "INSERT INTO sched_mode ( sched_id, mode_id, test_value ) VALUES ( '".$SCHED_ID."', '".$post_sched_mode_mode_id."', '".$val."');";
                        #print_r( $sql );
                        if (!mysqli_query($conn, $sql)) {
                            echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
                        }
                    }
                }
            }

            # Update Timers

            $sql = "DELETE FROM sched_timer WHERE sched_id = '".$SCHED_ID."';";
            if (!mysqli_query($conn, $sql)) {
                echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
            }

            foreach( $_POST as $key => $val )
            {
                if( preg_match( '/timer.*value/', $key ) )
                {
                    $post_sched_timer_timer_id = explode( '_', $key )[1];

                    if( $val !== 'na' )
                    {
                        if( $val == 'false' ) { $val = '0'; }
                        if( $val == 'true' ) { $val = '1'; }
                        #$post_sched_mode_mode_value = $_POST["mode_".$post_sched_mode_mode_id."_value"];
                        $sql = "INSERT INTO sched_timer ( sched_id, timer_id, value ) VALUES ( '".$SCHED_ID."', '".$post_sched_timer_timer_id."', '".$val."');";
                        #print_r( $sql );
                        if (!mysqli_query($conn, $sql)) {
                            echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
                        }
                    }
                }
            }

            # Update Network

            $sql = "DELETE FROM sched_network WHERE sched_id = '".$SCHED_ID."';";
            if (!mysqli_query($conn, $sql)) {
                echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
            }

            foreach( $_POST as $key => $val )
            {
                if( preg_match( '/network.*value/', $key ) )
                {
                    $post_sched_network_network_id = explode( '_', $key )[1];

                    if( $val !== 'na' )
                    {
                        if( $val == 'false' ) { $val = '0'; }
                        if( $val == 'true' ) { $val = '1'; }
                        #$post_sched_mode_mode_value = $_POST["mode_".$post_sched_mode_mode_id."_value"];
                        $sql = "INSERT INTO sched_network ( sched_id, network_id, test ) VALUES ( '".$SCHED_ID."', '".$post_sched_network_network_id."', '".$val."');";
                        #print_r( $sql );
                        if (!mysqli_query($conn, $sql)) {
                            echo "<br><br>Error: " . $sql . "<br>" . mysqli_error($conn);
                        }
                    }
                }
            }

            mysqli_close($conn);
        }
    }

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (!$conn) {
        die("<br><br>Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM schedules WHERE id=".$SCHED_ID;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        echo "0 results";
    }

    $row = mysqli_fetch_assoc($result);

    $SCHED_NAME = $row["name"];
    $SCHED_START = $row["start"];
    $SCHED_END = $row["end"];

    echo "<span class='ptitle'>$SCHED_NAME</span>";

    echo '<form method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'?id='.$SCHED_ID.'">';

    echo "<table class='ttab'>";
    echo "<tr><td width=50%>";    

    echo "<span class='ptitlesub'>Schedule</span><br>";

    echo "<table class='ttabsub'>";
    echo "<tr><td>Name:</td><td><input type='text' name='name' value='$SCHED_NAME' class='itextboxsub'></td></tr>";
    echo "<tr><td>Start time:</td><td><input type='text' name='start' value='$SCHED_START' class='itextboxsub'></td></tr>";
    echo "<tr><td>End time:</td><td><input type='text' name='end' value='$SCHED_END' class='itextboxsub'></td></tr>";
    echo "</table>";



    $SCHED_DOW1 = $row["dow1"]; if ( $SCHED_DOW1 == '1' ) { $SCHED_DOW1_CHK = 'checked="checked"'; }else{ $SCHED_DOW1_CHK = ''; }
    $SCHED_DOW2 = $row["dow2"]; if ( $SCHED_DOW2 == '1' ) { $SCHED_DOW2_CHK = 'checked="checked"'; }else{ $SCHED_DOW2_CHK = ''; }
    $SCHED_DOW3 = $row["dow3"]; if ( $SCHED_DOW3 == '1' ) { $SCHED_DOW3_CHK = 'checked="checked"'; }else{ $SCHED_DOW3_CHK = ''; }
    $SCHED_DOW4 = $row["dow4"]; if ( $SCHED_DOW4 == '1' ) { $SCHED_DOW4_CHK = 'checked="checked"'; }else{ $SCHED_DOW4_CHK = ''; }
    $SCHED_DOW5 = $row["dow5"]; if ( $SCHED_DOW5 == '1' ) { $SCHED_DOW5_CHK = 'checked="checked"'; }else{ $SCHED_DOW5_CHK = ''; }
    $SCHED_DOW6 = $row["dow6"]; if ( $SCHED_DOW6 == '1' ) { $SCHED_DOW6_CHK = 'checked="checked"'; }else{ $SCHED_DOW6_CHK = ''; }
    $SCHED_DOW7 = $row["dow7"]; if ( $SCHED_DOW7 == '1' ) { $SCHED_DOW7_CHK = 'checked="checked"'; }else{ $SCHED_DOW7_CHK = ''; }

    echo "<span class='ptitlesub'>Repeat schedule every :</span>";

    echo "<table  class='ttabsub'><tr><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th></tr>";
    echo '<tr>';
    echo '<td><input type="checkbox" name="repeat_dow[]" value="dow1" '.$SCHED_DOW1_CHK.' /></td>';
    echo '<td><input type="checkbox" name="repeat_dow[]" value="dow2" '.$SCHED_DOW2_CHK.' /></td>';
    echo '<td><input type="checkbox" name="repeat_dow[]" value="dow3" '.$SCHED_DOW3_CHK.' /></td>';
    echo '<td><input type="checkbox" name="repeat_dow[]" value="dow4" '.$SCHED_DOW4_CHK.' /></td>';
    echo '<td><input type="checkbox" name="repeat_dow[]" value="dow5" '.$SCHED_DOW5_CHK.' /></td>';
    echo '<td><input type="checkbox" name="repeat_dow[]" value="dow6" '.$SCHED_DOW6_CHK.' /></td>';
    echo '<td><input type="checkbox" name="repeat_dow[]" value="dow7" '.$SCHED_DOW7_CHK.' /></td>';
    echo '</tr></table>';

    echo "</td><td width=50%>";



    $sql = "SELECT * FROM devices LEFT JOIN sched_device ON devices.d_id=sched_device.device_id AND sched_device.sched_id=".$SCHED_ID." ORDER BY devices.name asc;";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        #echo "devices 0 results";
    } else {

        echo "<span class='ptitlesub'>Activate Devices</span><br><br>";

        echo "<table class='ttabsub' >";

        while($row = mysqli_fetch_assoc($result)) {
            $DEVICE_NAME = $row["name"];
            $DEVICE_ID = $row["d_id"];
            $DEVICE_ACTIVE = $row["device_id"];

            if ( $DEVICE_ACTIVE != null ) { $DEVICE_ACTIVE_CHK = 'checked="checked"'; }else{ $DEVICE_ACTIVE_CHK = ''; }

            echo "<tr><td>$DEVICE_NAME</td><td><input type='checkbox' name='devices[]' value='$DEVICE_ID' $DEVICE_ACTIVE_CHK /></td></tr>";
        }

        echo "</table>";
    }
    
    echo "</td></tr><tr><td>";

    $sql = "SELECT * FROM sensors LEFT JOIN sched_sensor ON sensors.id=sched_sensor.sensor_id AND sched_sensor.sched_id=".$SCHED_ID.";";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        #echo "sensors 0 results";
    } else {

        echo "<span class='ptitlesub'>When Sensors</span>";

        echo "<table class='ttabsub' >";

        while($row = mysqli_fetch_assoc($result)) {

            echo "<tr><td>".$row["name"]."</td>";

            $SENSOR_OPP = $row["opp"];
            if ( $SENSOR_OPP == "" ) { $NA_SELECTED = 'selected'; }else{ $NA_SELECTED = ''; }
            if ( $SENSOR_OPP == "<" ) { $LT_SELECTED = 'selected'; }else{ $LT_SELECTED = ''; }
            if ( $SENSOR_OPP == "=" ) { $EQ_SELECTED = 'selected'; }else{ $EQ_SELECTED = ''; }
            if ( $SENSOR_OPP == "!" ) { $NE_SELECTED = 'selected'; }else{ $NE_SELECTED = ''; }
            if ( $SENSOR_OPP == ">" ) { $GT_SELECTED = 'selected'; }else{ $GT_SELECTED = ''; }

            echo '<td><select name="sensor_'.$row["id"].'_opp">';
            echo '<option value="na" '.$NA_SELECTED.' >(IS IGNORED)</option>';
            echo '<option value="lt" '.$LT_SELECTED.' >IS LESS THAN</option>';
            echo '<option value="eq" '.$EQ_SELECTED.' >IS EQUAL TO</option>';
            echo '<option value="ne" '.$NE_SELECTED.' >IS NOT EQUAL TO</option>';
            echo '<option value="gt" '.$GT_SELECTED.' >IS GREATER THAN</option>';
            echo '</select></td>';

            echo "<td><input type='text' name='sensor_".$row["id"]."_value' value='".$row["value"]."' class='itextboxsub'></td></tr>";
        }
        echo '</table>';
    }
    
    echo '</td><td>';

    $sql = "SELECT * FROM modes LEFT JOIN sched_mode ON modes.id=sched_mode.mode_id AND sched_mode.sched_id=".$SCHED_ID.";";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        #echo "sensors 0 results";
    } else {
        echo "<span class='ptitlesub'>AND Modes</span>";

        echo "<table class='ttabsub' >";

        while($row = mysqli_fetch_assoc($result)) {

            echo '<tr><td>'.$row["name"].'</td>';

            $MODE_ID = $row["id"];
            $MODE_VALUE = $row["test_value"];


            if ( $MODE_VALUE == "" ) { $NA_SELECTED = 'selected'; }else{ $NA_SELECTED = ''; }
            if ( $MODE_VALUE == "0" ) { $F_SELECTED = 'selected'; }else{ $F_SELECTED = ''; }
            if ( $MODE_VALUE == "1" ) { $T_SELECTED = 'selected'; }else{ $T_SELECTED = ''; }

            echo '<td><select name="mode_'.$MODE_ID.'_value">';
            echo '<option value="na" '.$NA_SELECTED.' >(IS IGNORED)</option>';
            echo '<option value="true" '.$T_SELECTED.' >ON</option>';
            echo '<option value="false" '.$F_SELECTED.' >OFF</option>';;
            echo '</select></td>';

        }
        echo '</table>';
    }
    
    echo "</td></tr><tr><td>";  

    $sql = "SELECT * FROM timers LEFT JOIN sched_timer ON timers.id=sched_timer.timer_id AND sched_timer.sched_id=".$SCHED_ID.";";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        #echo "sensors 0 results";
    } else {
        echo "<span class='ptitlesub'>AND Timers</span>";

        echo "<table class='ttabsub' >";

        while($row = mysqli_fetch_assoc($result)) {

            echo '<tr><td>'.$row["name"].'</td>';

            $TIMER_ID = $row["id"];
            $TIMER_VALUE = $row["value"];


            if ( $TIMER_VALUE == "" ) { $NA_SELECTED = 'selected'; }else{ $NA_SELECTED = ''; }
            if ( $TIMER_VALUE == "0" ) { $F_SELECTED = 'selected'; }else{ $F_SELECTED = ''; }
            if ( $TIMER_VALUE == "1" ) { $T_SELECTED = 'selected'; }else{ $T_SELECTED = ''; }

            echo '<td><select name="timer_'.$TIMER_ID.'_value">';
            echo '<option value="na" '.$NA_SELECTED.' >(IS IGNORED)</option>';
            echo '<option value="true" '.$T_SELECTED.' >RUNNING</option>';
            echo '<option value="false" '.$F_SELECTED.' >STOPPED</option>';;
            echo '</select></td>';
        }

        echo '</table>';   
    }
    
    echo "</td><td>";   

    $sql = "SELECT * FROM network LEFT JOIN sched_network ON network.id=sched_network.network_id AND sched_network.sched_id='".$SCHED_ID."';";

    #echo $sql;

    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        #echo "sensors 0 results";
    } else {
      
      echo "<span class='ptitlesub'>AND Who is Connected</span>";
      
      echo "<table class='ttabsub' >";

      while($row = mysqli_fetch_assoc($result)) {

        echo '<tr><td>'.$row["name"].'</td>';

        $NETWORK_ID = $row["id"];
        $NETWORK_VALUE = $row["test"];

        if ( $NETWORK_VALUE == "" ) { $NA_SELECTED = 'selected'; }else{ $NA_SELECTED = ''; }
        if ( $NETWORK_VALUE == "0" ) { $F_SELECTED = 'selected'; }else{ $F_SELECTED = ''; }
        if ( $NETWORK_VALUE == "1" ) { $T_SELECTED = 'selected'; }else{ $T_SELECTED = ''; }

        echo '<td><select name="network_'.$NETWORK_ID.'_value">';
        echo '<option value="na" '.$NA_SELECTED.' >(IS IGNORED)</option>';
        echo '<option value="true" '.$T_SELECTED.' >CONNECTED</option>';
        echo '<option value="false" '.$F_SELECTED.' >NOT CONNECTED</option>';;
        echo '</select></td>';

        }
    
    echo '</table>';
        
    }
    echo '</td></tr>';

    echo '</table>';

    echo '<br><br>';

    echo "<input type='submit' name='save' value='Save' class='bgreen' />";
    echo "&nbsp;&nbsp;";
    echo "<input type='button' onclick='location.href=\"/sched-list.php\";' value='Done' class='bgrey' />";
    echo '</form>';

    mysqli_close($conn);
    
?>

</body>
</html>
