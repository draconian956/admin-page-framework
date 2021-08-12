<?php 
/**
	Admin Page Framework v3.8.32b01 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_Form_Model___FieldTypeResource extends AdminPageFramework_FrameworkUtility {
    public $aFieldTypeDefinition = array();
    public $aResources = array('internal_styles' => array(), 'internal_styles_ie' => array(), 'internal_scripts' => array(), 'src_styles' => array(), 'src_scripts' => array(),);
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aFieldTypeDefinition, $this->aResources,);
        $this->aFieldTypeDefinition = $this->getAsArray($_aParameters[0]);
        $this->aResources = $this->getAsArray($_aParameters[1]);
    }
    public function get() {
        $this->aResources['internal_scripts'] = $this->_getUpdatedInternalItemsByCallback($this->aResources['internal_scripts'], 'hfGetScripts');
        $this->aResources['internal_styles'] = $this->_getUpdatedInternalItemsByCallback($this->aResources['internal_styles'], 'hfGetStyles');
        $this->aResources['internal_styles_ie'] = $this->_getUpdatedInternalItemsByCallback($this->aResources['internal_styles_ie'], 'hfGetIEStyles');
        $this->aResources['src_styles'] = $this->_getUpdatedEnqueuingItemsByCallback($this->aResources['src_styles'], 'aEnqueueStyles');
        $this->aResources['src_scripts'] = $this->_getUpdatedEnqueuingItemsByCallback($this->aResources['src_scripts'], 'aEnqueueScripts');
        return $this->aResources;
    }
    private function _getUpdatedInternalItemsByCallback(array $aSubject, $sKey) {
        $_oCallable = $this->getElement($this->aFieldTypeDefinition, $sKey);
        if (!is_callable($_oCallable)) {
            return $aSubject;
        }
        $aSubject[] = call_user_func_array($_oCallable, array());
        return $aSubject;
    }
    private function _getUpdatedEnqueuingItemsByCallback($aSubject, $sKey) {
        return array_merge($aSubject, $this->getElementAsArray($this->aFieldTypeDefinition, $sKey));
    }
    }
    