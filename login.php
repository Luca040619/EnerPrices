<!DOCTYPE HTML>
<html>

<head>
  <style>
    .error {
      color: #FF0000;
    }
  </style>
</head>

<body>

  <?php
  // define variables and set to empty values
  $usernameErr = $passwordErr = "";
  $username = $password = "";

  $hostname = "localhost";
  $username_db = "root";
  $password_db = "";
  $database = "Scuola";

  $connessione = new mysqli($hostname, $username_db, $password_db, $database);

  // connessione al server sql con selezione del database
  if ($connessione->connect_error) {
    die("Errore di connessione: " . $connessione->connect_error);
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
      $usernameErr = "L'username è richiesto!";
    } else {
      $username = test_input($_POST["username"]);
    }

    if (empty($_POST["password"])) {
      $passwordErr = "La password è obbligatoria!";
    } else {
      $password = test_input($_POST["password"]);
    }

    // If no errors, check credentials in database
    if (empty($usernameErr) && empty($passwordErr)) {
        $query_check = "SELECT * FROM utenti WHERE username = ?";
        $stmt = $connessione->prepare($query_check);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // User exists, check password
            $user = $result->fetch_assoc();
            if (password_verify($password, $user["password"])) {
                // Password is correct, start the session
                session_start();
                // Save user data in session variables
                $_SESSION['Nome'] = $user['Nome'];
                $_SESSION['Cognome'] = $user['Cognome'];
                $_SESSION['Username'] = $user['Username'];
                $_SESSION['Email'] = $user['Email'];
                $_SESSION['Premium'] = $user['Premium'];
                
                echo "Login riuscito!";
            } else {
                $passwordErr = "Password non valida!";
            }
        } else {
            $usernameErr = "Username non valido!";
        }
    }
}
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<h2>LOGIN</h2>
<p><span class="error">* required field</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    Username: <input type="text" name="username" value="<?php echo $username; ?>">
    <span class="error">* <?php echo $usernameErr; ?></span>
    <br><br>
    Password: <input type="password" name="password" value="<?php echo $password; ?>">
    <span class="error">* <?php echo $passwordErr; ?></span>
    <br><br>
    <input type="submit" name="submit" value="Submit">
    <input type="button" value="Reset" onclick="location.href='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>'">
</form>

</body>

</html>