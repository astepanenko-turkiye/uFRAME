<?php

namespace Application\Components\common;

class View {

    const MAIN=10;
    const ACTION=5;
    const DISABLED=0;

    protected $renderLevel=self::MAIN;

    public function setRenderLevel($level) {

        $this->renderLevel=$level;

    }

    public function getRenderLevel() {

        return $this->renderLevel;

    }

    protected $_vars=[];

    public function setVar($key, $value) {

        $this->_vars[$key]=$value;

    }

    public function getVar($key) {

        if(array_key_exists($key,$this->_vars)) {

            return $this->_vars[$key];

        } else {

            return null;
        }

    }

    public function getVars() {

        return $this->_vars;

    }

    protected $_head_component=BASE_DIR."app".DS."views".DS."_components".DS."head.php";
    protected $_header_component=BASE_DIR."app".DS."views".DS."_components".DS."header.php";
    protected $_main_component=BASE_DIR."app".DS."views".DS."_components".DS."main.php";
    protected $_footer_component=BASE_DIR."app".DS."views".DS."_components".DS."footer.php";

    protected $_head_html;
    protected $_header_html;
    protected $_main_html;
    protected $_footer_html;

    public function setHead($path) {
        $this->_head_component=$path;
    }
    public function setHeader($path) {
        $this->_header_component=$path;
    }
    public function setMain($path) {
        $this->_main_component=$path;
    }
    public function setFooter($path) {
        $this->_footer_component=$path;
    }

    public function getHead() {
        return $this->_head_html;
    }
    public function getHeader() {
        return $this->_header_html;
    }
    public function getMain() {
        return $this->_main_html;
    }
    public function getFooter() {
        return $this->_footer_html;
    }

    protected $template_path;

    public function setTemplate($path) {
        $this->template_path=$path;
    }

    protected function minifyOutput(&$buffer) {

        $search=[
            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' // Remove HTML comments
        ];

        $replace=[
            '>',
            '<',
            '\\1',
            ''
        ];

        $buffer=preg_replace($search,$replace,$buffer);

        $buffer=str_replace('> <','><',$buffer);

    }

    public function getContent() {

        if(!isset($this->template_path)) $this->template_path=VIEW_ACTION_FOLDER.DS.VIEW_ACTION_FILE;

        $BASEz3xVDk=BASE_DIR."app".DS."views".DS."index.php";

        $ACTIO2ap9=BASE_DIR."app".DS."views".DS.$this->template_path;

        $VARS4DBpu=$this->getVars();

        if($VARS4DBpu) { extract($VARS4DBpu); unset($VARS4DBpu); }

        if(file_exists($ACTIO2ap9)) $this->setMain($ACTIO2ap9);

        switch($this->renderLevel) {

            case self::MAIN:

                ob_start(); require_once $this->_head_component; $this->_head_html=ob_get_clean();
                ob_start(); require_once $this->_header_component; $this->_header_html=ob_get_clean();
                ob_start(); require_once $this->_main_component; $this->_main_html=ob_get_clean();
                ob_start(); require_once $this->_footer_component; $this->_footer_html=ob_get_clean();

                ob_start(); require_once $BASEz3xVDk; $html=ob_get_clean();

                break;
            case self::ACTION:

                $this->_head_html="";
                $this->_header_html="";
                ob_start(); require_once $this->_main_component; $this->_main_html=ob_get_clean();
                $this->_footer_html="";

                $html=$this->_main_html;

                break;
            case self::DISABLED:

                $this->_head_html="";
                $this->_header_html="";
                $this->_main_html="";
                $this->_footer_html="";

                break;

        }

        $this->minifyOutput($html);

        return $html;

    }

    public function partial($filepath,$VARS4DBpu=[]) {

        if(!empty($VARS4DBpu)) { extract($VARS4DBpu); unset($VARS4DBpu); }

        $filepath=explode("/",$filepath);

        $filepath=implode(DS,$filepath);

        $filepath=BASE_DIR."app".DS."views".DS.$filepath;

        ob_start(); require_once $filepath; $html=ob_get_clean();

        return $html;

    }

}