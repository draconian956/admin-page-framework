<?php 
/**
	Admin Page Framework v3.9.0b03 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_Form_View___Generate_FieldAddress extends AdminPageFramework_Form_View___Generate_FlatFieldName {
    public function get() {
        return $this->_getFlatFieldName();
    }
    public function getModel() {
        return $this->get() . '|' . $this->sIndexMark;
    }
    }
    