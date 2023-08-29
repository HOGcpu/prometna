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
	
	function poisci_udelezenca(){
		
		global $database;
		global $username;
		global $password;
		
		$login_ime = $_POST['upime'];
		$login_geslo = $_POST['geslo'];

		$uporabnik_je = "SELECT * FROM uporabnik WHERE ime = '$login_ime'";
	
		$user_exists = mysqli_query($database, $uporabnik_je);

		if(mysqli_num_rows($user_exists) > 0) {
			$query_password_correct = "SELECT * FROM uporabnik WHERE geslo = '$login_geslo' and ime = '$login_ime'";
			$password_correct = mysqli_query($database, $query_password_correct);

			if(mysqli_num_rows($password_correct) > 0) {
				$username = $login_ime;
				$password = $login_geslo;
			}
		}
		
	}
	
	function random_str(
		int $length = 64,
		string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string 
	{
			
		if ($length < 1) {
			throw new \RangeException("Length must be a positive integer");
		}
		$pieces = [];
		$max = mb_strlen($keyspace, '8bit') - 1;
		for ($i = 0; $i < $length; ++$i) {
			$pieces []= $keyspace[random_int(0, $max)];
		}
		return implode('', $pieces);
	}
	
	if(isset($_POST["upime"],$_POST["geslo"]))
	{
		
		poisci_udelezenca();
		
		if($_POST["upime"] == $username && $_POST["geslo"] == $password)
		{
			if($_POST["upime"] == "" && $_POST["geslo"] == "")
			{
				http_response_code(401);
			}
			// Naslednja vrstica ustvari zeton na podlagi uporabniskega imena in gesla in je za isto kombinacijo obeh vedno enak.
			// To naredi primer preprost, saj se lahko preverjanje veljavnosti zetona izvede na isti nacin kot tvorjenje, 
			// zato zetona na strezniku ni potrebno shraniti (glejte podatki.php). 
			// Z vidika varnosti pa je boljsi pristop tvorjenje nakljucnega zetona, ki ima omejeno casovno veljavnost.
			$token = hash("md5",random_str());
			
			$query_add_token = "UPDATE uporabnik SET token = '$token' WHERE ime = '$username'";
			mysqli_query($database, $query_add_token);
			
			echo json_encode(array('token'=>$token));
		}
		else{
			//v nasprotnem primeru zavrnemo avtentikacijo
			http_response_code(401);
		}
	}
	else{
		http_response_code(401);
	}
	
		
	mysqli_close($database);	// Disconnect from the database
?>