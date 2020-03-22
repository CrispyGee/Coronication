<?php
$servername = "localhost";
$username = "root";
$password = "1234";
$database = "db_test";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";

$function_name = $_POST["funktionsname"];

if ($function_name == "anliegende_nutzer_abfragen") {
        $email = $_POST["email"];
        $passwort = $_POST["password"];
        $id = getIdByEmail($email, $conn);
        $previous_ids = [];
        $connectedUsers = queryConnectedUsers($id, $conn, 0, $previous_ids);
        echo $connectedUsers;
}

function queryConnectedUsers($id, $conn,$depth,$previous_ids){
    $connected_users_query = "SELECT * FROM Nutzer WHERE Nutzer.ID 
            IN (SELECT Verbindung FROM Verbindungen WHERE Nutzer = ".$id.") 
            OR Nutzer.ID IN (SELECT Nutzer FROM Verbindungen WHERE Verbindung = ".$id.");";
        // $connected_users_query = "SELECT * FROM Verbindungen WHERE Nutzer = ".$id." OR Verbindung = ".$id.";" 

    $result = $conn->query($connected_users_query);

    if ($result->num_rows > 0) {
        // output data of each row
        $returnVal = "";
        if ($depth == 0) $returnVal.= "{'ID': ".$id.",\n";
        $returnVal .= "'connectedUsers': [";
        while($row = $result->fetch_assoc()) {
            if (!in_array($row["ID"], $previous_ids)){
                $returnVal.= "{'ID': " . $row["ID"]. ", 'Email': '" . $row["email"] . "'";
                    if ($depth<=1) {
                        $returnVal.=", ";
                        $returnVal.=queryConnectedUsers($row["ID"], $conn, ($depth+1), array_merge($previous_ids, [$id]));
                    }
                    $returnVal.= "},\n";
            }
        }
        $returnVal.= "]";
        $returnVal = str_replace(",\n]", "\n]", $returnVal);
        $returnVal = str_replace("'", "\"", $returnVal);
        if ($depth==0) $returnVal.="}";
        return $returnVal;
    } else {
        return "{}";
    }
}

function getIdByEmail($email, $conn)
{
    $id_query = "SELECT ID FROM Nutzer WHERE Nutzer.email = '".$email."';";
    
    $result = $conn->query($id_query);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            return $row["ID"];
        }
    } else {
        return -1;
    }
}

