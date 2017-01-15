<!DOCTYPE HTML>
<html>
<head>
<style>
        .pbody { background-color: #080808; }
        .debug {font-family: courier; color: red; font-size: large;}
        .error {color: #FF0000;}
        .tcol {font: arial 18px bold; color: grey;}
        .dcol {font-family: arial; color: grey; font-size: large;}
        .ptitle {font-family: arial; color: navy; font-size: xxx-large;}
        .itextbox {font-family: arial; color: grey; font-size: large; padding: 12px 20px; margin: 8px 30px; width: 80%;}
        .bgrey {  background-color: grey;  border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial;}
        .bblue {  background-color: blue;  border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial;}
        .bgreen { background-color: green; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial;}
        .bred {   background-color: red;   border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-family: arial;}
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
        
        #print_r($_GET);

        $DEVICE_ID = $_GET['id'];
        
        #echo $DEVICE_ID;

        #echo $_SERVER["REQUEST_METHOD"];
        
        if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
                #print_r("<pre><BR>------------------------<BR>");
                #print_r($_POST);
                #print_r("<BR>------------------------<BR></pre>");
                
                if ( isset($_POST["done"]) ) {
                        #echo "#### done ####";
                        header('Location: /devices-list.php');
                        exit();
                }
                
                if ( isset($_POST["save"]) ) {
                        #echo "#### save ####";
                        
                        $POST_DEVICE_NAME = $_POST["name"];
                        $POST_DEVICE_PIN = $_POST["pin"];
                        $POST_DEVICE_ACTIVE_LEVEL = $_POST["active_level"];
                        
                        // Create connection
                        $conn = mysqli_connect($servername, $username, $password, $dbname);
                        // Check connection
                        if (!$conn) {
                                die("<br><br>Connection failed: " . mysqli_connect_error());
                        }
                        # Update schedules with post data
                        $sql = "UPDATE devices SET name = '$POST_DEVICE_NAME', pin = '$POST_DEVICE_PIN', active_level = '$POST_DEVICE_ACTIVE_LEVEL' WHERE d_id='".$DEVICE_ID."';";
                        #echo $sql;
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
        #echo $sql;
        $result = mysqli_query($conn, $sql);
        #print_r( $result );
        if (mysqli_num_rows($result) == 0) {
                echo "0 results";
        }
        
        $row = mysqli_fetch_assoc($result);
        $DEVICE_NAME = $row["name"];
        $DEVICE_PIN = $row["pin"];
        $DEVICE_ACTIVE_LEVEL = $row["active_level"];
        
        echo "<span class='ptitle'>$DEVICE_NAME</span><br><br>";


        echo "<span class='tcol'>Name</span><br>";
        echo "<input type='text' name='name' value='$DEVICE_NAME' class='itextbox'><br><br>";
        
        echo "<span class='tcol'>GPIO Pin</span><br>";
        echo "<input type='text' name='pin' value='$DEVICE_PIN' class='itextbox'><br><br>";
        
        echo "<span class='tcol'>Pin Active H/L</span><br>";
        echo "<input type='text' name='active_level' value='$DEVICE_ACTIVE_LEVEL' class='itextbox'><br><br>";
 
        
        echo "<input type='submit' name='save' value='Save' class='bgreen' />";
        echo "&nbsp;&nbsp;";
        echo "<input type='submit' name='done' value='Done' class='bgrey'  />";
        echo '</form>';
        
        mysqli_close($conn);
?>

</body>
</html>
