<?php 
//Izveido reportus prieks debugging!!!!!!!!!
ini_set("display_errors, 1");
ini_set("display_startup_errors, 1");
error_reporting(E_ALL);

require_once "config.php";



$successMessage = "";//tukss strings lai attelotu zinas par uploadu statusu

if(isset($FILES["uploadedFile"])){

$file = $FILES["uploadedFile"];//saglaba visus datus par attiecigo failu
$fileName = $file["name"];//saglaba jeb dabu faila nosaukumu
$fileTmpName = $file["tmp_name"];// saglaba TEMP failu pirms augsupielades
$fileError = $file["error"];// izmet errorus ja tadi ir 0=kludas nav ;)

$uploadDir = "uploads/";// norada folder kur fails tiks glabats


if(!is_dir($uploadDir)){//checko ja upload mape nav izveidota tad izveidojam mapi
mkdir($uploadDir, 0777, true);// 777 = read + write + execute 0_0
}

$filePath = $uploadDir . basename($fileName);//pilnais path kur fails tiks augsupieladets

if($fileError === 0) {//parbauda kludas pirms augsupielades

if(move_uploaded_file($fileTmpName, $filePath)){// ja viss ir save tad temp failu parcelam uz isto lokaciju

$successMessage .= "File succesfully uploaded to folder.\n ";


$sql = "INSERT INTO sign_up (file_name , file_path) VALUES (:fileNAme , :filePath)";
$stmt = $conn->prepare($sql);

try {

$stmt->execute()([

    ':fileName' => $filePath,
    '"filePath' => $filePath

]);


}

}




}


}































?>