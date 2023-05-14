<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo</title>
    <link rel="icon" type="image/gif" href="Icons/Logo Progetto Gpoi V2.png">
    <link rel="stylesheet" href="styles.css">

    <style>
        .form-field {
            margin-bottom: 7px;
        }
    </style>
</head>

<body>
    <?php
    session_start();

    $nome = $_SESSION['nome'];
    $cognome = $_SESSION['cognome'];
    $username = $_SESSION['username'];
    $email = $_SESSION['email'];
    $premium = $_SESSION['premium'];

    if ($nome == '') {
        header('Location: login.php');
    }

    if ($premium)
        $premium_text = 'Si';
    else
        $premium_text = 'No';

    ?>

    <img src="Icons/logoprova.png" alt="Logo Ener Prices" id="login-logo">
    <div id="login-container">
        <h1 class="big-titles" id="login-title">Profilo Utente</h1>
        <label for="nome" class="form-label">Nome</label>
        <div id="nome" class="form-field">
            <?php echo $nome; ?>
        </div>

        <label for="cognome" class="form-label">Cognome</label>
        <div id="cognome" class="form-field">
            <?php echo $cognome; ?>
        </div>

        <label for="username" class="form-label">Nome Utente</label>
        <div id="username" class="form-field">
            <?php echo $username; ?>
        </div>

        <label for="email" class="form-label">Email</label>
        <div id="email" class="form-field">
            <?php echo $email; ?>
        </div>

        <label for="premium" class="form-label">Premium</label>
        <div id="premium" class="form-field">
            <?php echo $premium_text; ?>
        </div>

        <button class="login-btn" id="log-out" onclick='window.location.href = "logout.php"'
            tex-align="center">Log-out</button>
        <button type="button" id="sign-in" class="login-btn" onclick='window.location.href = "index.php"'>
            Homepage</button>
    </div>
</body>

</html>