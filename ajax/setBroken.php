<?php

include("../config.php");

if(isset($_POST["src"])) {


    $query = $con->prepare("update images set broken = 1 where imageURL = :src");
    $query->bindParam(":src", $_POST["src"]);
    $query->execute();
}   
else {

    echo "no src passed to";
}



?>