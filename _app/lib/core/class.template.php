<?php
class Template{
    var $template;
    var $variables;

    function __construct($template){
        $this->template = @file_get_contents($template);
        $this->variables = array();
    }

    public function replace($array){
        if ( !is_array($array) ){
            return;
        }
        foreach ( $array as $name => $data ){
            $this->add($name, $data);
        }
        return;
    }

    public function render($direct_output = false){
        $template = addslashes($this->template);
        foreach ( $this->variables as $variable => $data ){
            $$variable = $data;
        }
        eval("\$template = \"$template\";");
        if ( $direct_output ) {
            echo stripslashes($template);
        } else {
            return stripslashes($template);
        }
    }

	private function add($var_name, $var_data){
        $this->variables[$var_name] = $var_data;
    }
}
?>
