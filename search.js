
document.getElementById("searchInput").addEventListener("keyup", function(){
let query = this.value.trim();

if (query.length ===0){
    document.getElementById("searchResults").innerHTML = "";
    return;
}

//objet requete ajax
let req = new XMLHttpRequest();
req.open("GET", "/PROJET-WEB_INESS_LYNDA/Front-Office/search.php?query=" + encodeURIComponent(query), true);

//reponse
req.onload = function () {
    if (req.status === 200) {
        document.getElementById("searchResults").innerHTML = req.responseText;
    }
};

//envoi requete a php
req.send();
});