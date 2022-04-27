<?php
header('Content-Type: text/html; charset=UTF-8');
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
	print_r('Не POST методы не принимаются');
}
$errors = FALSE;
if(empty($_POST['name']) || empty($_POST['email']) || empty($_POST['year']) || empty($_POST['bio']) || empty($_POST['check-1']) || $_POST['check-1'] == false || !isset($_POST['super']) ){
	print_r('Заполните пустые поля!');
	exit();
}
$name = $_POST['name'];
$email = $_POST['email'];
$birth_year = $_POST['year'];
$pol = $_POST['radio-1'];
$limbs = intval($_POST['radio-2']);
$superpowers = $_POST['super'];
$bio= $_POST['bio'];

$bioreg = "/^\s*\w+[\w\s\.,-]*$/";
$reg = "/^\w+[\w\s-]*$/";
$mailreg = "/^[\w\.-]+@([\w-]+\.)+[\w-]{2,4}$/";
$list_sup = array('fly','sleep','run');

if(!preg_match($reg,$name)){
	print_r('Неверный формат имени');
	exit();
}
if($limbs > 4){
	print_r('Неверное количество(?) конечностей');
	exit();
}
if(!preg_match($bioreg,$bio)){
	print_r('Неверный формат биографии');
	exit();
}
if(!preg_match($mailreg,$email)){
	print_r('Неверный формат email');
	exit();
}
if($pol !== 'male' && $pol !== 'female'){
	print_r('Неверный формат пола');
	exit();
}
foreach($superpowers as $checking){
	if(array_search($checking,$list_sup)=== false){
			print_r('Неверный формат суперсил');
			exit();
	}
}

$user = 'u47577';
$pass = '9303559';
$db = new PDO('mysql:host=localhost;dbname=u47577', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
try {
  $stmt = $db->prepare("INSERT INTO application SET name=:name, email=:email, year=:byear, pol=:pol, konech=:limbs, biogr=:bio");
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':email', $email);
  $stmt->bindParam(':byear', $birth_year);
  $stmt->bindParam(':pol', $pol);
  $stmt->bindParam(':limbs', $limbs);
  $stmt->bindParam(':bio', $bio);
  if($stmt->execute()==false){
  print_r($stmt->errorCode());
  print_r($stmt->errorInfo());
  exit();
  }
  
  
  $id = $db->lastInsertId();
  $sppe= $db->prepare("INSERT INTO superp SET name=:name, per_id=:person");
  $sppe->bindParam(':person', $id);
  foreach($superpowers as $inserting){
	$sppe->bindParam(':name', $inserting);
	if($sppe->execute()==false){
	  print_r($sppe->errorCode());
	  print_r($sppe->errorInfo());
	  exit();
	}
  }
}
catch(PDOException $e){
  print('Error : ' . $e->getMessage());
  exit();
}

print_r("Данные отправлены в бд");
?>