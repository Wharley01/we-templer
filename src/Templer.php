<?php
/*
 * @Author <Sulaiman Adewale>
 * @Email <hackerslord96@gmail.com>
 * @Project <Simple template engine>
 *
 *
 */



namespace We {

    require_once "tools.php";
    class Templer
    {
        public $file;
        public $data = [];
        public $file_raw_content;
        public $file_interpreted;
        public $open_tag = "\[";
        public $close_tag = "\]";
        public $var_sym = "\%";
        public $var_rule = "([\w.]*)";
        public function __construct($file)
        {
            $this->file = $file;
            $raw = file_get_contents($file);
            $this->file_raw_content = $raw;
            $this->file_interpreted = $raw;
            return $this;
        }
        public function bind($data){
            if(!$this->data){
                $this->data = $data;
            }else{
                foreach ($data as $key => $val){
                    $this->data[$key] = $val;
                }
            }
            return $this;
        }
        public function Render(){
//            clean();
            $this->replace_all_vars();
            $this->interpret_conditional_statement();
            echo $this->file_interpreted;
        }
        public function interpret_vars($raw){
            $rule = "{$this->open_tag}\s*{$this->var_sym}{$this->var_rule}{$this->close_tag}";
            $file_interpreted = $raw;
            $file_interpreted = preg_replace_callback("/{$rule}/",function($m){
                $var = @$this->data[$m[1]];
                if (preg_match("/\./",$m[1])){
                    $break_w = explode(".",$m[1]);
                    $root = $this->data[$break_w[0]];
                    for($i = 1;$i < count($break_w);$i++){
                        if(!@$root[$break_w[$i]]){
                            throw new \Exception("Error: Undefined index '{$break_w[$i]}' in '{$break_w[$i-1]}'");
                        }
                        $root = $root[$break_w[$i]];
                    }

                    return $root;
                }elseif(!$var){
                    throw new \Exception("Error: Undefined variable name \"{$m[0]}\"");
                }else{

                    return $var;
                }
            },$file_interpreted);

            return $file_interpreted;
        }
public  function get_var_val($var){

    if (preg_match("/\./",$var)){
        $break_w = explode(".",$var);
        $root = $this->data[$break_w[0]];
        for($i = 1;$i < count($break_w);$i++){
            if(!@$root[$break_w[$i]]){
                throw new \Exception("Error: Undefined index '{$break_w[$i]}' in '{$break_w[$i-1]}'");
            }
            $root = $root[$break_w[$i]];
        }
        //echo $root;
        return $root;
    }elseif(!$this->data[$var]){
        throw new \Exception("Error: Undefined variable name \"{$this->data[$var]}\"");
    }else{
        //echo $this->data[$var];
        return $this->data[$var];
    }
}
public function inline_variabe_replace($string){
    $rule = "\`{$this->var_sym}{$this->var_rule}\`";
    $return = $string;
            $return = preg_replace_callback("/{$rule}/",function($matches){

                return $this->get_var_val($matches[1]);
            },$return);
            //echo $return;
            return $return;
        }
 public function assign_vars(){
            $rule = "{$this->open_tag}\s*{$this->var_sym}{$this->var_rule}\s*=\s*([^]]*)\s*{$this->close_tag}";

            $this->file_interpreted = preg_replace_callback("/{$rule}/",function($m){

                $content = $this->strip_in_string_vars($m[2]);
                $interpret = $this->interpret_vars($content);
                $this->data[$m[1]] = $interpret;
                return "";
            },$this->file_interpreted);
        }
        public function replace_all_vars(){
            $this->assign_vars();
            $this->file_interpreted = $this->interpret_vars($this->file_interpreted);
        }
        public function strip_in_string_vars($string){
            $rule = "\`{$this->var_sym}{$this->var_rule}\`";
            $content = $string;
            $content = preg_replace_callback("/{$rule}/",function($r){

                $return = "{$this->open_tag}{$this->var_sym}{$r[1]}{$this->close_tag}";
                return str_replace("\\","",$return);
            },$content);//convert in-text variable to real one
            return $content;
        }
        public function interpret_conditional_statement(){
            //method to interpret a conditional statement
            //find if statement
            $rule = "{$this->open_tag}if\s*\{(.*)}{$this->close_tag}\s*\n*([\w\n\W]*){$this->open_tag}\/if{$this->close_tag}";
            $rule = preg_replace("/\n/","\\n",$rule);
            $this->file_interpreted = preg_replace_callback("/{$rule}/",function($m){
                //now breake the conditions
                $bc = preg_split("/}\s*(AND|OR)\s*{/",$m[1],-1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                $cd = [];
                $bl = [];
                $lo = array(
                    "AND",
                    "OR",
                    "||",
                    "&&"
                );//accpted logical operators
                for ($i = 0; $i < count($bc); $i++){//re
                    if(in_array($bc[$i],$lo)){
                        $bl[] = $bc[$i];
                    }else{// check if condition is true
                       // echo $bc[$i].PHP_EOL;
        $data_rule = "((\"|'|`)(.*)(\"|'|`)|(\d+\.\d+)|(\d+)|true|false|TRUE|FALSE)\s*(=|==|[<>]+)\s*((\"|'|`)(.*)(\"|'|`)|(\d+\.\d+)|(\d+)|true|false|TRUE|FALSE)";

            if(preg_match("/^{$data_rule}$/",$bc[$i],$matches)){

                $left_val = $this->inline_variabe_replace($matches[1]);

                $left_val = data_type_test($left_val);

                $right_val = $this->inline_variabe_replace($matches[8]);
                $right_val = data_type_test($right_val);



                echo PHP_EOL."LEFT: ".$left_val."RIGHT: ".$right_val.PHP_EOL;

                    //print_r($matches);


            }
                    }
                }

                //print_r($bc);

            },$this->file_interpreted);

            return $this->file_interpreted;
        }
        public function interpret_loops(){
            return $this->file_interpreted;
        }
        public function interpret_include_file(){
            return $this->file_interpreted;
        }


    }
}