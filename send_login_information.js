/** Get form data and return an object
@param  {HTMLFormControlsCollection} elements
@return {Object}
*/
const formToJSON = elements => [].reduce.call(elements, (data, element) => 
{
    if(element.name!="")
    {
        data[element.name] = element.value;
    }
  return data;
}, {});
 
function sendLoginInformation()
{
    const data = formToJSON(document.getElementById("loginForm").elements);     // convert form data into object
    let JSONdata = JSON.stringify(data, null, "  ");	// convert object into a JSON string
    
    let xmlhttp = new XMLHttpRequest();	// create the (empty) HTTP request
     
    xmlhttp.onreadystatechange = function()    // Code to run depending on the response
    {
        if (this.readyState == 4 && this.status == 200)    // OK
        {
            var response = JSON.parse(xmlhttp.responseText);
            setCookie("token", response["token"], 1);
            window.location.replace("main_menu.php");
        }
        if(this.readyState == 4 && this.status != 200)     // not OK
        {
            console.log(this.status);
        }
    };
     
    xmlhttp.open("POST", "/AutoRecipe/login.php", true);   // Set method, URL of the request
    xmlhttp.send(JSONdata);
}