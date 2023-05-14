<?php
// Inizia la sessione
session_start();
// Distruggi tutte le variabili di sessione
$_SESSION = array();
// Infine, distruggi la sessione
session_destroy();
// elimina i cookie
setcookie("username", "", time() - 3600);
// Reindirizza l'utente alla pagina di login
header("location: login.php");
exit;
?>