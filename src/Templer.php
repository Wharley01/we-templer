<?php
/*
 * @Author <Sulaiman Adewale>
 * @Email <hackerslord96@gmail.com>
 * @Project <Simple template engine>
 *
 *
 */

namespace We;


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
        $this->replace_all_vars();
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

    public function assign_vars(){
        $rule = "{$this->open_tag}\s*{$this->var_sym}{$this->var_rule}\s*=\s*([^]]*)\s*{$this->close_tag}";

        $this->file_interpreted = preg_replace_callback("/{$rule}/",function($m){
            $rule = "\`{$this->var_sym}{$this->var_rule}\`";
            $content = $m[2];
            $content = preg_replace_callback("/{$rule}/",function($r){

                $return = "{$this->open_tag}{$this->var_sym}{$r[1]}{$this->close_tag}";
                return str_replace("\\","",$return);
            },$content);//convert in-text variable to real one
            $interpret = $this->interpret_vars($content);
            $this->data[$m[1]] = $interpret;
            return "";
        },$this->file_interpreted);
    }
    public function replace_all_vars(){
        $this->assign_vars();
        $this->file_interpreted = $this->interpret_vars($this->file_interpreted);
    }
    public function interpret_conditional_statement(){
        return $this->file_interpreted;
    }
    public function interpret_loops(){
        return $this->file_interpreted;
    }
    public function interpret_include_file(){
        return $this->file_interpreted;
    }


}