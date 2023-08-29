<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Vmesnik prometnih nesreč</title>
		<link rel="stylesheet" type="text/css" href="stilP.css" />
		<script src="pov.js"></script>
		
		
		
		
	</head>
	<body>
		<div class="center">
			<?php include "Meni.html"?>
			<br/>
			<table id="tabela">
				<tr>
					<th>Vzrok Nesrece</th>
					<th>Tip nesrece</th>
					<th>Zaporedna stevilka nesrece</th>
					<th>Starost</th>
					<th>Vrsta udeleženca</th>
					
					<th>Vozniški staž v letih</th>
					<th>vrednost alkotesta</th>
				</tr>
			</table>
			<br/><br/>
			<form id="pridobiNesrece" onsubmit="pridobiUdelezence(); return false;" style="display: inline">
				
				Začetni datum:
				<input type="date" name="datum1" value="" required/><br/>
				
				Končni datum:
				<input type="date" name="datum2" value="" required/><br/>
				
				Vrednost alkotesta nad:
				<input type="number" value="vrednostAlkotesta" min="0" value="0" step = ".01"/><br>
				
				<input type="submit" value="Pridobi udeležence." /><br>
		
				
				Prikaži prvih 10 udeležencev (offset) / ali pusti prazno za prikaz števila povzročiteljev:
				<input type="number" name="steviloOffset" value=""/><br/>
				<input type="submit" value="Pridobi udeležence." /><br>
				
			</form>
			<br/>
			<div id="odgovor"></div>
	</body>
</html>