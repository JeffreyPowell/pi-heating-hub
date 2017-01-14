<!DOCTYPE HTML>
<html>
<head>
<style>
.fixedsmall {font-family: courier; color: black; font-size: xx-small;}
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
        
        print_r($_GET);

        $DEVICE_ID = $_GET['id'];
        
        echo $DEVICE_ID;

        #echo $_SERVER["REQUEST_METHOD"];
        
        if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
                #print_r("<pre><BR>------------------------<BR>");
                #print_r($_POST);
                #print_r("<BR>------------------------<BR></pre>");
                
                if ( !isset($_POST["done"]) ) {
                        header('Location: /sched-list.php');
                        exit();
                }
                
                if ( !isset($_POST["save"]) ) {
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
                        mysqli_close($conn);
                }
                
        }

        
        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        // Check connection
        if (!$conn) {
                die("<br><br>Connection failed: " . mysqli_connect_error());
        }
        echo '<form method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'?id='.$DEVICE_ID.'">';
        $sql = "SELECT * FROM devices WHERE d_id=".$DEVICE_ID;
        echo $sql;
        $result = mysqli_query($conn, $sql);
        print_r( $result );
        if (mysqli_num_rows($result) == 0) {
                echo "0 results";
        }
        
        $row = mysqli_fetch_assoc($result);
        $DEVICE_NAME = $row["name"];
        $DEVICE_PIN = $row["pin"];
        $DEVICE_ACTIVE_LEVEL = $row["active_level"];
        
        echo '<h1>'.$DEVICE_NAME.'</h1><br><br>';
        echo '<table width=100% ><tr>';
        echo '<th width=30%>Name</th>';
        echo '<th width=30%>GPIO Pin</th>';
        echo '<th width=30%>Pin Active H/L</th></tr>';
        
        echo '<tr>';
        echo "<td><input type='text' name='name' value='$DEVICE_NAME'></th>";
        echo "<td><input type='text' name='pin' value='$DEVICE_PIN'></th>";
        echo "<td><input type='text' name='active_level' value='$DEVICE_ACTIVE_LEVEL'></th>";
        echo '</tr>';
        echo '</table>';


echo '<br><br>';
    
    
echo '<input type="submit" name="save" value="Save" />';
echo '<input type="submit" name="done" value="Done" />';
echo '</form>';
mysqli_close($conn);
?>

</body>
</html>
