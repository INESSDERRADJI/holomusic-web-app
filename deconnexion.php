<?php
//ouverture de la session 
session_start();

//destruction de la section
session_destroy();

//redirection vers la page d'accueil 
header('location: connexion.php');
exit;
?>