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
        $this->interpret_vars();
        echo $this->file_interpreted;
    }
    public function interpret_vars(){
        $rule = "{$this->open_tag}\s*{$this->var_sym}{$this->var_rule}{$this->close_tag}";
        $this->file_interpreted = preg_replace_callback("/{$rule}/",function($m){
            $var = @$this->data[$m[1]];
            if (preg_match("/\./",$m[1])){
                $break_w = explode(".",$m[1]);
                $root = $this->data[$break_w[0]];
                for($i = 1;$i < count($break_w);$i++){
                    $root = $root[$break_w[$i]];
                }
                return $root;
            }elseif(!$var){
                throw new \Exception("Error: Undefined variable name \"{$m[0]}\"");
            }else{
              return $var;
            }
        },$this->file_interpreted);
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