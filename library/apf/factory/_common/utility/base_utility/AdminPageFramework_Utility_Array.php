<?php 
/**
	Admin Page Framework v3.9.0b01 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
abstract class AdminPageFramework_Utility_Array extends AdminPageFramework_Utility_String {
    static public function getUnusedNumericIndex($aArray, $nIndex, $iOffset = 1) {
        if (!isset($aArray[$nIndex])) {
            return $nIndex;
        }
        return self::getUnusedNumericIndex($aArray, $nIndex + $iOffset, $iOffset);
    }
    static public function isAssociative(array $aArray) {
        return array_keys($aArray) !== range(0, count($aArray) - 1);
    }
    static public function isLastElement(array $aArray, $sKey) {
        end($aArray);
        return $sKey === key($aArray);
    }
    static public function isFirstElement(array $aArray, $sKey) {
        reset($aArray);
        return $sKey === key($aArray);
    }
    static public function getReadableListOfArray(array $aArray) {
        $_aOutput = array();
        foreach ($aArray as $_sKey => $_vValue) {
            $_aOutput[] = self::getReadableArrayContents($_sKey, $_vValue, 32) . PHP_EOL;
        }
        return implode(PHP_EOL, $_aOutput);
    }
    static public function getReadableArrayContents($sKey, $vValue, $sLabelCharLengths = 16, $iOffset = 0) {
        $_aOutput = array();
        $_aOutput[] = ($iOffset ? str_pad(' ', $iOffset) : '') . ($sKey ? '[' . $sKey . ']' : '');
        if (!in_array(gettype($vValue), array('array', 'object'))) {
            $_aOutput[] = $vValue;
            return implode(PHP_EOL, $_aOutput);
        }
        foreach ($vValue as $_sTitle => $_asDescription) {
            if (!in_array(gettype($_asDescription), array('array', 'object'))) {
                $_aOutput[] = str_pad(' ', $iOffset) . $_sTitle . str_pad(':', $sLabelCharLengths - self::getStringLength($_sTitle)) . $_asDescription;
                continue;
            }
            $_aOutput[] = str_pad(' ', $iOffset) . $_sTitle . ": {" . self::getReadableArrayContents('', $_asDescription, 16, $iOffset + 4) . PHP_EOL . str_pad(' ', $iOffset) . "}";
        }
        return implode(PHP_EOL, $_aOutput);
    }
    static public function getReadableListOfArrayAsHTML(array $aArray) {
        $_aOutput = array();
        foreach ($aArray as $_sKey => $_vValue) {
            $_aOutput[] = "<ul class='array-contents'>" . self::getReadableArrayContentsHTML($_sKey, $_vValue) . "</ul>" . PHP_EOL;
        }
        return implode(PHP_EOL, $_aOutput);
    }
    static public function getReadableArrayContentsHTML($sKey, $vValue) {
        $_aOutput = array();
        $_aOutput[] = $sKey ? "<h3 class='array-key'>" . $sKey . "</h3>" : "";
        if (!in_array(gettype($vValue), array('array', 'object'))) {
            $_aOutput[] = "<div class='array-value'>" . html_entity_decode(nl2br(str_replace(' ', '&nbsp;', $vValue)), ENT_QUOTES) . "</div>";
            return "<li>" . implode(PHP_EOL, $_aOutput) . "</li>";
        }
        foreach ($vValue as $_sKey => $_vValue) {
            $_aOutput[] = "<ul class='array-contents'>" . self::getReadableArrayContentsHTML($_sKey, $_vValue) . "</ul>";
        }
        return implode(PHP_EOL, $_aOutput);
    }
    }
    