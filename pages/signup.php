<?php
  //header per RESTful api
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  //ci prendiamo $conn per eseguire le connessioni e san() per sanificare input e emailExist per validazione mail piu' tardi
  require_once "../services/connect.php";
  require_once "../services/sanitize.php";
  require_once "../services/emailExistFn.php";

  // Partenza e Verifica connessione
  if ($conn->connect_error) {
    die("Errore di connessione!");
  }

  //prendiamo i dati contenuti, gli sanifichiamo e criptiamo il password
  $username = san($conn, $_GET['username']);
  $email = san($conn, $_GET['email']);
  $password = hash('sha256', $_GET['password']);
  $passwordConf = hash('sha256', $_GET['passwordConf']);

  //$valid ci servira' per verificare se i valori sono validi, comunque queste confermazioni verrano fatte anche dal cliente
  //per sicurezza ottimale e per usare meno bandwith mandando messaggi di errore
  $valid = true;

  //per prima verifichiamo il username
  if (strlen($username) < 4 || strlen($username) > 25) $valid = false;

  //poi l'email
  if (strlen($email) < 4 || strlen($email) > 70) $valid = false;
  if (strpos($email, "liceofermipadova") == FALSE) $valid = false;
  //dobbiamo anche verificare che l'email non e' gia' in utilizzo
  //siccome anche il cliente dovra' verificare questa cosa lo mettero' su una pagina diversa in un function
  if (emailExist($conn, $email) == true) $valid = false;

  //poi il password
  if (strlen($password) < 6 || strlen($password) > 70) $valid = false;
  if ($password != $passwordConf) $valid = false;

  //adesso che abbiamo confermato che i dati sono validi, possiamo mettere i dati nel database
  if ($valid) {
    $sql = "INSERT INTO utenti (username, password, email) VALUES ('$username', '$password', '$email')";

    if ($conn->query($sql) === TRUE) echo json_encode('true'); else echo json_encode('false');
  } else echo json_encode('false');
