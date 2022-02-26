<?php
/*
 * Admin Page Framework v3.9.0b17 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_FieldType_checkbox extends AdminPageFramework_FieldType {
    public $aFieldTypeSlugs = array( 'checkbox' );
    protected $aDefaultKeys = array( 'select_all_button' => false, 'select_none_button' => false, 'save_unchecked' => true, );
    protected function getEnqueuingScripts()
    {
        return array( array( 'handle_id' => 'admin-page-framework-field-type-checkbox', 'src' => dirname(__FILE__) . '/js/checkbox.bundle.js', 'in_footer' => true, 'dependencies' => array( 'jquery', 'admin-page-framework-script-form-main' ), 'translation_var' => 'AdminPageFrameworkFieldTypeCheckbox', 'translation' => array( 'fieldTypeSlugs' => $this->aFieldTypeSlugs, 'selectors' => array( 'selectAll' => $this->___getSelectButtonClassSelectors($this->aFieldTypeSlugs, 'select_all_button'), 'selectNone' => $this->___getSelectButtonClassSelectors($this->aFieldTypeSlugs, 'select_none_button'), ), 'messages' => array(), ), ), );
    }
    private function ___getSelectButtonClassSelectors(array $aFieldTypeSlugs, $sDataAttribute='select_all_button')
    {
        $_aClassSelectors = array();
        foreach ($aFieldTypeSlugs as $_sSlug) {
            if (! is_scalar($_sSlug)) {
                continue;
            }
            $_aClassSelectors[] = '.admin-page-framework-checkbox-container-' . $_sSlug . "[data-{$sDataAttribute}]";
        }
        return implode(',', $_aClassSelectors);
    }
    protected $_sCheckboxClassSelector = 'apf_checkbox';
    protected function getField($aField)
    {
        $_aOutput = array();
        $_bIsMultiple = is_array($aField[ 'label' ]);
        foreach ($this->getAsArray($aField[ 'label' ], true) as $_sKey => $_sLabel) {
            $_aOutput[] = $this->_getEachCheckboxOutput($aField, $_bIsMultiple ? $_sKey : '', $_sLabel);
        }
        return "<div " . $this->getAttributes($this->_getCheckboxContainerAttributes($aField)) . ">" . "<div class='repeatable-field-buttons'></div>" . implode(PHP_EOL, $_aOutput) . "</div>";
    }
    protected function _getCheckboxContainerAttributes(array $aField)
    {
        return array( 'class' => 'admin-page-framework-checkbox-container-' . $aField[ 'type' ], 'data-select_all_button' => $aField[ 'select_all_button' ] ? (! is_string($aField[ 'select_all_button' ]) ? $this->oMsg->get('select_all') : $aField[ 'select_all_button' ]) : null, 'data-select_none_button' => $aField[ 'select_none_button' ] ? (! is_string($aField[ 'select_none_button' ]) ? $this->oMsg->get('select_none') : $aField[ 'select_none_button' ]) : null, );
    }
    private function _getEachCheckboxOutput(array $aField, $sKey, $sLabel)
    {
        $_aInputAttributes = array( 'data-key' => $sKey, ) + $aField[ 'attributes' ];
        $_oCheckbox = new AdminPageFramework_Input_checkbox($_aInputAttributes, array( 'save_unchecked' => $this->getElement($aField, 'save_unchecked'), ));
        $_oCheckbox->setAttributesByKey($sKey);
        $_oCheckbox->addClass($this->_sCheckboxClassSelector);
        return $this->getElementByLabel($aField[ 'before_label' ], $sKey, $aField[ 'label' ]) . "<div " . $this->getLabelContainerAttributes($aField, 'admin-page-framework-input-label-container admin-page-framework-checkbox-label') . ">" . "<label " . $this->getAttributes(array( 'for' => $_oCheckbox->getAttribute('id'), 'class' => $_oCheckbox->getAttribute('disabled') ? 'disabled' : null, )) . ">" . $this->getElementByLabel($aField[ 'before_input' ], $sKey, $aField[ 'label' ]) . $_oCheckbox->get($sLabel) . $this->getElementByLabel($aField[ 'after_input' ], $sKey, $aField[ 'label' ]) . "</label>" . "</div>" . $this->getElementByLabel($aField[ 'after_label' ], $sKey, $aField[ 'label' ]) ;
    }
}
