<?php
$bools = array(
    "true",
    "true",
    "true",
    "false",
);
$logic = array(
    "AND",
    "AND",
    "OR"
);
function boolTest($bools,$logic){
            $flat = [];
            for ($i = 0; $i < count($bools);$i ++){
             $flat[] = $bools[$i];
             if(@$logic[$i]){
                 $flat[] =  $logic[$i];
             }
            }
            $bool = "return ".join(" ",$flat).";";
           return eval($bool);

}
function data_type_test($data){
        if(preg_match('/^("|\')(.*)("|\')$/',$data,$string)){
            echo "Datatype: String";

            if($string[1] == "\""){
               return str_replace('"','\"',$string[2]);
            }elseif ($string[1] == "'"){
                return str_replace("'","\'",$string[2]);
            }
        }elseif(preg_match('/^(\d*)$/',$data,$integer)){
            echo "Datatype: integer";
            return $integer[1];


        }elseif(preg_match('/^(\d*\.\d*)$/',$data,$float)){
            echo "Datatype: Floating point number";
            return $float[1];

        }else{
            echo "Invalid data type";
        }
        return false;
}
print_r(data_type_test(23.45));
?>