<?php
  //credo che era abbastanza ovvio ma questo e' una funzione per determinare se un email e' gia' in utilizzo
  function emailExist ($conn, $email) {

    //prima vorrei ricontrollare che la connessione mysql funziona pk se no teoricamente non trovera' niente e ci potrebbero
    //essere problemi
    if ($conn->connect_error) {
      die("Errore di connessione!");
    }

    //facciamo il query come sempre
    $sql = "SELECT user_id FROM utenti WHERE email='$email'";
    $result = $conn->query($sql);
    $result = mysqli_fetch_assoc($result);

    //se il query da un risultato l'email sara' gia' in utilizzo
    if ($result['user_id'] != NULL) return true; else return false;
  }
