<?php

class Parser {

    /**
     * @var array All the variables to parse
     */
    private $variables = array();

    /**
     * Set the templatefile (usually in /scripts/assets/ )
     *
     * @param $templateName
     */
    public function setTemplate($templateName) {
        $this->setContent(file_get_contents(TEMPLATE_ASSET_PATH . '/' . $templateName . '.tpl'));
    }

    /**
     * Set the content of the parser
     * @param $content
     */
    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * Set a key / value to parse.
     * WARNING! Key: "myKey" becomes: {MYKEY} in the template.
     *
     * If $key is an array it will add the whole array.
     *
     * @param $key string|array
     * @param $value string
     */
    public function setVar($key,$value = '') {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->variables[$k] = $v;
            }
        } else {
            $this->variables[$key] = $value;
        }
    }

    /**
     * Parse all our variables.
     *
     * @return string The parsed content.
     */
    public function parse() {

        // This is very inefficient. I was too lazy to build a nice regex parser.
        // Perhaps its a good idea to make the templates phtml files. Then we inject some variables
        // and then you can easily parse PHP, grab the output and return it here!
        foreach ($this->variables as $key => $value) {
            $key = '{' . strtoupper($key) . '}';
            $this->content = str_replace($key, $value, $this->content);
        }

        return $this->content;
    }

}