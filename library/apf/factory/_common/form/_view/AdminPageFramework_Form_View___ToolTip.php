<?php 
/**
	Admin Page Framework v3.9.0b03 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_Form_View___ToolTip extends AdminPageFramework_Form_View___Section_Base {
    public $aArguments = array('attributes' => array('container' => array(), 'title' => array(), 'content' => array(), 'description' => array(), 'icon' => array(),), 'icon' => null, 'dash-icon' => 'dashicons-editor-help', 'icon_alt_text' => '[?]', 'title' => null, 'content' => null,);
    public $sTitleElementID;
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aArguments, $this->sTitleElementID,);
        $this->aArguments = $this->_getArgumentsFormatted($_aParameters[0], $this->aArguments);
        $this->sTitleElementID = $_aParameters[1];
    }
    private function _getArgumentsFormatted($asArguments, $aDefaults) {
        $_aArguments = array();
        if ($this->_isContent($asArguments)) {
            $_aArguments['content'] = $asArguments;
            return $_aArguments + $aDefaults;
        }
        $_aArguments = $this->getAsArray($asArguments);
        $_aArguments['attributes'] = $this->uniteArrays($this->getElementAsArray($_aArguments, 'attributes'), $aDefaults['attributes']);
        return $_aArguments + $aDefaults;
    }
    private function _isContent($asContent) {
        if (is_string($asContent)) {
            return true;
        }
        if (is_array($asContent) && !$this->isAssociative($asContent)) {
            return true;
        }
        return false;
    }
    public function get() {
        if (!$this->aArguments['content']) {
            return '';
        }
        return "<a " . $this->_getElementAttributes('container', array('admin-page-framework-form-tooltip', 'no-js')) . ">" . $this->_getTipLinkIcon() . "<span " . $this->_getElementAttributes('content', 'admin-page-framework-form-tooltip-content') . ">" . $this->_getTipTitle() . $this->_getDescriptions() . "</span>" . "</a>";
    }
    private function _getTipLinkIcon() {
        if (isset($this->aArguments['icon'])) {
            return $this->aArguments['icon'];
        }
        if (version_compare($GLOBALS['wp_version'], '3.8', '>=')) {
            return "<span " . $this->_getElementAttributes('icon', array('dashicons', $this->aArguments['dash-icon'])) . "></span>";
        }
        return $this->aArguments['icon_alt_text'];
    }
    private function _getTipTitle() {
        if (isset($this->aArguments['title'])) {
            return "<span " . $this->_getElementAttributes('title', 'admin-page-framework-form-tooltip-title') . ">" . $this->aArguments['title'] . "</span>";
        }
        return '';
    }
    private function _getDescriptions() {
        if (isset($this->aArguments['content'])) {
            return "<span " . $this->_getElementAttributes('description', 'admin-page-framework-form-tooltip-description') . ">" . implode("</span><span " . $this->_getElementAttributes('description', 'admin-page-framework-form-tooltip-description') . ">", $this->getAsArray($this->aArguments['content'])) . "</span>";
        }
        return '';
    }
    private function _getElementAttributes($sElementKey, $asClassSelectors) {
        $_aContainerAttributes = $this->getElementAsArray($this->aArguments, array('attributes', $sElementKey)) + array('class' => '');
        $_aContainerAttributes['class'] = $this->getClassAttribute($_aContainerAttributes['class'], $asClassSelectors);
        return $this->getAttributes($_aContainerAttributes);
    }
    }
    