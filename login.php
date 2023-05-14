<!DOCTYPE HTML>
<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="icon" type="image/gif" href="Icons/Logo Progetto Gpoi V2.png">
  <link rel="stylesheet" href="styles.css">
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
          $_SESSION['nome'] = $user['nome'];
          $_SESSION['cognome'] = $user['cognome'];
          $_SESSION['username'] = $user['username'];
          $_SESSION['email'] = $user['email'];
          $_SESSION['premium'] = $user['premium'];
          $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR']; // non facciamoci caso

          header('Location: index.php');
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

  <img src="Icons/logoprova.png" alt="Logo Ener Prices" id="login-logo">
  <div id="login-container">
    <h1 class="big-titles" id="login-title">Login</h1>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
      <label for="user" class="form-label">Username </label><span class="error">*
        <?php echo $usernameErr; ?>
      </span><br>
      <input id="user" name="username" type="text" class="form-field"><br>
      <label for="psw" class="form-label">Password </label><span class="error">*
        <?php echo $passwordErr; ?>
      </span><br>
      <input id="psw" name="password" type="password" class="form-field"><br>
      <button type="button" onclick='window.location.href = "signin.php"' id="sign-up" class="login-btn">Sign in</button>
      <button type="submit" id="sign-in" class="login-btn" value="Submit">Login</button>
    </form>
  </div>

</body>

</html>