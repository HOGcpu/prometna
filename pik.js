function setCookie(cname, cvalue, exdays) {
  // Set the parts of the cookie input string  
  const d = new Date();
  d.setTime(d.getTime() + (exdays*10000000));
  let expires = "expires="+ d.toUTCString();
  // Use the set parts as an input for setting a cookie
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

  function getCookie(cname) {
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