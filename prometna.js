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

function podatkiNesrece(){
	var vzdevek=document.getElementById("obrazec")['stevilka'].value;
	
	document.getElementById('posodobitev').style.display="none";
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
			prikaziZaUrejanje(odgovorJSON);
		}
		if(this.readyState == 4 && this.status != 200)
		{
			document.getElementById("odgovor").innerHTML="Ni uspelo: "+this.status;
		}
	};

	xmlhttp.open("GET", "prometna/"+vzdevek, true);
	xmlhttp.send();
}

function prikazi(odgovorJSON)
{
	var fragment = document.createDocumentFragment();		//zaradi učinkovitosti uporabimo fragment
	//fragment.appendChild("Podatki nesrece: ");
	for(var stolpec in odgovorJSON)		
	{
		var div = document.createElement("div");			// Za vsak stolpec ustvarimo lasten razdelek 'div'
		div.innerHTML = stolpec + ": " + odgovorJSON[stolpec];	//...in vanj zapišemo vrednost stolpca
		fragment.appendChild(div);							// Razdelek dodamo v fragment.
	}
	document.getElementById("odgovor").innerHTML="Podatki nesrece: ";			//pobrišemo morebitno obstoječo vsebino
	document.getElementById("odgovor").appendChild(fragment);	//fragment dodamo v pripravljen element
}

function prikaziZaUrejanje(odgovorJSON)
{
	var obrazec = document.getElementById('posodobitev');
	obrazec.style.display="block";
	
	obrazec.klasifikacija.value = odgovorJSON['klasifikacija'];
	obrazec.vzrokNesrece.value = odgovorJSON['vzrokNesrece'];
	obrazec.tipNesrece.value = odgovorJSON['tipNesrece'];
	
}

const formToJSON = elements => [].reduce.call(elements, (data, element) => 
{
	if(element.name!="")
	{
		data[element.name] = element.value;
	}
  return data;
}, {});




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
	const data = formToJSON(document.getElementById("obrazecNova").elements);	// vsebino obrazca pretvorimo v objekt
	var JSONdata = JSON.stringify(data, null, "  ");						// objekt pretvorimo v znakovni niz v formatu JSON
		
	var xmlhttp = new XMLHttpRequest();										// ustvarimo HTTP zahtevo
	 
	xmlhttp.onreadystatechange = function()									// določimo odziv v primeru različnih razpletov komunikacije
	{
		if (this.readyState == 4 && this.status == 201)						// zahteva je bila uspešno poslana, prišel je odgovor 201
		{
			document.getElementById("odgovor").innerHTML="Dodajanje uspelo!";
		}
		if(this.readyState == 4 && this.status != 201)						// zahteva je bila uspešno poslana, prišel je odgovor, ki ni 201
		{
			var jsonResponse = JSON.parse(this.responseText);
			var textResponse = "";
			for (let i = 0; i < jsonResponse["error_data"].length; i++) {
				textResponse += "<br>" + jsonResponse["error_data"][i];
			}
			document.getElementById("odgovor").innerHTML="Dodajanje ni uspelo: "+jsonResponse["error_message"]+textResponse;
		}
	};
	 
	xmlhttp.open("POST", "prometna", true);							// določimo metodo in URL zahteve, izberemo asinhrono zahtevo (true)
	xmlhttp.send(JSONdata);												// priložimo podatke in izvedemo zahtevo
}

function dodajNovoForma()
{
	document.getElementById("obrazecNova").style.display = "inline";														// priložimo podatke in izvedemo zahtevo
}

function izbrisiForma()
{
	document.getElementById("obrazecIzbris").style.display = "inline";														// priložimo podatke in izvedemo zahtevo
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

function odstraniNesreco()
{
	var zapStevilkaa=document.getElementById("obrazecIzbris")['zapStevilka'].value;	// vsebino obrazca pretvorimo v objekt
				
		
	var xmlhttp = new XMLHttpRequest();										// ustvarimo HTTP zahtevo
	 
	xmlhttp.onreadystatechange = function()									// določimo odziv v primeru različnih razpletov komunikacije
	{
		if (this.readyState == 4 && this.status == 204)						// zahteva je bila uspešno poslana, prišel je odgovor 201
		{
			document.getElementById("odgovor").innerHTML="Nesreca odstranjena!";
		}
		if(this.readyState == 4 && this.status != 204)						// zahteva je bila uspešno poslana, prišel je odgovor, ki ni 201
		{
			var jsonResponse = JSON.parse(this.responseText);
			var textResponse = "";
			for (let i = 0; i < jsonResponse["error_data"].length; i++) {
				textResponse += "<br>" + jsonResponse["error_data"][i];
			}
			document.getElementById("odgovor").innerHTML="Odstranjevanje ni uspelo: "+jsonResponse["error_message"]+textResponse;
		}
	};
	 
	//console.log(JSONdata);
	xmlhttp.open("DELETE", "prometna/"+zapStevilkaa, true);							// določimo metodo in URL zahteve, izberemo asinhrono zahtevo (true)
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
			odstraniNesreco();
			return;
		}
	};
	
	xmlhttp.open("POST", "adminCheck.php", true);
	xmlhttp.setRequestHeader("Accept", "application/json");
	xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
	xmlhttp.send(dataToSend);
}

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
		// try{
			// var odgovorJSON = JSON.parse(this.responseText);				
		// }
		// catch(e){
			// console.log("Napaka pri razčlenjevanju podatkov");
			// return;
		// }
		if (this.readyState == 4 && this.status == 204)						// zahteva je bila uspešno poslana, prišel je odgovor 204
		{
			document.getElementById("odgovor").innerHTML="Posodobitev uspela!";
		}
		if(this.readyState == 4 && this.status != 204)						// zahteva je bila uspešno poslana, prišel je odgovor, ki ni 204
		{
			document.getElementById("odgovor").innerHTML="Posodobitev ni uspela: "+odgovorJSON;
		}
	};
	
	var vzdevek = document.getElementById("obrazec")['stevilka'].value;
	
	xmlhttp.open("PUT", "prometna/"+vzdevek, true);							// določimo metodo in URL zahteve, izberemo asinhrono zahtevo (true)
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