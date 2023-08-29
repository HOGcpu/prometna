<?php
$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)
header('Content-type: text/plain; charset=utf-8');

include("orodja.php"); 					// Vključitev 'orodij'

$zbirka = dbConnect();					// Pridobitev povezave s podatkovno zbirko
$distUdelezenec = array("UDELEŽENEC", "POVZROČITELJ");
$distSpol = array("MOŠKI", "ŽENSKI");

header('Content-Type: application/json');	// Nastavimo MIME tip vsebine odgovora

switch($_SERVER["REQUEST_METHOD"])		// Glede na HTTP metodo v zahtevi izberemo ustrezno dejanje nad virom
{
	case 'GET':
		if(!empty($_GET["zapStevilka"]))
		{
			udelezenci_nesrece($_GET["zapStevilka"]);
		}
		else
		{
			http_response_code(400);
		}
		break;
		
	case 'POST':
		dodaj_udelezenca();
		break;
		
	case 'PUT':
		if(!empty($_GET["zapStevilka"]))
		{
			posodobi_udelezenca($_GET["zapStevilka"]);		// 
		}
		else
		{
			http_response_code(400);     // bad request
		}
		break;
	case 'DELETE':
		if(!empty($_GET["zapStevilka"]))
		{
			izbrisi_udelezenca($_GET["zapStevilka"]);		// Izbris igralca
		}
		else
		{
			http_response_code(400);     // bad request
		}
		break;	

	default:
		http_response_code(405);		//Če naredimo zahtevo s katero koli drugo metodo je to 'Method Not Allowed'
		break;
}

mysqli_close($zbirka);					// Sprostimo povezavo z zbirko


// ----------- konec skripte, sledijo funkcije -----------

function udelezenci_nesrece($zapStevilka)
{
	global $zbirka, $DEBUG;
	$zapStevilka=mysqli_escape_string($zbirka, $zapStevilka);
	$odgovor =array();
	
	if(nesreca_obstaja($zapStevilka))
	{
		$poizvedba="SELECT * FROM udelezenec WHERE zapStevilka='$zapStevilka'";
	
		$rezultat=mysqli_query($zbirka, $poizvedba);
		
		while($vrstica = mysqli_fetch_assoc($rezultat))	//igralec obstaja
		{
			$odgovor[]=$vrstica;
					
		}
		http_response_code(200);		//OK
		echo json_encode($odgovor);
	}
	else							// igralec ne obstaja
	{
		http_response_code(404);		//Not found
	}
}

