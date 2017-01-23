<!DOCTYPE HTML>
<html>
<head>
<style>
    .pbody { background-color: #080808; font-family: courier; color: red; font-size: small;}
    .debug { font-family: courier; color: red; font-size: large; }
    .error { color: #FF0000; }
    .ttab  { width: 100%; }
    .tcol  { font: 22px arial; }
    .tspan { font: 22px arial; color: grey; margin: 16px; display: inline-block; }
    .dcolname   { text-align: left; padding: 0 0 0 32px; }
    .dcolstatus { text-align: center; }
    .dspan { font-family: arial; color: grey; font-size: large; display: inline-block; }
    .ptitle { font: bold 32px arial; color: blue; }
    .itextbox { font-family: arial; color: grey; font-size: large; padding: 16px; margin: 16px; display: inline-block; width: 90%; }
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

    $ini_array = parse_ini_file("/home/pi/pi-heating-hub/config/config.ini", true);
    
    $servername = $ini_array['db']['server'];
    $username =$ini_array['db']['user'];
    $password = $ini_array['db']['password'];
    $dbname = $ini_array['db']['database'];

    #print_r($_GET);
    $NETDEV_ID = $_GET['id'];

    #echo $DEVICE_ID;
    #echo $_SERVER["REQUEST_METHOD"];
        
    if ($_SERVER["REQUEST_METHOD"] == "POST" ) {

        if ( isset($_POST["save"]) ) {
            #echo "#### save ####";

            $POST_NETDEV_NAME = str_replace( "'", " ", $_POST["name"]);
            $POST_NETDEV_MAC = str_replace( " ", "", $_POST["mac"]);

            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            // Check connection
            if (!$conn) {
                die("<br><br>Connection failed: " . mysqli_connect_error());
            }
            # Update schedules with post data
            $sql = "UPDATE network SET name = '$POST_NETDEV_NAME', mac = '$POST_NETDEV_MAC' WHERE id='$NETDEV_ID';";
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
    echo '<form method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'?id='.$NETDEV_ID.'">';
    $sql = "SELECT * FROM network WHERE id=".$NETDEV_ID;
    #echo $sql;
    $result = mysqli_query($conn, $sql);
    #print_r( $result );
    if (mysqli_num_rows($result) == 0) {
        echo "0 results";
    }

    $row = mysqli_fetch_assoc($result);
    $NETDEV_NAME = htmlentities($row["name"]);
    $NETDEV_MAC = $row["mac"];

    echo "<span class='ptitle'>EDIT Network Device '$NETDEV_NAME'</span><br><br>";
    echo "<table class='ttab'>";
    echo "<tr><td>";
    echo "<span class='tspan'>Name</span><br>";
    echo "<input type='text' name='name' value='".$NETDEV_NAME."' class='itextbox'><br><br>";

    echo "</td></tr><tr><td>";

    echo "<span class='tspan'>MAC Address</span><br>";
    echo "<input type='text' name='mac' value='$NETDEV_MAC' class='itextbox'><br><br>";

    echo "</td></tr>";

    echo "</table>";

    echo "<input type='submit' name='save' value='Save' class='bgreen' />";
    echo "&nbsp;&nbsp;";
    echo "<input type='button' onclick='location.href=\"/netdevices-list.php\";' value='Done' class='bgrey' />";
    echo '</form>';
    
    mysqli_close($conn);
?>

</body>
</html>
