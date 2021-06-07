<?php
// ini_set('max_execution_time', 0);
// set_time_limit(1800);
// ini_set('memory_limit', '-1');




include("config.php");
include("./classes/DomDocumentParser.php");

$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImages = array();

function linkExist($url){

    global $con;

    $query = $con->prepare("select * from sites where url = :url");

    $query->bindParam(":url", $url);
    $query->execute();


    return $query->rowCount() != 0;
    
}


function ImageExist($imageUrl){

    global $con;

    $query = $con->prepare("select * from images where imageUrl = :imageUrl");

    $query->bindParam(":imageUrl", $imageUrl);
    $query->execute();


    return $query->rowCount() != 0;
    
}




function insertLink($url, $title, $description, $keywords){

    global $con;

    $query = $con->prepare("insert into sites(url, title, description, keywords)
    
                           values(:url, :title, :description, :keywords)");

    $query->bindParam(":url", $url);
    $query->bindParam(":title", $title);                       
    $query->bindParam(":description", $description);                       
    $query->bindParam(":keywords", $keywords); 
    
    
    return $query->execute();
    

}



function insertImage($url, $imgSrc, $alt, $imgTitle){

    global $con;

    $query = $con->prepare("insert into images(siteUrl, imageUrl, alt, title)
    
                           values(:siteUrl, :imageUrl, :alt, :title)");

    $query->bindParam(":siteUrl", $url);
    $query->bindParam(":imageUrl", $imgSrc);                       
    $query->bindParam(":alt", $alt);                       
    $query->bindParam(":title", $imgTitle); 
    
    
    return $query->execute();
    

}


function createLink($src, $url){

    $scheme = parse_url($url)["scheme"]; //http
    $host = parse_url($url)["host"];  // www.reecekenney.com

    if(substr($src, 0, 2) == "//"){
        $src = $scheme . ":" .$src;
    }
    else  if(substr($src, 0, 1) == "/") {
          $src = $scheme . "://" . $host . $src;
    }

    else if(substr($src, 0, 2) == "./"){
        $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
    }  
      else if(substr($src, 0, 3) == "../"){

        $src = $scheme . "://" . $host . "/" . $src;

      } 

       else if(substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http"){

        $src = $scheme . "://" . $host . "/" . $src;

      } 


    return $src;
}

function getDetails($url) {

    global $alreadyFoundImages;
  $parser = new DomDocumentParser($url);

  $titleArray = $parser->getTitleTags();

  if(sizeof($titleArray) == 0 || $titleArray->item(0) == null){
     return;
  }
  $title = $titleArray->item(0)->nodeValue;

  $title = str_replace("\n", "", $title);

  if($title ==""){
      return;
  }

  $description = "";
  $keywords = "";

  $metasArray = $parser->getMetaTags();

  foreach($metasArray as $meta){

    if($meta->getAttribute("name") == "describtion"){
        $description = $meta->getAttribute("content");
    }

    if($meta->getAttribute("name") == "keywords"){
        $keywords = $meta->getAttribute("content");
    }

  }

  $description = str_replace("\n", "", $description);
  $keywords = str_replace("\n", "", $keywords);

if(linkExist($url)){
    // echo "$url already exists <br>";

}  

else if( insertLink($url, $title, $description, $keywords)){
    echo "SUCCESS: $url <br>";
}

else {
    echo "ERROR: Failed to insert $url <br>";
}



$imageArray = $parser->getImages();

foreach($imageArray as $image){
    $imgSrc = $image->getAttribute("src");
    $alt = $image->getAttribute("alt");
    $imgTitle = $image->getAttribute("title");

    if(!$imgTitle && !$alt){
        continue;
    }

    $imgSrc = createLink($imgSrc, $url);

      



    if(!in_array($imgSrc, $alreadyFoundImages) && !ImageExist($imgSrc)){
        $alreadyFoundImages[] = $imgSrc;

        insertImage($url, $imgSrc, $alt, $imgTitle);
    }
    else {
        // echo "$imgSrc already exist <br>";
    
    continue;    
    }
}
  //insertLink($url, $title, $description, $keywords);

  //echo "URL: $url, Description:$description,  Keywords:$keywords,  <br>";

}


function followLinks($url){

     global  $alreadyCrawled;
    global  $crawling;
 
    $parser = new DomDocumentParser($url);
    $linklist = $parser->getLinks();

    foreach($linklist as $link){
        $href = $link->getAttribute('href');

        if(strpos($href, "#") !== false){
            continue;
        }
        else if(substr($href, 0, 11) == "javascript:") {
            continue;
        }

       $href =  createLink($href, $url);

       if(!in_array($href, $alreadyCrawled)){
           $alreadyCrawled[] = $href;
           $crawling[] = $href;

           getDetails($href);
       }
         //else return; 
        
        //echo $href . "<br>";
    }

     array_shift($crawling);

     foreach($crawling as $site){
         followLinks($site);
     }

}

$startUrl = "https://en.wikipedia.org/wiki/Corona";

followLinks($startUrl);

?>