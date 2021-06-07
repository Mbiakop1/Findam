<?php


// ini_set('max_execution_time', 0);
// set_time_limit(1800);
// ini_set('memory_limit', '-1');




class DomDocumentParser {
    
    private $doc;

    public function __construct($url){

        echo $url;
    
        $options = array(
            'http'=>array('method'=>"GET", 'header'=>"User-Agent: FindamBot/0.1\n")
        );

        $context = stream_context_create($options);

        $this->doc = new DomDocument();

        @$this->doc->loadHTML(file_get_contents($url, false, $context));

    }
    
    public function getLinks() {
        return $this->doc->getElementsByTagName("a");
    }

    
    public function getTitleTags() {
        return $this->doc->getElementsByTagName("title");
    }

     public function getMetaTags() {
        return $this->doc->getElementsByTagName("meta");
    }

     public function getImages() {
        return $this->doc->getElementsByTagName("img");
    }
}

?>