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
		if(!empty($_GET["zapStevilka"]))
		{
			pridobi_nesreco($_GET["zapStevilka"]);		// 
		}
		else
		{
			http_response_code(400);
		}
		break;
		
	case 'POST':
		dodaj_nesreco();
		break;
	case 'PUT':
		if(!empty($_GET["zapStevilka"]))
		{
			posodobi_nesreco($_GET["zapStevilka"]);		// 
		}
		else
		{
			http_response_code(400);     // bad request
		}
		break;
	case 'DELETE':
		if(!empty($_GET["zapStevilka"]))
		{
			izbrisi_nesreco($_GET["zapStevilka"]);		// Izbris igralca
		}
		else
		{
			http_response_code(400);     // bad request
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

function pridobi_nesreco($zapStevilka)
{
	global $zbirka;
	$vzdevek=mysqli_escape_string($zbirka, $zapStevilka);
	
	$poizvedba="SELECT * FROM prometna WHERE zapStevilka='$zapStevilka'";
	
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

function dodaj_nesreco()
{
	global $zbirka, $DEBUG, $array1;

	$resultUpravna = mysqli_query($zbirka, "SELECT DISTINCT upravnaEnota FROM prometna");
	$uniqueUpravna = [];
	while($row = mysqli_fetch_row($resultUpravna)){
   		array_push($uniqueUpravna, $row[0]);
	}

	$uniqueDatum = date("Y-m-d");
	
	$podatki = json_decode(file_get_contents("php://input"),true);
	
	if(isset($podatki["zapStevilka"], $podatki["klasifikacija"], $podatki["upravnaEnota"], 
	$podatki["datum"], $podatki["naselje"],$podatki["cesta"],$podatki["opisKraj"],$podatki["vzrokNesrece"],
	$podatki["tipNesrece"],$podatki["vreme"],$podatki["stanjePrometa"],$podatki["stanjeVozisca"]))
	{
		$zapStevilka = mysqli_escape_string($zbirka, $podatki["zapStevilka"]);
		$klasifikacija = mysqli_escape_string($zbirka, $podatki["klasifikacija"]);
		$upravnaEnota = mysqli_escape_string($zbirka, $podatki["upravnaEnota"]);

		$datum = mysqli_escape_string($zbirka, $podatki["datum"]);
		$naselje = mysqli_escape_string($zbirka, $podatki["naselje"]);
		$cesta = mysqli_escape_string($zbirka, $podatki["cesta"]);

		$opisKraj = mysqli_escape_string($zbirka, $podatki["opisKraj"]);
		$vzrokNesrece = mysqli_escape_string($zbirka, $podatki["vzrokNesrece"]);
		$tipNesrece = mysqli_escape_string($zbirka, $podatki["tipNesrece"]);

		$vreme = mysqli_escape_string($zbirka, $podatki["vreme"]);
		$stanjePrometa = mysqli_escape_string($zbirka, $podatki["stanjePrometa"]);
		$stanjeVozisca = mysqli_escape_string($zbirka, $podatki["stanjeVozisca"]);

		//$geslo = password_hash(mysqli_escape_string($zbirka, $podatki["geslo"]), PASSWORD_DEFAULT);
		//$email = mysqli_escape_string($zbirka, $podatki["email"]);

		$resultCesta = mysqli_query($zbirka, "SELECT DISTINCT cesta FROM prometna WHERE upravnaEnota = '$upravnaEnota'");
		$uniqueCesta = [];
		while($row = mysqli_fetch_row($resultCesta)){
			array_push($uniqueCesta, $row[0]);
		}

		$resultOpis = mysqli_query($zbirka, "SELECT DISTINCT opisKraj FROM prometna WHERE upravnaEnota = '$upravnaEnota'");
		$uniqueOpis = [];
		while($row = mysqli_fetch_row($resultOpis)){
			array_push($uniqueOpis, $row[0]);
		}

		$resultVzrok = mysqli_query($zbirka, "SELECT DISTINCT vzrokNesrece FROM prometna");
		$uniqueVzrok = [];
		while($row = mysqli_fetch_row($resultVzrok)){
			array_push($uniqueVzrok, $row[0]);
		}

		$resultTip = mysqli_query($zbirka, "SELECT DISTINCT tipNesrece FROM prometna");
		$uniqueTip = [];
		while($row = mysqli_fetch_row($resultTip)){
			array_push($uniqueTip, $row[0]);
		}

		if(!nesreca_obstaja($zapStevilka))
		{
			$poizvedba = "INSERT INTO prometna (zapStevilka, klasifikacija, upravnaEnota, datum, naselje, cesta, opisKraj, vzrokNesrece, tipNesrece, vreme, stanjePrometa, stanjeVozisca) 
			VALUES ('$zapStevilka', '$klasifikacija', '$upravnaEnota', '$datum', '$naselje', '$cesta', '$opisKraj', '$vzrokNesrece', '$tipNesrece', '$vreme', '$stanjePrometa', '$stanjeVozisca')";
			
			if(!(in_array($klasifikacija, $array1)))
			{
				$resultK = mysqli_query($zbirka, "SELECT DISTINCT upravnaEnota FROM prometna");
				$row = mysqli_fetch_row($resultK);
				http_response_code(409);
				pripravi_odgovor_napaka("Napačna klasifikacija. Pravilne klasifikacije: ",$array1);
			
			}
			elseif(!(in_array($upravnaEnota, $uniqueUpravna)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Napačna upravna enota. Pravilne upravne enote:", $uniqueUpravna);
				
			}
			elseif(($datum > $uniqueDatum) || (!validateDate($datum)) )
			{
				http_response_code(409);
				pripravi_odgovor_napaka("To ni danasji datum ali datum pred tem datumom. Danasnji datum:", $uniqueDatum);
				
			}
			//elseif(($naselje != "DA") || ($naselje != "NE") ) to popravi
			elseif(($naselje != "DA"))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Naselje ima lahko le naslednje vrednosti: ", "DA, NE");
			}
			elseif(!(in_array($cesta, $uniqueCesta)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Cesta v tej upravni enoti je le lahko:", $uniqueCesta);
				//echo json_encode($uniqueCesta);
			}
			elseif(!(in_array($opisKraj, $uniqueOpis)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Opis kraja mora biti sledeč:", $uniqueOpis);
			}
			elseif(!(in_array($vzrokNesrece, $uniqueVzrok)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Vzrok je lahko samo:", $uniqueVzrok);
			}
			elseif(!(in_array($tipNesrece, $uniqueTip)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Tip je lahko samo:", $uniqueTip);
			}
			elseif(mysqli_query($zbirka, $poizvedba))
			{
				http_response_code(201);
				$odgovor = URL_vira($zapStevilka);
				echo json_encode($odgovor);
			}
			else
			{
				http_response_code(500); //ni nujno vedno streznik kriv
				
				if($DEBUG)
				{
					pripravi_odgovor_napaka(mysqli_error($zbirka));
				}
			}
		}
		else
		{
			http_response_code(409);
			pripravi_odgovor_napaka("Nesreca s taksnim id-jem (zaporedno številko) že obstaja : ", $zapStevilka);
		}
	}
	else
	{
		http_response_code(400);
	}
	//izvedi poizvedbo za dodajanjem
}

function posodobi_nesreco($zapStevilka)
{
	global $zbirka, $DEBUG, $array1;
	$zapStevilka=mysqli_escape_string($zbirka, $zapStevilka);
	
	$podatki = json_decode(file_get_contents("php://input"), true);
	
	//obstaja igralec?
	if(nesreca_obstaja($zapStevilka))
	{
		if(isset($podatki["klasifikacija"], $podatki["vzrokNesrece"], $podatki["tipNesrece"]))
		{
			$klasifikacija = mysqli_escape_string($zbirka, $podatki["klasifikacija"]);
			$vzrokNesrece = mysqli_escape_string($zbirka, $podatki["vzrokNesrece"]);
			$tipNesrece = mysqli_escape_string($zbirka, $podatki["tipNesrece"]);
			
			$poizvedba = "UPDATE prometna SET klasifikacija='$klasifikacija', 
			vzrokNesrece='$vzrokNesrece', tipNesrece='$tipNesrece' WHERE zapStevilka='$zapStevilka'";

			$resultVzrok = mysqli_query($zbirka, "SELECT DISTINCT vzrokNesrece FROM prometna");
			$uniqueVzrok = [];
			while($row = mysqli_fetch_row($resultVzrok)){
				array_push($uniqueVzrok, $row[0]);
			}

			$resultTip = mysqli_query($zbirka, "SELECT DISTINCT tipNesrece FROM prometna");
			$uniqueTip = [];
			array_push($uniqueTip, "Tip nesreče je lahko samo: ");
			while($row = mysqli_fetch_row($resultTip)){
				array_push($uniqueTip, $row[0]);
			}

			if(!(in_array($klasifikacija, $array1)))
			{				
				http_response_code(409);
				pripravi_odgovor_napaka("Napačna klasifikacija");
				echo json_encode("Pravilne klasifikacije:");
				echo json_encode($array1);
			}
			elseif(!(in_array($vzrokNesrece, $uniqueVzrok)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Vzrok je lahko samo:");
				echo json_encode($uniqueVzrok);
			}
			elseif(!(in_array($tipNesrece, $uniqueTip)))
			{
				//$odgovor=mysqli_fetch_assoc($uniqueTip);
				http_response_code(409);
				// pripravi_odgovor_napaka("Tip nesreče je lahko samo:");
				echo json_encode($uniqueTip);
			}
			elseif(mysqli_query($zbirka, $poizvedba))
			{
				http_response_code(204);
				pripravi_odgovor_napaka("Uspesno posodobljena nesreca.");
				$odgovor = URL_vira($zapStevilka);
				echo json_encode($odgovor);
			}
			else
			{
				http_response_code(500); //ni nujno vedno streznik kriv
				
				if($DEBUG)
				{
					pripravi_odgovor_napaka(mysqli_error($zbirka));
				}
			}
		}
		else
		{
			http_response_code(400); //bad request
		}		
	}
	else
	{
		http_response_code(400);
	}	
}

function izbrisi_nesreco($zapStevilka)
{
	//console.log("asd");
	global $zbirka, $DEBUG;
	$zapStevilkaa=mysqli_escape_string($zbirka, $zapStevilka);
	
	$podatki = json_decode(file_get_contents("php://input"), true);
	
	//obstaja igralec?
	if(nesreca_obstaja($zapStevilkaa))
	{	
		$poizvedba = "DELETE FROM prometna WHERE zapStevilka='$zapStevilkaa'";
		if(mysqli_query($zbirka, $poizvedba))
		{
			http_response_code(204);
		}
		else
		{
			http_response_code(500); //ni nujno vedno streznik kriv
			
			if($DEBUG)
			{
				pripravi_odgovor_napaka(mysqli_error($zbirka), " ");
			}
		}
	}
	else
	{
		
		http_response_code(400); //bad request
		pripravi_odgovor_napaka("Nesreca s to stevilko ne obstaja:", $zapStevilkaa);
	}			
}


function validateDate($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}









?>