function dodaj_udelezenca()
{
	global $zbirka, $DEBUG, $distUdelezenec, $distSpol;
	$podatki = json_decode(file_get_contents("php://input"),true);
	
	if(isset($podatki["zapStevilka"], $podatki["povzrocitelj"], 
	$podatki["starost"], $podatki["spol"], $podatki["stalnoPrebivalisce"], $podatki["drzavljanstvo"],
	$podatki["poskodba"], $podatki["vrstaUdelezenca"], $podatki["varnostniPas"], $podatki["vozniskiStazVLetih"],
	$podatki["vrednostAlkotesta"]))
	{
		$zapStevilka = mysqli_escape_string($zbirka, $podatki["zapStevilka"]);
		$povzrocitelj = mysqli_escape_string($zbirka, $podatki["povzrocitelj"]);

		$starost = mysqli_escape_string($zbirka, $podatki["starost"]);
		$spol = mysqli_escape_string($zbirka, $podatki["spol"]);
		$stalnoPrebivalisce = mysqli_escape_string($zbirka, $podatki["stalnoPrebivalisce"]);

		$drzavljanstvo = mysqli_escape_string($zbirka, $podatki["drzavljanstvo"]);
		$poskodba = mysqli_escape_string($zbirka, $podatki["poskodba"]);
		$vrstaUdelezenca = mysqli_escape_string($zbirka, $podatki["vrstaUdelezenca"]);

		$varnostniPas = mysqli_escape_string($zbirka, $podatki["varnostniPas"]);
		$vozniskiStazVLetih = mysqli_escape_string($zbirka, $podatki["vozniskiStazVLetih"]);
		$vrednostAlkotesta = mysqli_escape_string($zbirka, $podatki["vrednostAlkotesta"]);

		$resultPoskodba = mysqli_query($zbirka, "SELECT DISTINCT poskodba FROM udelezenec");
		$uniquePoskodba = [];
		while($row = mysqli_fetch_row($resultPoskodba)){
			array_push($uniquePoskodba, $row[0]);
		}

		$resultvrstaUdelezenca = mysqli_query($zbirka, "SELECT DISTINCT vrstaUdelezenca FROM udelezenec");
		$uniquevrstaUdelezenca = [];
		while($row = mysqli_fetch_row($resultvrstaUdelezenca)){
			array_push($uniquevrstaUdelezenca, $row[0]);
		}

		$resultvarnostniPas = mysqli_query($zbirka, "SELECT DISTINCT varnostniPas FROM udelezenec");
		$uniquevarnostniPas = [];
		while($row = mysqli_fetch_row($resultvarnostniPas)){
			array_push($uniquevarnostniPas, $row[0]);
		}
		
		if(nesreca_obstaja($zapStevilka))
		{
			$zadnjaCifra = zadnji_udelezenec($zapStevilka);
			$nula1 = 0;

			if ($zadnjaCifra < 10)
			{
				$nula2 = 0;
				$zapOseba = (int)($zapStevilka . $nula1 . $nula2 . $zadnjaCifra);
			}
			else 
			{
				$zapOseba = (int)($zapStevilka . $nula1 . $zadnjaCifra);
			}

			$poizvedba = "INSERT INTO udelezenec (zapOseba, zapStevilka, povzrocitelj, 
			starost, spol, stalnoPrebivalisce, drzavljanstvo, poskodba, vrstaUdelezenca,
			varnostniPas, vozniskiStazVLetih, vrednostAlkotesta) VALUES 
			('$zapOseba', '$zapStevilka', '$povzrocitelj', '$starost', '$spol', 
			'$stalnoPrebivalisce', '$drzavljanstvo', '$poskodba', '$vrstaUdelezenca',
			'$varnostniPas', '$vozniskiStazVLetih', '$vrednostAlkotesta')";
			
			if(!(in_array($povzrocitelj, $distUdelezenec)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Napačna vrsta udeleženca, Pravilna vnosa sta: ", $distUdelezenec);
				//echo json_encode("Pravilna vnosa sta: ");
				//echo json_encode($distUdelezenec);
			}
			elseif(!(in_array($spol, $distSpol)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Napačno vnešen spol. Pravilna vnosa sta: ", $distSpol);
				// echo json_encode("Pravilna vnosa sta: ");
				// echo json_encode($distSpol);
			}
			elseif(!(in_array($poskodba, $uniquePoskodba)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Napačno vnešena poškodba. Pravilna vnosi so: ", $uniquePoskodba);
				// echo json_encode("Pravilna vnosi so: ");
				// echo json_encode($uniquePoskodba);
			}
			elseif(!(in_array($vrstaUdelezenca, $uniquevrstaUdelezenca)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Napačno vnešena vrsta udeleženca. Pravilni vnosi so: ", $uniquevrstaUdelezenca);
				//echo json_encode("Pravilni vnosi so: ");
				//echo json_encode($uniquevrstaUdelezenca);
			}
			elseif(!(in_array($varnostniPas, $uniquevarnostniPas)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Naroben vnos za varnostni pas. Pravilni vnosi so: ", $uniquevarnostniPas);
				//echo json_encode("Pravilni vnosi so: ");
				//echo json_encode($uniquevarnostniPas);
			}
			elseif(!(is_decimal($vrednostAlkotesta)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Rezultat Alkotesta vnesite v decimalnem načinu z .", "Primeri pravilnega vnosa so: 0.00, 0.14, 1.12 itd.");
				//echo json_encode("Primeri pravilnega vnosa so: ");
				//echo json_encode("0.00, 0.14, 1.12 itd.");
			}
			elseif(mysqli_query($zbirka, $poizvedba))
			{
				http_response_code(201);
				$odgovor = URL_vira($zapOseba);
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
			pripravi_odgovor_napaka("Nesreca s takim ID-jem (zap. številko) ne obstaja", "napaka");
		}
	}
	else
	{
		http_response_code(400);
	}
	//izvedi poizvedbo za dodajanjem
}

function posodobi_udelezenca($zapOseba)
{
	global $zbirka, $DEBUG, $distUdelezenec, $distSpol;
	$zapOseba=mysqli_escape_string($zbirka, $zapOseba);
	
	$podatki = json_decode(file_get_contents("php://input"), true);
	
	//obstaja igralec?
	if(udelezenec_obstaja($zapOseba))
	{
		if(isset($podatki["zapStevilka"], $podatki["povzrocitelj"], $podatki["starost"], $podatki["spol"], $podatki["stalnoPrebivalisce"], 
			$podatki["drzavljanstvo"], $podatki["poskodba"], $podatki["vrstaUdelezenca"], $podatki["varnostniPas"], 
			$podatki["vozniskiStazVLetih"], $podatki["vrednostAlkotesta"]))
		{
			$zapStevilka = mysqli_escape_string($zbirka, $podatki["zapStevilka"]);
			$povzrocitelj = mysqli_escape_string($zbirka, $podatki["povzrocitelj"]);

			$starost = mysqli_escape_string($zbirka, $podatki["starost"]);
			$spol = mysqli_escape_string($zbirka, $podatki["spol"]);
			$stalnoPrebivalisce = mysqli_escape_string($zbirka, $podatki["stalnoPrebivalisce"]);

			$drzavljanstvo = mysqli_escape_string($zbirka, $podatki["drzavljanstvo"]);
			$poskodba = mysqli_escape_string($zbirka, $podatki["poskodba"]);
			$vrstaUdelezenca = mysqli_escape_string($zbirka, $podatki["vrstaUdelezenca"]);

			$varnostniPas = mysqli_escape_string($zbirka, $podatki["varnostniPas"]);
			$vozniskiStazVLetih = mysqli_escape_string($zbirka, $podatki["vozniskiStazVLetih"]);
			$vrednostAlkotesta = mysqli_escape_string($zbirka, $podatki["vrednostAlkotesta"]);

			$resultPoskodba = mysqli_query($zbirka, "SELECT DISTINCT poskodba FROM udelezenec");
			$uniquePoskodba = [];
			while($row = mysqli_fetch_row($resultPoskodba)){
				array_push($uniquePoskodba, $row[0]);
			}

			$resultvrstaUdelezenca = mysqli_query($zbirka, "SELECT DISTINCT vrstaUdelezenca FROM udelezenec");
			$uniquevrstaUdelezenca = [];
			while($row = mysqli_fetch_row($resultvrstaUdelezenca)){
				array_push($uniquevrstaUdelezenca, $row[0]);
			}

			$resultvarnostniPas = mysqli_query($zbirka, "SELECT DISTINCT varnostniPas FROM udelezenec");
			$uniquevarnostniPas = [];
			while($row = mysqli_fetch_row($resultvarnostniPas)){
				array_push($uniquevarnostniPas, $row[0]);
			}

			$poizvedba = "UPDATE udelezenec SET povzrocitelj = '$povzrocitelj', 
				starost = '$starost', spol = '$spol', stalnoPrebivalisce = '$stalnoPrebivalisce', 
				drzavljanstvo = '$drzavljanstvo', 
				poskodba = '$poskodba', vrstaUdelezenca = '$vrstaUdelezenca',
				varnostniPas = '$varnostniPas', vozniskiStazVLetih = '$vozniskiStazVLetih', 
				vrednostAlkotesta = '$vrednostAlkotesta' 
				where zapOseba = $zapOseba AND zapStevilka = $zapStevilka;";

			if(!(in_array($povzrocitelj, $distUdelezenec)))
			{
				http_response_code(410);
				pripravi_odgovor_napaka("Napačna vrsta udeleženca, Pravilna vnosa sta:", $distUdelezenec);
				//echo json_encode("Pravilna vnosa sta:");
				//echo json_encode($distUdelezenec);
			}
			elseif(!(in_array($spol, $distSpol)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Napačno vnešen spol. Pravilna vnosa sta: ", $distSpol);
				//echo json_encode("Pravilna vnosa sta:");
				//echo json_encode($distSpol);
			}
			elseif(!(in_array($poskodba, $uniquePoskodba)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Napačno vnešena poškodba, Pravilna vnosi so: ", $uniquePoskodba);
				//echo json_encode("Pravilna vnosi so:");
				//echo json_encode($uniquePoskodba);
			}
			elseif(!(in_array($vrstaUdelezenca, $uniquevrstaUdelezenca)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Napačno vnešena vrsta udeleženca, Pravilni vnosi so: ", $uniquevrstaUdelezenca);
				//echo json_encode("Pravilni vnosi so:");
				//echo json_encode($uniquevrstaUdelezenca);
			}
			elseif(!(in_array($varnostniPas, $uniquevarnostniPas)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Naroben vnos za varnostni pas. Pravilni vnosi so: ", $uniquevarnostniPas);
				//echo json_encode("Pravilni vnosi so:");
				//echo json_encode($uniquevarnostniPas);
			}
			elseif(!(is_decimal($vrednostAlkotesta)))
			{
				http_response_code(409);
				pripravi_odgovor_napaka("Rezultat Alkotesta vnesite v decimalnem načinu z ., Primeri pravilnega vnosa so: ", "0.00, 0.14, 1.12 itd.");
				//echo json_encode("Primeri pravilnega vnosa so:");
				//echo json_encode("0.00, 0.14, 1.12 itd.");
			}
			elseif(mysqli_query($zbirka, $poizvedba))
			{
				http_response_code(204);
				$odgovor = URL_vira($zapOseba);
				echo json_encode($odgovor);
			}
			else
			{
				http_response_code(500); //ni nujno vedno streznik kriv
				
				if($DEBUG)
				{
					pripravi_odgovor_napaka(mysqli_error($zbirka), "napaka");
				}
			}
		}
		else
		{
			http_response_code(400); //bad request
			echo json_encode("Niso nastavljeni vsi podatki v oknih!");
		}		
	}
	else
	{
		http_response_code(400);
		echo json_encode("Ta udelezenec s to stevilko zaporedne Osebe ne obstaja.");
	}	
}

function izbrisi_udelezenca($zapOseba)
{
	global $zbirka, $DEBUG;
	$zapOseba=mysqli_escape_string($zbirka, $zapOseba);
	
	$podatki = json_decode(file_get_contents("php://input"), true);
	
	//obstaja igralec?
	if(udelezenec_obstaja($zapOseba))
	{	
		$poizvedba = "DELETE FROM udelezenec WHERE zapOseba='$zapOseba'";
		if(mysqli_query($zbirka, $poizvedba))
		{
			http_response_code(204);
		}
		else
		{
			http_response_code(500); //ni nujno vedno streznik kriv
			
			if($DEBUG)
			{
				pripravi_odgovor_napaka(mysqli_error($zbirka), "Ta udelezenec s to stevilko ne obstaja.");
			}
		}
	}
	else
	{
		http_response_code(400); //bad request
	}			
}


function is_decimal( $val )
{
    return is_numeric( $val ) && floor( $val ) >= 0;
}




?>