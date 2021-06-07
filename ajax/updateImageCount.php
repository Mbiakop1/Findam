<?php

include("../config.php");

if(isset($_POST["imageUrl"])) {
   
    $query = $con->prepare("update images set clicks = clicks + 1 where imageUrl=:imageUrl");
    $query->bindParam(":imageUrl", $_POST["imageUrl"]);
    $query->execute();

}
else {

    echo "no imageUrl  passed to";
}



?>