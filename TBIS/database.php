<?php
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "bus_system";
$conn = "";
try {
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
} catch (mysqli_sql_exception) {
    echo "you are not connected <br>";
}
