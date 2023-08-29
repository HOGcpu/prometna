<?php
$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)
header('Content-type: text/plain; charset=utf-8');

include("orodja.php"); 					// Vključitev 'orodij'

$zbirka = dbConnect();					// Pridobitev povezave s podatkovno zbirko


header('Content-Type: application/json');	// Nastavimo MIME tip vsebine odgovora

switch($_SERVER["REQUEST_METHOD"])		// Glede na HTTP metodo v zahtevi izberemo ustrezno dejanje nad virom
{
	case 'GET':
        udelezenci_stats();
		break;	

	default:
		http_response_code(405);		//Če naredimo zahtevo s katero koli drugo metodo je to 'Method Not Allowed'
		break;
}

mysqli_close($zbirka);					// Sprostimo povezavo z zbirko


// ----------- konec skripte, sledijo funkcije -----------

function udelezenci_stats()
{
	global $zbirka, $DEBUG;
	$podatki = json_decode(file_get_contents("php://input"), true);
    $odgovor =array();

    //if(isset($podatki["povzrocitelj"], 
	//$podatki["starost"], $podatki["spol"]))

	$povzrocitelj = mysqli_escape_string($zbirka, $podatki["povzrocitelj"]);

	$starost = mysqli_escape_string($zbirka, $podatki["starost"]);
	$spol = mysqli_escape_string($zbirka, $podatki["spol"]);
	$vrednostAlkotesta = mysqli_escape_string($zbirka, $podatki["vrednostAlkotesta"]);

	
	if(!empty($starost)) 
	{
		$query_string_second_part[]=" AND starost = '$starost'";
	}
	if(!empty($spol)) 
	{
		$query_string_second_part[]=" AND spol = '$spol'";
	}
	
	$query_string_First_Part= "SELECT * FROM udelezenec WHERE";
	$query_string_second_part= implode(" ", $query_string_second_part);
	$query_string_second_part=  preg_replace("/AND/", " ", $query_string_second_part, 1);

	//$varnostniPas = mysqli_escape_string($zbirka, $podatki["varnostniPas"]);
	//$vozniskiStazVLetih = mysqli_escape_string($zbirka, $podatki["vozniskiStazVLetih"]);
	//$vrednostAlkotesta = mysqli_escape_string($zbirka, $podatki["vrednostAlkotesta"]);

	$poizvedba = $query_string_First_Part.$query_string_second_part;

	//AND varnostniPas = '$varnostniPas'
	//AND vozniskiStazVLetih = '$vozniskiStazVLetih' 
	//AND vrednostAlkotesta = '$vrednostAlkotesta'

	$rezultat=mysqli_query($zbirka, $poizvedba);

	while($vrstica = mysqli_fetch_assoc($rezultat))	//igralec obstaja
	{
		$odgovor[]=$vrstica;
				
	}
	http_response_code(200);		//OK
	echo json_encode($odgovor);

	//else							// igralec ne obstaja
	//{
	//	http_response_code(405);		//Not found
	//}
}



?>