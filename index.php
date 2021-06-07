<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Welcome to Doodle</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <?php

echo 'max_execution_time = ' . (ini_get('max_execution_time')) . "\n";

?>

    <div class="wrapper indexPage">
        <div class="mainSection">

            <div class="logoContainer">
                <img src="./assets/images/FINDAM_lego.png" alt="findam_logo">
            </div>

            <div class="searchContainer">
                <form action="search.php" method="GET">
                    <input type="text" class="searchBox" name="term" placeholder="Start Typing">
                    <input type="submit" class="searchButton" value="Search">
                </form>
            </div>
        </div>
    </div> <br>



    <div class="footerContainer">
        <p class="copyright-text">Copyright &copy; 2021 All Rights Reserved by
            <a href="#">Mbiakop Clinton</a>.
        </p>
    </div>
</body>

</html>