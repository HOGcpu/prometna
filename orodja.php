<?php

/**
 * Funkcija vzpostavi povezavo z zbirko podatkov na proceduralni način
 *
 * @return $conn objekt, ki predstavlja povezavo z izbrano podatkovno zbirko
 */
function dbConnect()
{
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "prometne_nesrece";

	// Ustvarimo povezavo do podatkovne zbirke
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	mysqli_set_charset($conn,"utf8");
	
	// Preverimo uspeh povezave
	if (mysqli_connect_errno())
	{
		printf("Povezovanje s podatkovnim strežnikom ni uspelo: %s\n", mysqli_connect_error());
		exit();
	} 	
	return $conn;
}

/**
 * Funkcija pripravi odgovor v obliki JSON v primeru napake
 *
 * @param $vsebina Znakovni niz, ki opisuje napako
 */
function pripravi_odgovor_napaka($vsebina, $addedData)
{
	$odgovor=array(
		'status' => 0,
		'error_message'=>$vsebina,
		'error_data'=>$addedData
	);
	echo json_encode($odgovor);
}

/**
 * Funkcija preveri, če podan igralec obstaja v podatkovni zbirki
 *
 * @param $vzdevek Vzdevek igralca
 * @return true, če igralec obstaja, v nasprotnem primeru false
 */
function nesreca_obstaja($vzdevek)
{	
	global $zbirka;
	$vzdevek=mysqli_escape_string($zbirka, $vzdevek);
	
	$poizvedba="SELECT * FROM prometna WHERE zapStevilka='$vzdevek'";
	
	if(mysqli_num_rows(mysqli_query($zbirka, $poizvedba)) > 0)
	{
		return true;
	}
	else
	{
		return false;
	}	
}

function udelezenec_obstaja($vzdevek)
{	
	global $zbirka;
	$vzdevek=mysqli_escape_string($zbirka, $vzdevek);
	
	$poizvedba="SELECT * FROM udelezenec WHERE zapOseba='$vzdevek'";
	
	if(mysqli_num_rows(mysqli_query($zbirka, $poizvedba)) > 0)
	{
		return true;
	}
	else
	{
		return false;
	}	
}

function zadnji_udelezenec($zapStevilka)
{	
	global $zbirka;
	$zapStevilka=mysqli_escape_string($zbirka, $zapStevilka);
	
	$poizvedba="SELECT * FROM udelezenec WHERE zapStevilka='$zapStevilka'";

	$stevilka = (mysqli_num_rows(mysqli_query($zbirka, $poizvedba)) + 1);
	
	return $stevilka;
		
}

/**
 * Funkcija pripravi URL podanega vira
 *
 * @param $vir Ime vira
 * @return $url URL podanega vira
 */
function URL_vira($vir)
{
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
	{
		$url = "https"; 
	}
	else
	{
		$url = "http"; 
	}
	$url .= "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $vir;
	
	return $url; 
}
?>