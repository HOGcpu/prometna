<?php
$DEBUG = true;	
header('Content-type: text/plain; charset=utf-8');						// Priprava podrobnejših opisov napak (med testiranjem)

include("orodja.php"); 					// Vključitev 'orodij'

$zbirka = dbConnect();					// Pridobitev povezave s podatkovno zbirko
$array1 = array("Z MATERIALNO ŠKODO", "Z LAŽJO TELESNO POŠKODBO", "S HUDO TELESNO POŠKODBO", "S SMRTNIM IZIDOM");



header('Content-Type: application/json');	// Nastavimo MIME tip vsebine odgovora

switch($_SERVER["REQUEST_METHOD"])		// Glede na HTTP metodo v zahtevi izberemo ustrezno dejanje nad virom
{
	case 'GET':
		if(!empty($_GET["zapOseba"]))
		{
			pridobi_udelezenca($_GET["zapOseba"]);		// 
		}
		else
		{
			http_response_code(400);
		}
		break;
	
	// ******* Dopolnite še z dodajanjem, posodabljanjem in brisanjem igralca		
	default:
		http_response_code(405);		//Če naredimo zahtevo s katero koli drugo metodo je to 'Method Not Allowed'
		break;
}

mysqli_close($zbirka);					// Sprostimo povezavo z zbirko


// ----------- konec skripte, sledijo funkcije -----------

// function pridobi_vse_igralce()
// {
	// global $zbirka;
	// $odgovor=array();
	
	// $poizvedba="SELECT vzdevek, ime, priimek, email FROM igralec";	
	
	// $rezultat=mysqli_query($zbirka, $poizvedba);
	
	// while($vrstica=mysqli_fetch_assoc($rezultat))
	// {
		// $odgovor[]=$vrstica;
	// }
	
	// http_response_code(200);		//OK
	// echo json_encode($odgovor);
// }

function pridobi_udelezenca($zapOseba)
{
	global $zbirka;
	$vzdevek=mysqli_escape_string($zbirka, $zapOseba);
	
	$poizvedba="SELECT * FROM udelezenec WHERE zapOseba='$zapOseba';";
	
	$rezultat=mysqli_query($zbirka, $poizvedba);

	if(mysqli_num_rows($rezultat)>0)	//igralec obstaja
	{
		$odgovor=mysqli_fetch_assoc($rezultat);
		
		http_response_code(200);		//OK
		echo json_encode($odgovor);
	}
	else							// igralec ne obstaja
	{
		http_response_code(404);		//Not found
	}
}









?>