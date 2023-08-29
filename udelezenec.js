function getCookiePrometna(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
}

function podatkiUdelezencev(){
	var vzdevek=document.getElementById("obrazec")['stevilka'].value;
	
	//document.getElementById('posodobitev').style.display="none";
	document.getElementById('odgovor').innerHTML="";
	
	// Funkcija je namenjena prilagoditvi vsebine strani glede na to, ali je uporabnik ze prijavljen ali se ne.
	
	var zeton = getCookiePrometna('token');
	if(zeton != null && zeton != "")
	{			
		document.getElementById("obrazec").style.display = "inline";		
	}
	else
	{
		window.location.replace("login.html");
	}
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			try{
				var odgovorJSON = JSON.parse(this.responseText);
				
			}
			catch(e){
				console.log("Napaka pri razčlenjevanju podatkov");
				return;
			}
			console.log(odgovorJSON);
				
			prikazi(odgovorJSON);
			
			//prikaziZaUrejanje(odgovorJSON);
		}
		if(this.readyState == 4 && this.status != 200)
		{
			document.getElementById("odgovor").innerHTML="Ni uspelo, nesreca ne obstaja. : "+this.status;
		}
	};

	xmlhttp.open("GET", "udelezenec/"+vzdevek, true);
	xmlhttp.send();
}

function prikazi(odgovorJSON){
	var fragment = document.createDocumentFragment();		//zaradi učinkovitosti uporabimo fragment
	var menu = document.getElementById("tabela");
	while (menu.childElementCount > 1) {
		menu.removeChild(menu.lastChild);
	}
	//za vsak element polja v JSONu ustvarimo novo vrstico v tabeli (tr)
	for (var i=0; i<odgovorJSON.length; i++) {
		var tr = document.createElement("tr");
		
		//za vsak stolpec v vrstici ustvarimo novo celico (td) ...
		for(var stolpec in odgovorJSON[i]){
			var td = document.createElement("td");
			td.innerHTML=odgovorJSON[i][stolpec];		
			tr.appendChild(td);						
		}
		fragment.appendChild(tr);					
	}
	document.getElementById("tabela").appendChild(fragment);	
}



const formToJSON = elements => [].reduce.call(elements, (data, element) => 
{
	if(element.name!="")
	{
		data[element.name] = element.value;
	}
  return data;
}, {});




function posodobiAdmin(){
	var zeton = getCookiePrometna('token');
	if(zeton != null && zeton != "")
	{			
		document.getElementById("obrazec").style.display = "inline";

		
		checkUserPosodobitev(zeton);
	}
	else
	{
		window.location.replace("login.html");
	}
}

function posodobiPodatke()
{
	const data = formToJSON(document.getElementById("posodobitev").elements);	// vsebino obrazca pretvorimo v objekt
	var JSONdata = JSON.stringify(data, null, "  ");						// objekt pretvorimo v znakovni niz v formatu JSON
	
	var xmlhttp = new XMLHttpRequest();										// ustvarimo HTTP zahtevo
	 
	xmlhttp.onreadystatechange = function()									// določimo odziv v primeru različnih razpletov komunikacije
	{
		if (this.readyState == 4 && this.status == 204)						// zahteva je bila uspešno poslana, prišel je odgovor 204
		{
			document.getElementById("odgovor2").innerHTML="Posodobitev uspela!";
		}
		if(this.readyState == 4 && this.status != 204)						// zahteva je bila uspešno poslana, prišel je odgovor, ki ni 204
		{

			var jsonResponse = JSON.parse(this.responseText);
			
			document.getElementById("odgovor2").innerHTML="Dodajanje ni uspelo: "+jsonResponse["error_message"]+jsonResponse["error_data"];
		}
		if(this.readyState == 4 && this.status == 400)
		{
			document.getElementById("odgovor2").innerHTML="Dodajanje ni uspelo: "+this.responseText;
		}
	};
	
	var vzdevek = document.getElementById("obrazecUdelezenca")['stevilka'].value;
	console.log(JSONdata);
	xmlhttp.open("PUT", "udelezenec/"+vzdevek, true);							// določimo metodo in URL zahteve, izberemo asinhrono zahtevo (true)
	xmlhttp.send(JSONdata);
}

