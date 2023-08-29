<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Vmesnik prometnih nesreč</title>
		<link rel="stylesheet" type="text/css" href="stilP.css" />
		<script src="prometna.js"></script>
		
		
		
		
	</head>
	<body>
		<div class="center">
			<?php include "Meni.html"?>
			<br/>
			<form id="obrazec" onsubmit="podatkiNesrece(); return false;" style="display: inline">
				<label for="stevilka">številka prosmetne nesreče:</label>
				<input type="number" name="stevilka" required />
				<input type="submit" value="Prikaži" />
			</form>
			
			<form id="posodobitev" onsubmit="posodobiAdmin(); return false;" style="display: none">
				
				<label for="klasifikacija">Izberite klasifikacijo:</label>
				<select name="klasifikacija">
					<option value="Z MATERIALNO ŠKODO">Z MATERIALNO ŠKODO</option>
					<option value="Z LAŽJO TELESNO POŠKODBO">Z LAŽJO TELESNO POŠKODBO</option>
					<option value="S HUDO TELESNO POŠKODBO">S HUDO TELESNO POŠKODBO</option>
					<option value="S SMRTNIM IZIDOM">S SMRTNIM IZIDOM</option>
				</select> <br/>
				
				<label for="vzrokNesrece">Izberite vzrokNesrece:</label>
				<select name="vzrokNesrece">
					<option value="PREMIKI Z VOZILOM">PREMIKI Z VOZILOM</option>
					<option value="NEPRAVILNA STRAN / SMER VOŽNJE">NEPRAVILNA STRAN / SMER VOŽNJE</option>
					<option value="NEUSTREZNA VARNOSTNA RAZDALJA">NEUSTREZNA VARNOSTNA RAZDALJA</option>
					
					<option value="NEUPOŠTEVANJE PRAVIL O PREDNOSTI">NEUPOŠTEVANJE PRAVIL O PREDNOSTI</option>
					<option value="NEPRILAGOJENA HITROST">NEPRILAGOJENA HITROST</option>
					<option value="NEPRAVILNOSTI PEŠCA">NEPRAVILNOSTI PEŠCA</option>
					
					<option value="NEPRAVILNOSTI NA TOVORU">NEPRAVILNOSTI NA TOVORU</option>
					<option value="NEPRAVILNO PREHITEVANJE">NEPRAVILNO PREHITEVANJE</option>
					<option value="NEPRAVILNOSTI NA CESTI">NEPRAVILNOSTI NA CESTI</option>
					
					<option value="NEPRAVILNOSTI NA VOZILU">NEPRAVILNOSTI NA VOZILU</option>
					<option value="OSTALO">OSTALO</option>
				</select> <br/>
				
				<label for="tipNesrece">Izberite tipNesrece:</label>
				<select name="tipNesrece">
					<option value="OPLAŽENJE">OPLAŽENJE</option>
					<option value="POVOŽENJE PEŠCA">POVOŽENJE PEŠCA</option>
					<option value="ČELNO TRČENJE">ČELNO TRČENJE</option>
					
					<option value="TRČENJE V STOJEČE / PARKIRANO VOZILO">TRČENJE V STOJEČE / PARKIRANO VOZILO</option>
					<option value="PREVRNITEV VOZILA">PREVRNITEV VOZILA</option>
					<option value="TRČENJE V OBJEKT">TRČENJE V OBJEKT</option>
					
					<option value="BOČNO TRČENJE">BOČNO TRČENJE</option>
					<option value="NALETNO TRČENJE">NALETNO TRČENJE</option>
					<option value="POVOŽENJE ŽIVALI">POVOŽENJE ŽIVALI</option>
					
					<option value="OSTALO">OSTALO</option>
				</select> <br/>

				<input type="submit" value="Posodobi" />
			</form>
			<br/>
			
			<br/>
			<form id="izbrisiNeko" style="display: inline">
			<button type="button" onclick="izbrisiForma(); return false;">Izbrisi nesreco</button>
			</form>
			
			<br/>
			
			<form id="obrazecIzbris" onsubmit="izbrisiAdmin(); return false;" style="display: none">
				Zaporedna številka nesreče za izbris:
				<input type="number" name="zapStevilka" value="" required/><br/>
				<input type="submit" value="Izbriši." />
			</form>
			<br/><div id="odgovor2"></div><br/>
			
			<button type="button" onclick="dodajNovoForma(); return false;">Dodaj novo nesreco</button>
			<br/>
			<form id="obrazecNova" onsubmit="dodajNovoAdmin(); return false;" style="display: none">
				Zaporedna številka:
				<input type="number" name="zapStevilka" value="" required/><br/>
				Klasifikacija:
				<input type="text" name="klasifikacija" value="" required/><br/>
				Upravna Enota:
				<input type="text" name="upravnaEnota" value="" required/><br/>
				
				Datum:
				<input type="date" name="datum" value="" required/><br/>
				Naselje:
				<input type="text" name="naselje" value="" required/><br/>
				Cesta:
				<input type="text" name="cesta" value="" required/><br/>
				
				Opis kraja:
				<input type="text" name="opisKraj" value="" required/><br/>
				Vzrok nesreče:
				<input type="text" name="vzrokNesrece" value="" required/><br/>
				Tip nesreče:
				<input type="text" name="tipNesrece" value="" required/><br/>
				
				Vreme:
				<input type="text" name="vreme" value="" required/><br/>
				Stanje prometa:
				<input type="text" name="stanjePrometa" value="" required/><br/>
				Stanje vozišča:
				<input type="text" name="stanjeVozisca" value="" required/><br/>
				
				
				<input type="submit" value="Dodaj" />
			</form>
			
			<br/><div class="ex3" id="odgovor"></div><br/>
			
		</div>
	</body>
</html>