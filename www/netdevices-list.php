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
    .dcolname   { text-align: left; padding: 8px 32px; }
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

    $ini_array = parse_ini_file("/home/pi/pi-heating-hub/config/config.ini", true);
    
    $servername = $ini_array['db']['server'];
    $username =$ini_array['db']['user'];
    $password = $ini_array['db']['password'];
    $dbname = $ini_array['db']['database'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if ( array_key_exists( 'add', $_POST ) ) {
            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            // Check connection
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
                }
            $sql = "INSERT INTO network (name, mac, value) VALUES ('new', '00:00:00:00:00', 0)";
            if (!mysqli_query($conn, $sql)) {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
            mysqli_close($conn);
        }

        if ( array_key_exists( 'delete', $_POST ) ) {

            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            // Check connection
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
                }

            $NETDEV_ID = $_POST["netdev_id"];

            #echo $SCHED_ID;

            $sql = "DELETE FROM sched_network WHERE network_id='$NETDEV_ID';";
            if (!mysqli_query($conn, $sql)) {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
            $sql = "DELETE FROM network WHERE id='$NETDEV_ID';";
            if (!mysqli_query($conn, $sql)) {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
            mysqli_close($conn);
        }
    }

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $sql = "SELECT * FROM network order by name asc";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        // output data of each row
        echo "<span class='ptitle'>Network Devices</span><br><br>";

        echo "<table class='ttab' ><tr>";
        echo "<th class='tcol'><span class='tspan'>Name</span></th>";     
        echo "<th width=1%><span class='tspan'>MAC Address</span></th>";
        echo "<th width=1%><span class='tspan'>Status</span></th>";
        echo "<th width=1%></th><th width=1%></th>";
        echo "</tr>";

        while($row = mysqli_fetch_assoc($result)) {

            $NETDEV_ID = $row["id"];
            $NETDEV_NAME = $row["name"];
            $NETDEV_MAC = $row["mac"];
            $NETDEV_VALUE = $row["value"];

            echo "<tr>";

            echo "<td class='dcolname' ><span class='dspan'>".$NETDEV_NAME."</span></td>";
            echo "<td class='dcolname' ><span class='dspan'>".$NETDEV_MAC."</span></td>";

            if ( $NETDEV_VALUE ) {
                echo "<td class='dcolstatus' ><img src='/images/dot-green.png' alt='Schedule Active' height='32' width='32'></td>";
            } else {
                echo "<td class='dcolstatus' ><img src='/images/dot-red.png' alt='Schedule Inactive' height='32' width='32'></td>";
            }

            echo "<td>";
            echo "<input type='button' onclick='location.href=\"/netdevice-edit.php?id=$NETDEV_ID\";' value='Edit' class='bblue' />";
            echo "</td>";

            echo "<td><form method='post' action='/netdevices-list.php'>";
            echo "<input type='hidden' name='netdev_id' value='".$NETDEV_ID."' />";
            echo "<input type='submit' name='delete' value='Delete' class='bred' /></form></td>";
            echo "</tr>";
        }    

        echo "</table>";

    } else {
        echo "<span class='ptitle'>No Configured Network Devices</span><br><br>";
    }

    mysqli_close($conn);
?>  

<form method='post' action='netdevices-list.php'>
<input type='submit' name='add' value='Add new' class='bgreen' />
<input type='button' onclick='location.href="/status.php";' value='Done' class='bgrey' />
</form>

</body>
</html>
