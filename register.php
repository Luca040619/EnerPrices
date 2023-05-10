<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "Scuola";

$nameErr = $emailErr = $genderErr = $websiteErr = "";
$connessione = new mysqli($hostname, $username, $password, $database);
// connessione al server sql con selezione del database
if (!$connessione) {
    die("Errore di connessione: " . $connessione->connect_error);
}

$nome = $_POST['nome'];
$cognome = $_POST['cognome'];
$user = $_POST['username'];
$email = $_POST['email'];
$psw = $_POST['password']; //salt
$premium = False;

// Verifica se i campi sono vuoti
if (empty($nome) || empty($cognome) || empty($user) || empty($psw) || empty($email)) {
    die("Errore: tutti i campi sono obbligatori.");
}

// Verifica se l'email esiste già
$query_email = "SELECT * FROM utenti WHERE Email = ?";
$statement = $connessione->prepare($query_email);
$statement->bind_param("s", $email);
$statement->execute();
$result = $statement->get_result();
if ($result->num_rows > 0) {
    die("Errore: l'email è già in uso.");
}

// Verifica se l'username esiste già
$query_username = "SELECT * FROM utenti WHERE Username = ?";
$statement = $connessione->prepare($query_username);
$statement->bind_param("s", $user);
$statement->execute();
$result = $statement->get_result();

if ($result->num_rows > 0) {
    die("Errore: l'username è già in uso.");
}

// Esegui l'hash della password
$psw_hash = password_hash($psw, PASSWORD_DEFAULT);

// Inserisci l'utente nel database
$query_insert = "INSERT INTO utenti (Nome, Cognome, Username, Password, Email, Premium) VALUES (?, ?, ?, ?, ?, ?)";
$statement = $connessione->prepare($query_insert);
$statement->bind_param("sssssb", $nome, $cognome, $user, $psw_hash, $email, $premium);
if ($statement->execute()) {
    echo "Utente registrato con successo.";
    header("location: login.html");
} else {
    echo "Errore: " . $statement->error;
}
$statement->close();
$connessione->close();
?>