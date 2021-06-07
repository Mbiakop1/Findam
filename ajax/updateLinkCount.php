<?php

include("../config.php");

if(isset($_POST["linkId"])) {
   
    $query = $con->prepare("update sites set clicks = clicks + 1 where id=:id");
    $query->bindParam(":id", $_POST["linkId"]);
    $query->execute();

}
else {

    echo "no link passed to";
}



?>