<?php
/**
 Admin Page Framework v3.7.4b07 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
abstract class AdminPageFramework_UserMeta extends AdminPageFramework_UserMeta_Controller {
    static protected $_sStructureType = 'user_meta';
    public function __construct($sCapability = 'read', $sTextDomain = 'admin-page-framework') {
        $this->oProp = new AdminPageFramework_Property_UserMeta($this, get_class($this), $sCapability, $sTextDomain, self::$_sStructureType);
        parent::__construct($this->oProp);
        $this->oUtil->addAndDoAction($this, "start_{$this->oProp->sClassName}");
    }
}