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

function pridobiUdelezence(){
	
	const data = formToJSON(document.getElementById("pridobiNesrece").elements);	// vsebino obrazca pretvorimo v objekt
	
	var JSONdata = JSON.stringify(data, null, "  ");
	console.log(JSONdata);
	
	var zeton = getCookiePrometna('token');
	if(zeton != null && zeton != "")
	{			
		//steviloNesrecDatuma();		
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
		else if(this.readyState == 4 && this.status == 201)
		{
			var odgovorJSON = JSON.parse(this.responseText);
			document.getElementById("odgovor").innerHTML="Med datuma je bilo toliko povzročiteljev nesreč: "+odgovorJSON["stev"] + ' ; z vrednostjo alkohola nad ' +document.getElementById("vrednostAlkotesta").value;
		}
		else if(this.readyState == 4 && this.status != 200)
		{
			document.getElementById("odgovor").innerHTML="Ni uspelo, nesrece med temi datumi ne obstajajo. : "+this.status;
		}
	};

	xmlhttp.open("POST", "pov", true);
	xmlhttp.send(JSONdata);
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

function steviloNesrecDatuma()
{
	const data = formToJSON(document.getElementById("pridobiNesrece").elements);	// vsebino obrazca pretvorimo v objekt
	var JSONdata = JSON.stringify(data, null, "  ");
	
}

const formToJSON = elements => [].reduce.call(elements, (data, element) => 
{
	if(element.name!="")
	{
		data[element.name] = element.value;
	}
  return data;
}, {});

