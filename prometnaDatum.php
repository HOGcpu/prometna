<?php
$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)
header('Content-type: text/plain; charset=utf-8');

include("orodja.php"); 					// Vključitev 'orodij'

$zbirka = dbConnect();					// Pridobitev povezave s podatkovno zbirko


header('Content-Type: application/json');	// Nastavimo MIME tip vsebine odgovora

switch($_SERVER["REQUEST_METHOD"])		// Glede na HTTP metodo v zahtevi izberemo ustrezno dejanje nad virom
{
	case 'POST':
        prometnaDatum();
		break;	

	default:
		http_response_code(405);		//Če naredimo zahtevo s katero koli drugo metodo je to 'Method Not Allowed'
		break;
}

mysqli_close($zbirka);					// Sprostimo povezavo z zbirko


// ----------- konec skripte, sledijo funkcije -----------

function prometnaDatum()
{
	global $zbirka, $DEBUG;
	$podatki = json_decode(file_get_contents("php://input"), true);
    $odgovor =array();

    if(isset($podatki["datum1"], $podatki["datum2"], 
	$podatki["steviloOffset"])){
		
		$datum1 = mysqli_escape_string($zbirka, $podatki["datum1"]);
		$datum2 = mysqli_escape_string($zbirka, $podatki["datum2"]);

		$steviloOffset = mysqli_escape_string($zbirka, $podatki["steviloOffset"]);
		
		$poizvedba = "SELECT zapStevilka, klasifikacija, upravnaEnota, datum FROM prometna
			where datum BETWEEN '$datum1' and '$datum2' 
			or datum BETWEEN '$datum2' and '$datum1' 
			order by datum
			Limit 10 offset '$steviloOffset';";
			
		
		$poizvedbaZapreverbo = "SELECT count(datum) FROM prometna
			where datum BETWEEN '$datum1' and '$datum2'
			or datum BETWEEN '$datum2' and '$datum1'";
		
		
		
		if(mysqli_num_rows(mysqli_query($zbirka, $poizvedbaZapreverbo)) > 0)	//igralec obstaja
		{
			//$rezultat=mysqli_query($zbirka, $poizvedba2);
			if(is_numeric($steviloOffset)){
				$poizvedba="SELECT zapStevilka, klasifikacija, upravnaEnota, datum 
				FROM prometna WHERE datum BETWEEN '$datum1' and '$datum2' 
				or datum BETWEEN '$datum2' and '$datum1' 
				order by datum
				LIMIT 10
				OFFSET $steviloOffset";
	
				$rezultat = mysqli_query($zbirka, $poizvedba);
			
				$reultatStevilo = mysqli_query($zbirka, $poizvedbaZapreverbo);
			
				while($vrstica = mysqli_fetch_assoc($rezultat))	//igralec obstaja
				{
					$odgovor[]=$vrstica;
					
				}
			
				$odgovor2 = mysqli_fetch_assoc($reultatStevilo);
			
				http_response_code(200);		//OK
				echo json_encode($odgovor);
				//echo json_encode($odgovor2);			
			}
			else{
				$reultatStevilo = mysqli_query($zbirka, $poizvedbaZapreverbo);
				$odgovor2 = mysqli_fetch_assoc($reultatStevilo);
				http_response_code(201);		//OK
				echo json_encode($odgovor2);				
			}
			
		}
		else							// igralec ne obstaja
		{
			http_response_code(404);		//Not found
		}
	}
	
	else{
		http_response_code(407);	
	}
}



?>