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
	
	if(isset($_POST["tokenTosendUpdate"]))
	{
		$trenutniToken = $_POST['tokenTosendUpdate'];
		$uporabnik_jee = "SELECT ime FROM uporabnik WHERE token = '$trenutniToken' AND (vloga = 'admin' OR vloga = 'konfigurator');";
		
		$user_exists = mysqli_query($database, $uporabnik_jee);

		if(mysqli_num_rows($user_exists) > 0) {
			http_response_code(201);
		}
		else{
			http_response_code(404);
			pripravi_odgovor_napaka("Nisi administrator ali konfigurator.", "Samo administrator in konfigurator lahko posodabljata.");
		}
		
	}
		
	mysqli_close($database);	// Disconnect from the database
?>