function checkUserPosodobitev(zetonZaPreverbo)
{
	var dataToSend = "tokenTosendUpdate="+zetonZaPreverbo;
	
	var xmlhttp = new XMLHttpRequest();	
	
	xmlhttp.onreadystatechange = function()									// določimo odziv v primeru različnih razpletov komunikacije
	{
		
		if(this.readyState == 4 && this.status != 201)						// zahteva je bila uspešno poslana, prišel je odgovor, ki ni 201
		{
			//var jsonResponse = JSON.parse(this.responseText);
			
			document.getElementById("odgovor").innerHTML="Akcija ni uspela. Nisi administrator ali konfigurator "; //+jsonResponse["error_message"]+ " " +jsonResponse["error_data"];
			return;
		}
		else if (this.readyState == 4 && this.status == 201)
		{
			posodobiPodatke();
			return;
		}
	};
	
	xmlhttp.open("POST", "adminCheckUpdate.php", true);
	xmlhttp.setRequestHeader("Accept", "application/json");
	xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
	xmlhttp.send(dataToSend);
}

function podatkiUdelezenca(){
	var vzdevek=document.getElementById("obrazecUdelezenca")['stevilka'].value;
	
	document.getElementById('odgovor').innerHTML="";
	
	// Funkcija je namenjena prilagoditvi vsebine strani glede na to, ali je uporabnik ze prijavljen ali se ne.
	
	var zeton = getCookiePrometna('token');
	if(zeton != null && zeton != "")
	{			
		document.getElementById("posodobitev").style.display = "inline";		
	}
	else
	{
		window.location.replace("login.html");
	}
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			try{
				var odgovorJSON = JSON.parse(this.responseText);
				
			}
			catch(e){
				console.log("Napaka pri razčlenjevanju podatkov");
				return;
			}
			//console.log(odgovorJSON);
			prikaziZaUrejanje(odgovorJSON);
			document.getElementById("odgovor2").innerHTML = " ";
		}
		if(this.readyState == 4 && this.status != 200)
		{
			document.getElementById("odgovor2").innerHTML="Ni uspelo. Ta udelezenec ne obstaja.: "+this.status;
		}
	};

	xmlhttp.open("GET", "udelezenecEdina/"+vzdevek, true);
	xmlhttp.send();
}

function prikaziZaUrejanje(odgovorJSON)
{
	var obrazec = document.getElementById('posodobitev');
	obrazec.style.display="block";
	
	posodobitev.zapStevilka.value = odgovorJSON['zapStevilka'];
	
	posodobitev.povzrocitelj.value = odgovorJSON['povzrocitelj'];
	posodobitev.starost.value = odgovorJSON['starost'];
	posodobitev.spol.value = odgovorJSON['spol'];
	
	posodobitev.stalnoPrebivalisce.value = odgovorJSON['stalnoPrebivalisce'];
	posodobitev.drzavljanstvo.value = odgovorJSON['drzavljanstvo'];
	posodobitev.poskodba.value = odgovorJSON['poskodba'];
	
	posodobitev.vrstaUdelezenca.value = odgovorJSON['vrstaUdelezenca'];
	posodobitev.varnostniPas.value = odgovorJSON['varnostniPas'];
	posodobitev.vozniskiStazVLetih.value = odgovorJSON['vozniskiStazVLetih'];
	
	posodobitev.vrednostAlkotesta.value = odgovorJSON['vrednostAlkotesta'];
	
}

function izbrisiAdmin(){
	var zeton = getCookiePrometna('token');
	if(zeton != null && zeton != "")
	{			
		document.getElementById("obrazec").style.display = "inline";

		
		checkUserDelete(zeton);
	}
	else
	{
		window.location.replace("login.html");
	}
}

function odstraniUdelezenca()
{
	var zapStevilkaa=document.getElementById("obrazecUdelezenca")['stevilka'].value;	// vsebino obrazca pretvorimo v objekt
				
		
	var xmlhttp = new XMLHttpRequest();										// ustvarimo HTTP zahtevo
	 
	xmlhttp.onreadystatechange = function()									// določimo odziv v primeru različnih razpletov komunikacije
	{
		if (this.readyState == 4 && this.status == 204)						// zahteva je bila uspešno poslana, prišel je odgovor 201
		{
			document.getElementById("odgovor2").innerHTML="Udelezenec odstranjen!";
		}
		if(this.readyState == 4 && this.status != 204)						// zahteva je bila uspešno poslana, prišel je odgovor, ki ni 201
		{
			
			document.getElementById("odgovor2").innerHTML="Odstranjevanje ni uspelo: Udelezenec s to stevilko udelezenca ne obstaja.";
		}
	};
	 
	//console.log(JSONdata);
	xmlhttp.open("DELETE", "udelezenec/"+zapStevilkaa, true);							// določimo metodo in URL zahteve, izberemo asinhrono zahtevo (true)
	xmlhttp.send();												// priložimo podatke in izvedemo zahtevo
}

