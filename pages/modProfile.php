<?php
  //header per RESTful api
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  //ci prendiamo $conn per eseguire le connessioni e san() per sanificare input
  require_once "../services/connect.php";
  require_once "../services/sanitize.php";

  // Partenza e Verifica connessione
  if ($conn->connect_error) {
    die("Errore di connessione!");
  }

  //dunque questo file sara' strutturato con degli if per certa informazione, percio' l'unica cosa che ci servira' per ora
  //e' la selezione di cosa modificare dall'utente, e l'id del utente, che prenderemo col solito GET
  $modify = san($conn, $_GET['modify']);
  $id = san($conn, $_GET['id']);

  //questo e' la sezione dedicata al modificare l'email
  if ($modify == 'email') {

    //dobbiamo prendere il password e l'email a cui si vuole cambiare
    $newEmail = san($conn, $_GET['newEmail']);
    $password = hash('sha256', $_GET['password']);

    //per prima verifichiamo il password
    $sql = "SELECT password FROM utenti WHERE user_id = $id";
    $result = $conn->query($sql);
    $result = mysqli_fetch_assoc($result);

    if ($password == $result['password']) {

      //adesso che abbiamo verificato che il password e' vero possiamo fare i controlli sul nuovo email
      $valid = true;
      if (strlen($newEmail) < 4 || strlen($newEmail) > 70) $valid = false;
      if (strpos($newEmail, "liceofermipadova") == FALSE) $valid = false;

      //controlliamo anche se il nuovo email non e' gia' in utilizzo
      include '../services/emailExistFn.php';
      if (emailExist($conn, $newEmail) == true) $valid = false;

      //abbiamo verificato che tutto e' OK percio' possiamo procedere col cambiare l'email nel database
      if ($valid) {
        $sql = "UPDATE utenti SET email = '$newEmail' WHERE user_id = $id";
        if ($conn->query($sql) === TRUE) echo json_encode('true'); else echo json_encode('false');
      } else echo json_encode('false');

    } else echo json_encode('false');
  }

  //questo e' la sezione dedicata al modificare la foto profilo
  if ($modify == 'picture') {
    //questo sara' abbastanza semplice perche' non richiederemo neanche la password, tanto e' solo la foto profilo
    $newPicture = san($conn, $_GET['newPicture']);

    //facciamo il query sql
    $sql = "UPDATE utenti SET picture = '$newPicture' WHERE user_id = $id";
    if ($conn->query($sql) === TRUE) echo json_encode('true'); else echo json_encode('false');
  }

  //questo e' la sezione dedicata al modificare la descrizione
  if ($modify == 'description') {
    //come con la foto profilo richiederemo solo la descrizione a cui si vuole cambiare
    $newDescription = san($conn, $_GET['newDescription']);

    //facciamo il query sql, dopo aver controllato se la descrizione non e' troppo lunga
    if (strlen($newDescription) < 751) {
      $sql = "UPDATE utenti SET description = '$newDescription' WHERE user_id = $id";
      if ($conn->query($sql) === TRUE) echo json_encode('true'); else echo json_encode('false');
    } else echo json_encode('false');
  }

  //questa e' la sezione dedicata al modificare il password
  if ($modify == 'password') {
    $password = hash('sha256', $_GET['password']);
    $newPassword = hash('sha256', $_GET['newPassword']);
    $newPasswordConf = hash('sha256', $_GET['newPasswordConf']);

    //adesso facciamo tutti i controlli di formattazione del password
    $valid = true;
    if (strlen($newPassword) < 6 || strlen($newPassword) > 70) $valid = false;
    if ($newPassword != $newPasswordConf) $valid = false;

    //adesso dobbiamo vedere se il vecchio password e' valido
    $sql = "SELECT password FROM utenti WHERE user_id = $id";
    $result = $conn->query($sql);
    $result = mysqli_fetch_assoc($result);
    if ($result['password'] != $password) $valid = false;

    //adesso possiamo eseguire il query sql
    if ($valid) {
      $sql = "UPDATE utenti SET password = '$newPassword' WHERE user_id = $id";
      if ($conn->query($sql) === TRUE) echo json_encode('true'); else echo json_encode('false');
    } else echo json_encode('false');
  }
