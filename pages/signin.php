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

  //prendiamo i dati contenuti, gli sanifichiamo e criptiamo il password
  $email = san($conn, $_GET['email']);
  $password = hash('sha256', $_GET['password']);

  //per prima controlliamo se l'email e' valido
  $sql = "SELECT user_id FROM utenti WHERE email = '$email' AND password = '$password'";
  $result = $conn->query($sql);

  //prendiamo i dati ottenuti e gli mettiamo in $return
  $result = mysqli_fetch_assoc($result);
  $return = $result['user_id'];
	
  //se $return non contiene nulla, le credenziali saranno state sbagliate
  if ($return == NULL) {
    $return = 'Email o Password inserito non è valido';
  }

  //e mandiamo indietro l'id o l'errore in JSON
  echo json_encode($return);