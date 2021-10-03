<?php
$bd = new SQLite3('edificios/RamTomarDB.sqlite');
 if(isset($_POST['username'],$_POST['password'])) {

 //gather all the data from the submission process
 $username   = $_POST['username'];
 $password   = $_POST['password'];

 $password = md5($password);

 $check_user = $bd->query("SELECT username, password FROM Utilizadores WHERE username = '$username' and password = '$password'");

 $checked_user = 0;

 while($row = $check_user->fetchArray()) {
    var_dump($row);
    $checked_user += 1;
}
if($checked_user == 0) {
   echo"<script>alert('Username or password is wrong')</script>";
}else{
// Start the session
session_start();
$_SESSION["username"] = $username;
header("Location: http://ram.ipt.pt");
}
 }
