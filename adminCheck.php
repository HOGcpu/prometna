<?php 
	//uporabnisko ime in geslo enega izmed registriranih uporabnikov:
	$DEBUG = true;

	include("orodja.php");

	$database = dbConnect(); 
	
	header('Content-Type: application/json');	// Nastavimo MIME tip vsebine odgovora
	header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	
	$username = "";
	$password = "";
	
	if(isset($_POST["tokenTosend"]))
	{
		$trenutniToken = $_POST['tokenTosend'];
		$uporabnik_jee = "SELECT ime FROM uporabnik WHERE token = '$trenutniToken' and vloga = 'admin';";
		
		$user_exists = mysqli_query($database, $uporabnik_jee);

		if(mysqli_num_rows($user_exists) > 0) {
			http_response_code(201);
			pripravi_odgovor_napaka("Si administrator.", "Samo administrator lahko izvaja to akcijo.");
		}
		else{
			http_response_code(401);
			pripravi_odgovor_napaka("Nisi administrator.", "Samo administrator lahko izvaja to akcijo.");
		}
		
	}
	
		
	mysqli_close($database);	// Disconnect from the database
?>