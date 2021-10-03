<?php

$bd = new SQLite3('edificios/RamTomarDB.sqlite');
echo "asdasdas";
 if(isset($_POST['username'],$_POST['password'],$_POST['email'])) {

 //gather all the data from the submission process
 $username   = $_POST['username'];
 $email      = $_POST['email'];
 $password   = $_POST['password'];

 $password = md5($password);

 if(isset($_POST['g-recaptcha-response'])){
    $captcha=$_POST['g-recaptcha-response'];
  }
  if(!$captcha){
    header("Location: http://ram.ipt.pt/");
    echo '<h2>Please check the the captcha form.</h2>';
    exit;
  }
  $secretKey = "6LfDdKMZAAAAAO85Q0HbxnEQjxvh5GurjUHwDhZs";
  $ip = $_SERVER['REMOTE_ADDR'];
  // post request to server
  $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
  $response = file_get_contents($url);
  $responseKeys = json_decode($response,true);
  // should return JSON with success as true
  if($responseKeys["success"]) {

 $check_email = $bd->query("SELECT email FROM Utilizadores WHERE email = '$email'") ;

 $checked_email = 0;

 while($row = $check_email->fetchArray()) {
    $checked_email += 1;
}
 if($checked_email != 0) {

  echo"<script>alert('Sorry that email is already taken')</script>";

 }

 else {

 $query = "INSERT INTO Utilizadores(username, email, `password`) VALUES('".$username."','".$email."','".$password."')";

 $result = $bd->exec($query);
 header("Location: http://ram.ipt.pt/");
 }
 }
 }