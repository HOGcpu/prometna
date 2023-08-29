<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Vmesnik prometnih nesreč</title>
		<link rel="stylesheet" type="text/css" href="stilP.css" />
		<script src="prometnaDatum.js"></script>
		
		
		
		
	</head>
	<body>
		<div class="center">
			<?php include "Meni.html"?>
			<br/>
			<table id="tabela">
				<tr>
					<th>Zaporedna stevilka</th>
					<th>Klasifikacija</th>
					<th>Upravna enota</th>
					<th>Datum</th>
				</tr>
			</table>
			<br/><br/>
			<form id="pridobiNesrece" onsubmit="pridobiNesrece(); return false;" style="display: inline">
				
				Začetni datum:
				<input type="date" name="datum1" value="" required/><br/>
				
				Končni datum:
				<input type="date" name="datum2" value="" required/><br/>
				
				<input type="submit" value="Pridobi število nesreč med datuma." /><br>
				
				Prikaži prvih 10 nesreč po n-ti nesreči (offset) / ali pusti prazno za prikaz števila nesreč:
				<input type="number" name="steviloOffset" value=""/><br/>
				<input type="submit" value="Pridobi nesreče." /><br>
				
			</form>
			<br/>
			<div id="odgovor"></div>
	</body>
</html>