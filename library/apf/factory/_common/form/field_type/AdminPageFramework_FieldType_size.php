<?php 
/**
	Admin Page Framework v3.7.12b04 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2016, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_FieldType_select extends AdminPageFramework_FieldType {
    public $aFieldTypeSlugs = array('select',);
    protected $aDefaultKeys = array('label' => array(), 'is_multiple' => false, 'attributes' => array('select' => array('size' => 1, 'autofocusNew' => null, 'multiple' => null, 'required' => null,), 'optgroup' => array(), 'option' => array(),),);
    protected function getStyles() {
        return ".admin-page-framework-field-select .admin-page-framework-input-label-container {vertical-align: top; }.admin-page-framework-field-select .admin-page-framework-input-label-container {padding-right: 1em;}";
    }
    protected function getField($aField) {
        $_oSelectInput = new AdminPageFramework_Input_select($aField['attributes']);
        if ($aField['is_multiple']) {
            $_oSelectInput->setAttribute(array('select', 'multiple'), 'multiple');
        }
        return $aField['before_label'] . "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: " . $this->sanitizeLength($aField['label_min_width']) . ";'>" . "<label for='{$aField['input_id']}'>" . $aField['before_input'] . $_oSelectInput->get($aField['label']) . $aField['after_input'] . "<div class='repeatable-field-buttons'></div>" . "</label>" . "</div>" . $aField['after_label'];
    }
}
class AdminPageFramework_FieldType_size extends AdminPageFramework_FieldType_select {
    public $aFieldTypeSlugs = array('size',);
    protected $aDefaultKeys = array('is_multiple' => false, 'units' => null, 'attributes' => array('size' => array('min' => null, 'max' => null, 'style' => 'width: 160px;',), 'unit' => array('multiple' => null, 'size' => 1, 'autofocusNew' => null, 'required' => null,), 'optgroup' => array(), 'option' => array(),),);
    protected $aDefaultUnits = array('px' => 'px', '%' => '%', 'em' => 'em', 'ex' => 'ex', 'in' => 'in', 'cm' => 'cm', 'mm' => 'mm', 'pt' => 'pt', 'pc' => 'pc',);
    protected function getStyles() {
        return ".admin-page-framework-field-size input {text-align: right;}.admin-page-framework-field-size select.size-field-select {vertical-align: 0px; }.admin-page-framework-field-size label {width: auto; } .form-table td fieldset .admin-page-framework-field-size label {display: inline;}";
    }
    protected function getField($aField) {
        $aField['units'] = $this->getElement($aField, 'units', $this->aDefaultUnits);
        $_aOutput = array();
        foreach (( array )$aField['label'] as $_isKey => $_sLabel) {
            $_aOutput[] = $this->_getFieldOutputByLabel($_isKey, $_sLabel, $aField);
        }
        return implode('', $_aOutput);
    }
    protected function _getFieldOutputByLabel($isKey, $sLabel, array $aField) {
        $_bMultiLabels = is_array($aField['label']);
        $_sLabel = $this->getElementByLabel($aField['label'], $isKey, $aField['label']);
        $aField['value'] = $this->getElementByLabel($aField['value'], $isKey, $aField['label']);
        $_aBaseAttributes = $_bMultiLabels ? array('name' => $aField['attributes']['name'] . "[{$isKey}]", 'id' => $aField['attributes']['id'] . "_{$isKey}", 'value' => $aField['value'],) + $aField['attributes'] : $aField['attributes'];
        unset($_aBaseAttributes['unit'], $_aBaseAttributes['size']);
        $_aOutput = array($this->getElementByLabel($aField['before_label'], $isKey, $aField['label']), "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: " . $this->sanitizeLength($aField['label_min_width']) . ";'>", $this->_getNumberInputPart($aField, $_aBaseAttributes, $isKey, is_array($aField['label'])), $this->_getUnitSelectInput($aField, $_aBaseAttributes, $isKey, is_array($aField['label'])), "</div>", $this->getElementByLabel($aField['after_label'], $isKey, $aField['label']));
        return implode('', $_aOutput);
    }
    private function _getNumberInputPart(array $aField, array $aBaseAttributes, $isKey, $bMultiLabels) {
        $_aSizeAttributes = $this->_getSizeAttributes($aField, $aBaseAttributes, $bMultiLabels ? $isKey : '');
        $_aSizeLabelAttributes = array('for' => $_aSizeAttributes['id'], 'class' => $_aSizeAttributes['disabled'] ? 'disabled' : null,);
        $_sLabel = $this->getElementByLabel($aField['label'], $isKey, $aField['label']);
        return "<label " . $this->getAttributes($_aSizeLabelAttributes) . ">" . $this->getElement($aField, $bMultiLabels ? array('before_label', $isKey, 'size') : array('before_label', 'size')) . ($aField['label'] && !$aField['repeatable'] ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength($aField['label_min_width']) . ";'>" . $_sLabel . "</span>" : "") . "<input " . $this->getAttributes($_aSizeAttributes) . " />" . $this->getElement($aField, $bMultiLabels ? array('after_input', $isKey, 'size') : array('after_input', 'size')) . "</label>";
    }
    private function _getUnitSelectInput(array $aField, array $aBaseAttributes, $isKey, $bMultiLabels) {
        $_aUnitAttributes = $this->_getUnitAttributes($aField, $aBaseAttributes, $bMultiLabels ? $isKey : '');
        $_oUnitInput = new AdminPageFramework_Input_select($_aUnitAttributes + array('select' => $_aUnitAttributes));
        $_aLabels = $bMultiLabels ? $this->getElement($aField, array('units', $isKey), $aField['units']) : $aField['units'];
        return "<label " . $this->getAttributes(array('for' => $_aUnitAttributes['id'], 'class' => $_aUnitAttributes['disabled'] ? 'disabled' : null,)) . ">" . $this->getElement($aField, $bMultiLabels ? array('before_label', $isKey, 'unit') : array('before_label', 'unit')) . $_oUnitInput->get($_aLabels) . $this->getElement($aField, $bMultiLabels ? array('after_input', $isKey, 'unit') : array('after_input', 'unit')) . "<div class='repeatable-field-buttons'></div>" . "</label>";
    }
    private function _getUnitAttributes(array $aField, array $aBaseAttributes, $isLabelKey = '') {
        $_bIsMultiple = $aField['is_multiple'] ? true : $this->getElement($aField, '' === $isLabelKey ? array('attributes', 'unit', 'multiple') : array('attributes', $isLabelKey, 'unit', 'multiple'), false);
        $_aSelectAttributes = array('type' => 'select', 'id' => $aField['input_id'] . ('' === $isLabelKey ? '' : '_' . $isLabelKey) . '_' . 'unit', 'multiple' => $_bIsMultiple ? 'multiple' : null, 'name' => $_bIsMultiple ? "{$aField['_input_name']}" . ('' === $isLabelKey ? '' : '[' . $isLabelKey . ']') . "[unit][]" : "{$aField['_input_name']}" . ('' === $isLabelKey ? '' : '[' . $isLabelKey . ']') . "[unit]", 'value' => $this->getElement($aField, array('value', 'unit'), ''),) + $this->getElement($aField, '' === $isLabelKey ? array('attributes', 'unit') : array('attributes', $isLabelKey, 'unit'), $this->aDefaultKeys['attributes']['unit']) + $aBaseAttributes;
        return $_aSelectAttributes;
    }
    private function _getSizeAttributes(array $aField, array $aBaseAttributes, $sLabelKey = '') {
        return array('type' => 'number', 'id' => $aField['input_id'] . '_' . ('' !== $sLabelKey ? $sLabelKey . '_' : '') . 'size', 'name' => $aField['_input_name'] . ('' !== $sLabelKey ? "[{$sLabelKey}]" : '') . '[size]', 'value' => $this->getElement($aField, array('value', 'size'), ''),) + $this->getElementAsArray($aField, '' === $sLabelKey ? array('attributes', 'size') : array('attributes', $sLabelKey, 'size'), $this->aDefaultKeys['attributes']['size']) + $aBaseAttributes;
    }
}
