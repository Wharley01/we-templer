<?php
require_once "src/Templer.php";
use We\Templer;

try{
    $test = new Templer("template.wtp");
    $test->Render();
}catch (Exception $e){
    echo $e->getMessage();
}

?>