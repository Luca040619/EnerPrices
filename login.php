<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "Scuola";

$connessione = new mysqli($hostname, $username, $password, $database);
// connessione al server sql con selezione del database
if (!$connessione) {
    die("Errore di connessione: " . $connessione->connect_error);
}

$user = test_input($_POST['username']);
$psw = test_input($_POST['password']);

// Verifica se i campi sono vuoti
if (empty($user) || empty($psw)) {
    die("Errore: tutti i campi sono obbligatori.");
}

// Verifica se l'username esiste
$query_username = "SELECT * FROM utenti WHERE Username = ?";
$stmt = $connessione->prepare($query_username);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // l'utente esiste, ora verifica la password
    $utente = $result->fetch_assoc();
    if (password_verify($psw, $utente['password'])) {
        // avvia la sessione
        session_start();
        // salva le informazioni dell'utente nella sessione
        $_SESSION['username'] = $user;
        $_SESSION['email'] = $utente['email'];
        $_SESSION['premium'] = $utente['premium'];
        echo "Accesso effettuato con successo.";
    } else {
        echo "Errore: password errata.";
    }
} else {
    echo "Errore: l'username non esiste.";
}
$stmt->close();
$connessione->close();

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>