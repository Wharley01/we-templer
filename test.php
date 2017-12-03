<?php
require_once "src/Templer.php";
use We\Templer;

try{
    $test = new Templer("template.wtp");
    $test->bind(["body" => ["text" => "This is the body text"],"title" => "This is the page title!"]);


    $test->Render();
}catch (Exception $e){
    echo $e->getMessage();
}

?>