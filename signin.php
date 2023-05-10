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
      $query_insert = "INSERT INTO utenti (Nome, Cognome, Username, Password, Email, Premium) VALUES (?, ?, ?, ?, ?, ?)";
      $stmt = $connessione->prepare($query_insert);
      $stmt->bind_param("sssssb", $nome, $cognome, $username, $password_hash, $email, $premium);
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

  <h2>REGISTRAZIONE</h2>
  <p><span class="error">* required field</span></p>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    Nome: <input type="text" name="nome" value="<?php echo $nome; ?>">
    <span class="error">*
      <?php echo $nomeErr; ?>
    </span>
    <br><br>
    Cognome: <input type="text" name="cognome" value="<?php echo $cognome; ?>">
    <span class="error">*
      <?php echo $cognomeErr; ?>
    </span>
    <br><br>
    Username: <input type="text" name="username" value="<?php echo $username; ?>">
    <span class="error">*
      <?php echo $usernameErr; ?>
    </span>
    <br><br>
    E-mail: <input type="text" name="email" value="<?php echo $email; ?>">
    <span class="error">*
      <?php echo $emailErr; ?>
    </span>
    <br><br>
    Password: <input type="password" name="password" value="<?php echo $password; ?>">
    <span class="error">*
      <?php echo $passwordErr; ?>
    </span>
    <br><br>
    <input type="submit" name="submit" value="Submit">
    <input type="button" value="Reset" onclick="location.href='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>'">
  </form>

</body>

</html>