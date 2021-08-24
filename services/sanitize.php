<?php
  //prendiamo connect.php per verificare anche la connessione
  require_once 'connect.php';

  //funzione di sanificazione
  function san ($conn, $string) {
    //qui guardiamo se qualcuno ha provato ad includere elementi di html e se gli htmlentities poi sono compatibili con sql
    //censuriamo anche le parolaccie
    return htmlentities(mysql_fix_string($conn, censor($string)));
  }

  //funzione per evitare mysql injection
  function mysql_fix_string($conn, $string) {
    if (get_magic_quotes_gpc()) $string = stripslashes ($string);
    return $conn->real_escape_string($string);
  }

  //vogliamo anche che tutte le parolaccie vengono censurate
  function censor ($string) {
    //la lista di parolaccie verrano messe in un file, vorrei che anche quelli che non sanno programmare lo potranno modificare, il file sara' di tipo JSON percio' dovra' essere decodato
    $censorJSON = file_get_contents("../extra/swear.json");
    $censor = json_decode($censorJSON, true);
    $return = $string;

    //dobbiamo controllare uno per uno se il messaggio ha una certa parolaccia nella lista
    for ($i = sizeof($censor); $i != 0; $i--) {
      //il preg_match guarda se il testo contiene una certa parola
      if(preg_match("/{$censor[$i-1]}/i", $string)) {
        $return = "Questo messaggio conteneva una parolaccia. Non usare parolaccie nei vostri messaggi per favore";
      }
    }
    return $return;
  }
?>
