<?php 
/**
	Admin Page Framework v3.8.28 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_Form_View___DebugInfo extends AdminPageFramework_FrameworkUtility {
    public $sStructureType = '';
    public $aCallbacks = array();
    public $oMsg;
    public function __construct() {
        $_aParameters = func_get_args() + array($this->sStructureType, $this->aCallbacks, $this->oMsg,);
        $this->sStructureType = $_aParameters[0];
        $this->aCallbacks = $_aParameters[1];
        $this->oMsg = $_aParameters[2];
    }
    public function get() {
        if (!$this->_shouldProceed()) {
            return '';
        }
        return "<div class='admin-page-framework-info'>" . $this->oMsg->get('debug_info') . ': ' . $this->getFrameworkNameVersion() . "</div>";
    }
    private function _shouldProceed() {
        if (!$this->callBack($this->aCallbacks['show_debug_info'], true)) {
            return false;
        }
        return in_array($this->sStructureType, array('widget', 'post_meta_box', 'page_meta_box', 'user_meta'));
    }
    }
    