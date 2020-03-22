<?php
$servername = "localhost";
$username = "root";
$password = "1234";
$database = "final";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";

$function_name = $_POST["funktionsname"];

if ($function_name == "nutzer_erstellen") {
	$email = $_POST["email"];
	$passwort = $_POST["password"];

	$create_user = "INSERT INTO Nutzer (Email, Passwort) VALUES ('".$email."','".$passwort."')";

	if ($conn->query($create_user) === TRUE) {
	    echo "http://ec2-18-195-125-154.eu-central-1.compute.amazonaws.com/test/#".$email;
	} else {
	    //echo "Error: " . $create_user . "<br>" . $conn->error;
	}
}
elseif ($function_name == "nutzer_erstellen_verbinden") {
	$email = $_POST["email"];
	$passwort = $_POST["password"];
	$verbindung = $_POST["verbindung"];

	$create = "INSERT INTO Nutzer (Email, Passwort) VALUES ('".$email."','".$passwort."')";
	$connect = "INSERT INTO Verbindungen (Nutzer, Verbindung) VALUES ((SELECT Nutzer.ID FROM Nutzer WHERE Nutzer.Email='".$email."'), (SELECT Nutzer.ID FROM Nutzer WHERE Nutzer.Email='".$verbindung."'))";

	if ($conn->query($create) === TRUE) {
		if ($conn->query($connect) === TRUE) {
	    	echo "http://ec2-18-195-125-154.eu-central-1.compute.amazonaws.com/test/#".$email;
		}
	} else {
	    //echo "Error1: " . $create . "<br>" . $conn->error;
	}

}
elseif ($function_name == "nutzer_verbinden") {
	$email = $_POST["email"];
	$passwort = $_POST["password"];
	$verbindung = $_POST["verbindung"];

	$CheckIfConnected = "SELECT Verbindungen.ID FROM Verbindungen WHERE Verbindungen.Nutzer= (SELECT Nutzer.ID FROM Nutzer WHERE Nutzer.Email='".$verbindung."') AND Verbindungen.Verbindung= (SELECT Nutzer.ID FROM Nutzer WHERE Nutzer.Email='".$email."')";
	$result = $conn->query($CheckIfConnected);
	if ($result->num_rows == 0) {			
		if ($email != $verbindung){
			$connect = "INSERT INTO Verbindungen (Nutzer, Verbindung) VALUES ((SELECT Nutzer.ID FROM Nutzer WHERE Nutzer.Email='".$email."' AND Nutzer.Passwort= '".$passwort."'), (SELECT Nutzer.ID FROM Nutzer WHERE Nutzer.Email='".$verbindung."'))";

			if ($conn->query($connect) === TRUE) {
    			//echo "New record created successfully";
			} else {
    			//echo "Error2: " . $connect . "<br>" . $conn->error;
			}
		}
    }
	else {
		echo "";
	}

}
elseif ($function_name == "login") {
	$email = $_POST["email"];
	$passwort = $_POST["password"];

	$login = "SELECT Email FROM Nutzer WHERE Nutzer.Email='".$email."' AND Nutzer.Passwort='".$passwort."'";
	$result = $conn->query($login);
	if ($result->num_rows > 0) {
    // output data of each row
		while ($row = $result->fetch_assoc()){
			echo "http://ec2-18-195-125-154.eu-central-1.compute.amazonaws.com/test/#".$row["Email"];
		}
    }
	else {
		echo "";
	}
}
elseif ($function_name == "formularausfuellen") {
	$email = $_POST["email"];
	$passwort = $_POST["password"];
	$alter = $_POST["alter"];
	$nachname = $_POST["nachname"];
	$vorname = $_POST["vorname"];
	$erkrankt = $_POST["erkrankt"];
	$direkterKontakt = $_POST["direkterKontakt"];

	$eintrag = "UPDATE Nutzer SET Alt=".$alter.", Nachname='".$nachname."', Vorname='".$vorname."', Erkrankt=".$erkrankt.", Kontakt=".$direkterKontakt." WHERE Nutzer.Email='".$email."' AND Nutzer.Passwort='".$passwort."'";

	if ($conn->query($eintrag) === TRUE) {
		echo "funktioniert";
	} else {
	    //echo "Error: " . $eintrag . "<br>" . $conn->error;
	}

}
elseif ($function_name == "formularausgefuellt") {
	$email = $_POST["email"];
	$passwort = $_POST["password"];
	$login = "SELECT Email FROM Nutzer WHERE Nutzer.Email='".$email."' AND Nutzer.Passwort='".$passwort."' AND Nutzer.Vorname IS NOT NULL";
	$result = $conn->query($login);
	if ($result->num_rows > 0) {
    // output data of each row
		echo "ausgefuellt";
    }
	else {
		echo "";
	}
}

$conn->close();
?>