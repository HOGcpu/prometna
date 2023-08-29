<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Vmesnik prometnih nesreč</title>
		<link rel="stylesheet" type="text/css" href="stilP.css" />
		<script src="udelezenec.js"></script>
		
		
		
		
	</head>
	<body>
		<div class="center">
			<?php include "Meni.html"?>
			
			<table id="tabela">
				<tr>
					<th>Zaporedna Oseba</th>
					<th>Zaporedna številka nesreče</th>
					<th>Povzročitelj</th>
					<th>Starost</th>
					<th>Spol</th>
					
					<th>Stalno prebivalisce</th>
					<th>Drzavljanstvo</th>
					<th>Poskodba</th>
					<th>Vrsta udeleženca</th>
					
					<th>Varnostni pas</th>
					<th>Vozniški staž v letih</th>
					<th>Vrednost alkotesta</th>
				</tr>
			</table>
			<div id="odgovor"></div>
			
			
			
			<br/>
			<form id="obrazec" onsubmit="podatkiUdelezencev(); return false;" style="display: inline">
				<label for="stevilka">Udeleženci v prometni nesreči številka:</label>
				<input type="number" name="stevilka" required />
				<input type="submit" value="Prikaži" />
			</form>
			<br/>
			
			<br/>
			<form id="obrazecUdelezenca" onsubmit="podatkiUdelezenca(); return false;" style="display: inline">
				<label for="stevilka">številka udelezenca:</label>
				<input type="number" name="stevilka" required />
				<input type="submit" value="Prikaži udeleženca" />
			</form><br/>

			<form id="posodobitev" style="display: none">
				<label for="zapStevilka">številka nesreče</label>
				<input type="number" name="zapStevilka" required /><br/>
				Povzročitelj:
				<input type="text" name="povzrocitelj" value="" required/><br/>
				Starost:
				<input type="number" name="starost" value="" required/><br/>
				
				Spol:
				<input type="text" name="spol" value="" required/><br/>
				Stalno prebivalisce:
				<input type="text" name="stalnoPrebivalisce" value="" required/><br/>
				Državljanstvo:
				<input type="text" name="drzavljanstvo" value="" required/><br/>
				
				Poškodba:
				<input type="text" name="poskodba" value="" required/><br/>
				Vrsta udeleženca:
				<input type="text" name="vrstaUdelezenca" value="" required/><br/>
				Varnostni pas:
				<input type="text" name="varnostniPas" value="" required/><br/>
				
				Vozniski staž v letih:
				<input type="text" name="vozniskiStazVLetih" value="" required/><br/>
				Vrednost alkotesta:
				<input type="text" name="vrednostAlkotesta" value="" required/><br/>

				<input type="button" value="Posodobi" onclick="posodobiAdmin(); return false;" />
				<input type="button" value="Dodaj" onclick="dodajNovoAdmin(); return false;" />
				<input type="button" value="Izbriši" onclick="izbrisiAdmin(); return false;" />
			</form>
			<br/>
			<div id="odgovor2"></div>
	</body>
</html>