function checkUserDelete(zetonZaPreverbo)
{
	var dataToSend = "tokenTosend="+zetonZaPreverbo;
	
	var xmlhttp = new XMLHttpRequest();	
	
	xmlhttp.onreadystatechange = function()									// določimo odziv v primeru različnih razpletov komunikacije
	{
		
		if(this.readyState == 4 && this.status != 201)						// zahteva je bila uspešno poslana, prišel je odgovor, ki ni 201
		{
			var jsonResponse = JSON.parse(this.responseText);
			
			document.getElementById("odgovor").innerHTML="Akcija ni uspela. "+jsonResponse["error_message"]+ " " +jsonResponse["error_data"];
			return;
		}
		else if (this.readyState == 4 && this.status == 201)
		{
			odstraniUdelezenca();
			return;
		}
	};
	
	xmlhttp.open("POST", "adminCheck.php", true);
	xmlhttp.setRequestHeader("Accept", "application/json");
	xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
	xmlhttp.send(dataToSend);
}

function dodajNovoAdmin(){
	var zeton = getCookiePrometna('token');
	if(zeton != null && zeton != "")
	{			
		document.getElementById("obrazec").style.display = "inline";

		
		checkUser(zeton);
	}
	else
	{
		window.location.replace("login.html");
	}
}

function dodajNovo()
{
	const data = formToJSON(document.getElementById("posodobitev").elements);	// vsebino obrazca pretvorimo v objekt
	var JSONdata = JSON.stringify(data, null, "  ");						// objekt pretvorimo v znakovni niz v formatu JSON
		
	var xmlhttp = new XMLHttpRequest();										// ustvarimo HTTP zahtevo
	 
	xmlhttp.onreadystatechange = function()									// določimo odziv v primeru različnih razpletov komunikacije
	{
		if (this.readyState == 4 && this.status == 201)						// zahteva je bila uspešno poslana, prišel je odgovor 201
		{
			document.getElementById("odgovor2").innerHTML="Dodajanje uspelo!";
		}
		if(this.readyState == 4 && this.status != 201)						// zahteva je bila uspešno poslana, prišel je odgovor, ki ni 201
		{
			var jsonResponse = JSON.parse(this.responseText);
			var textResponse = "";
			for (let i = 0; i < jsonResponse["error_data"].length; i++) {
				textResponse += "<br>" + jsonResponse["error_data"][i];
			}
			document.getElementById("odgovor2").innerHTML="Dodajanje ni uspelo: "+jsonResponse["error_message"]+textResponse;
		}
	};
	console.log(JSONdata);
	xmlhttp.open("POST", "udelezenec", true);							// določimo metodo in URL zahteve, izberemo asinhrono zahtevo (true)
	xmlhttp.send(JSONdata);												// priložimo podatke in izvedemo zahtevo
}

function checkUser(zetonZaPreverbo)
{
	var dataToSend = "tokenTosend="+zetonZaPreverbo;
	
	var xmlhttp = new XMLHttpRequest();	
	
	xmlhttp.onreadystatechange = function()									// določimo odziv v primeru različnih razpletov komunikacije
	{
		
		if(this.readyState == 4 && this.status != 201)						// zahteva je bila uspešno poslana, prišel je odgovor, ki ni 201
		{
			var jsonResponse = JSON.parse(this.responseText);
			
			document.getElementById("odgovor").innerHTML="Akcija ni uspela. "+jsonResponse["error_message"]+ " " +jsonResponse["error_data"];
			return;
		}
		else if (this.readyState == 4 && this.status == 201)
		{
			dodajNovo();
			return;
		}
	};
	
	xmlhttp.open("POST", "adminCheck.php", true);
	xmlhttp.setRequestHeader("Accept", "application/json");
	xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
	xmlhttp.send(dataToSend);
}