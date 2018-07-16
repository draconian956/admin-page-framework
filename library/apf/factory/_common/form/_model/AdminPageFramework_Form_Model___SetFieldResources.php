<?php 
/**
	Admin Page Framework v3.8.18 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2018, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_Form_Model___SetFieldResources extends AdminPageFramework_Form_Base {
    public $aArguments = array();
    public $aFieldsets = array();
    public $aResources = array('internal_styles' => array(), 'internal_styles_ie' => array(), 'internal_scripts' => array(), 'src_styles' => array(), 'src_scripts' => array(),);
    public $aFieldTypeDefinitions = array();
    public $aCallbacks = array('is_fieldset_registration_allowed' => null,);
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aArguments, $this->aFieldsets, $this->aResources, $this->aFieldTypeDefinitions, $this->aCallbacks,);
        $this->aArguments = $_aParameters[0];
        $this->aFieldsets = $_aParameters[1];
        $this->aResources = $_aParameters[2];
        $this->aFieldTypeDefinitions = $_aParameters[3];
        $this->aCallbacks = $_aParameters[4] + $this->aCallbacks;
    }
    public function get() {
        $this->___setCommon();
        $this->___set($this->aFieldsets);
        return $this->aResources;
    }
    private function ___setCommon() {
        if ($this->hasBeenCalled(__METHOD__)) {
            return;
        }
        new AdminPageFramework_Form_View___Script_RegisterCallback;
        $this->___setCommonFormInternalCSSRules();
    }
    private function ___setCommonFormInternalCSSRules() {
        $_aClassNames = array('AdminPageFramework_Form_View___CSS_Form', 'AdminPageFramework_Form_View___CSS_Field', 'AdminPageFramework_Form_View___CSS_Section', 'AdminPageFramework_Form_View___CSS_CollapsibleSection', 'AdminPageFramework_Form_View___CSS_FieldError', 'AdminPageFramework_Form_View___CSS_ToolTip',);
        foreach ($_aClassNames as $_sClassName) {
            $_oCSS = new $_sClassName;
            $this->aResources['internal_styles'][] = $_oCSS->get();
        }
        $_aClassNamesForIE = array('AdminPageFramework_Form_View___CSS_CollapsibleSectionIE',);
        foreach ($_aClassNames as $_sClassName) {
            $_oCSS = new $_sClassName;
            $this->aResources['internal_styles_ie'][] = $_oCSS->get();
        }
    }
    private function ___set($aAllFieldsets) {
        foreach ($aAllFieldsets as $_sSecitonID => $_aFieldsets) {
            $this->___setFieldResourcesBySection($_aFieldsets);
        }
    }
    private function ___setFieldResourcesBySection($_aFieldsets) {
        $_bIsSubSectionLoaded = false;
        foreach ($_aFieldsets as $_iSubSectionIndexOrFieldID => $_aSubSectionOrField) {
            if ($this->isNumericInteger($_iSubSectionIndexOrFieldID)) {
                if ($_bIsSubSectionLoaded) {
                    continue;
                }
                $_bIsSubSectionLoaded = true;
                foreach ($_aSubSectionOrField as $_aField) {
                    $this->___setFieldResources($_aField);
                }
                continue;
            }
            $_aField = $_aSubSectionOrField;
            $this->___setFieldResources($_aField);
        }
    }
    private function ___setFieldResources($aFieldset) {
        if (!$this->___isFieldsetAllowed($aFieldset)) {
            return;
        }
        $this->___setResourcesOfNestedFields($aFieldset);
        if ($this->hasNestedFields($aFieldset)) {
            $aFieldset['type'] = '_nested';
        }
        $_sFieldtype = $this->getElement($aFieldset, 'type');
        $_aFieldTypeDefinition = $this->getElementAsArray($this->aFieldTypeDefinitions, $_sFieldtype);
        $this->___setFieldResourcesByFieldTypeDefinition($aFieldset, $_sFieldtype, $_aFieldTypeDefinition);
    }
    private function ___isFieldsetAllowed($aFieldset) {
        return $this->callBack($this->aCallbacks['is_fieldset_registration_allowed'], array(true, $aFieldset,));
    }
    private function ___setResourcesOfNestedFields($aFieldset) {
        if (!$this->hasFieldDefinitionsInContent($aFieldset)) {
            return;
        }
        foreach ($aFieldset['content'] as $_asNestedFieldset) {
            if (is_scalar($_asNestedFieldset)) {
                continue;
            }
            $this->___setFieldResources($_asNestedFieldset);
        }
    }
    private function ___setFieldResourcesByFieldTypeDefinition($aFieldset, $_sFieldtype, $_aFieldTypeDefinition) {
        if (empty($_aFieldTypeDefinition)) {
            return;
        }
        $this->callback($_aFieldTypeDefinition['hfDoOnRegistration'], array($aFieldset));
        $this->callBack($this->aCallbacks['load_fieldset_resource'], array($aFieldset,));
        if ($this->hasBeenCalled('registered_' . $_sFieldtype . '_' . $this->aArguments['structure_type'])) {
            return;
        }
        new AdminPageFramework_Form_Model___FieldTypeRegistration($_aFieldTypeDefinition, $this->aArguments['structure_type']);
        $_oFieldTypeResources = new AdminPageFramework_Form_Model___FieldTypeResource($_aFieldTypeDefinition, $this->aResources);
        $this->aResources = $_oFieldTypeResources->get();
    }
}
