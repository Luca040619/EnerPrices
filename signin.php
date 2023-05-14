<!DOCTYPE HTML>
<html>

<head>
<meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signin</title>
  <link rel="icon" type="image/gif" href="Icons/Logo Progetto Gpoi V2.png">
  <link rel="stylesheet" href="styles.css">
</head>

<body>

  <?php
  // define variables and set to empty values
  $nomeErr = $cognomeErr = $usernameErr = $emailErr = $passwordErr = "";
  $nome = $cognome = $username = $email = $password = "";

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
    if (empty($_POST["nome"])) {
      $nomeErr = "Il nome è richiesto!";
    } else {
      $nome = test_input($_POST["nome"]);
      // check if name only contains letters and whitespace
      if (!preg_match("/^[a-zA-Z-' ]*$/", $nome)) {
        $nomeErr = "Il nome può contenere solo caratteri e spazi!";
      }
    }

    if (empty($_POST["cognome"])) {
      $cognomeErr = "Il cognome è richiesto!";
    } else {
      $cognome = test_input($_POST["cognome"]);
      // check if name only contains letters and whitespace
      if (!preg_match("/^[a-zA-Z-' ]*$/", $cognome)) {
        $cognomeErr = "Il cognome può contenere solo caratteri e spazi!";
      }
    }

    if (empty($_POST["username"])) {
      $usernameErr = "L'username è richiesto!";
    } else {
      $username = test_input($_POST["username"]);
      $query_username = "SELECT * FROM utenti WHERE Username = ?";
      $stmt = $connessione->prepare($query_username);
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
        $usernameErr = "Username già in uso!";
      }
    }

    if (empty($_POST["email"])) {
      $emailErr = "L'email è richiesta!";
    } else {
      $email = test_input($_POST["email"]);
      // check if e-mail address is well-formed
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Formato email non valido!";
      } else {
        $query_email = "SELECT * FROM utenti WHERE email = ?";
        $stmt = $connessione->prepare($query_email);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
          $emailErr = "L'email è già in uso!";
        }
      }
    }

    if (empty($_POST["password"])) {
      $passwordErr = "La password è obbligatoria!";
    } else {
      $password = test_input($_POST["password"]);
      // Hash + salt password
      $password_hash = password_hash($password, PASSWORD_DEFAULT);
    }

    // If no errors, insert into database
    if (empty($nomeErr) && empty($cognomeErr) && empty($usernameErr) && empty($emailErr) && empty($passwordErr)) {
      $premium = False;
      $query_insert = "INSERT INTO utenti (nome, cognome, username, password, email, premium) VALUES (?, ?, ?, ?, ?, ?)";
      $stmt = $connessione->prepare($query_insert);
      $stmt->bind_param("sssssi", $nome, $cognome, $username, $password_hash, $email, $premium);
      if ($stmt->execute()) {
        echo "Utente registrato con successo.";
        header('Location: login.php');
      } else {
        echo "Errore: " . $stmt->error;
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
    <h1 class="big-titles" id="login-title">Sign-in</h1>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
      <label for="nome" class="form-label">Nome </label><span class="error">*
        <?php echo $nomeErr; ?>
      </span><br>
      <input id="nome" name="nome" type="text" class="form-field" value="<?php echo $nome;?>"><br>
      <label for="cognome" class="form-label">Cognome </label><span class="error">*
        <?php echo $cognomeErr; ?>
      </span><br>
      <input id="cognome" name="cognome" type="text" class="form-field" value="<?php echo $cognome;?>"><br>
      <label for="user" class="form-label">Username </label><span class="error">*
        <?php echo $usernameErr; ?>
      </span><br>
      <input id="user" name="username" type="text" class="form-field" value="<?php echo $username;?>"><br>
      <label for="email" class="form-label">Email </label><span class="error">*
        <?php echo $emailErr; ?>
      </span><br>
      <input id="email" name="email" type="text" class="form-field" value="<?php echo $email;?>"><br>
      <label for="psw" class="form-label">Password </label><span class="error">*
        <?php echo $passwordErr; ?>
      </span><br>
      <input id="psw" name="password" type="password" class="form-field"><br>
      <button type="button" onclick='window.location.href = "login.php"' id="sign-up" class="login-btn">Login</button>
      <button type="submit" id="sign-in" class="login-btn" value="Submit">Sign in</button>
    </form>
  </div>
</body>

</html>