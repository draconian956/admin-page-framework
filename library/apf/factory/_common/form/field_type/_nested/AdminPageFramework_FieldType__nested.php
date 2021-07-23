<?php 
/**
	Admin Page Framework v3.9.0b01 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_FieldType__nested extends AdminPageFramework_FieldType {
    public $aFieldTypeSlugs = array('_nested');
    protected $aDefaultKeys = array();
    protected function getStyles() {
        return ".admin-page-framework-fieldset > .admin-page-framework-fields > .admin-page-framework-field.with-nested-fields > .admin-page-framework-fieldset.multiple-nesting {margin-left: 2em;}.admin-page-framework-fieldset > .admin-page-framework-fields > .admin-page-framework-field.with-nested-fields > .admin-page-framework-fieldset {margin-bottom: 1em;}.with-nested-fields > .admin-page-framework-fieldset.child-fieldset > .admin-page-framework-child-field-title {display: inline-block;padding: 0 0 0.4em 0;}.admin-page-framework-fieldset.child-fieldset > label.admin-page-framework-child-field-title {display: table-row; white-space: nowrap;}";
    }
    protected function getField($aField) {
        $_oCallerForm = $aField['_caller_object'];
        $_aInlineMixedOutput = array();
        foreach ($this->getAsArray($aField['content']) as $_aChildFieldset) {
            if (is_scalar($_aChildFieldset)) {
                continue;
            }
            if (!$this->isNormalPlacement($_aChildFieldset)) {
                continue;
            }
            $_aChildFieldset = $this->getFieldsetReformattedBySubFieldIndex($_aChildFieldset, ( integer )$aField['_index'], $aField['_is_multiple_fields'], $aField);
            $_oFieldset = new AdminPageFramework_Form_View___Fieldset($_aChildFieldset, $_oCallerForm->aSavedData, $_oCallerForm->getFieldErrors(), $_oCallerForm->aFieldTypeDefinitions, $_oCallerForm->oMsg, $_oCallerForm->aCallbacks);
            $_aInlineMixedOutput[] = $_oFieldset->get();
        }
        return implode('', $_aInlineMixedOutput);
    }
    }
    