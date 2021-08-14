<?php 
/**
	Admin Page Framework v3.9.0b03 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_FrameworkUtility extends AdminPageFramework_WPUtility {
    static public function showDeprecationNotice($sDeprecated, $sAlternative = '', $sProgramName = '') {
        $sProgramName = $sProgramName ? $sProgramName : self::getFrameworkName();
        parent::showDeprecationNotice($sDeprecated, $sAlternative, $sProgramName);
    }
    static public function sortAdminSubMenu() {
        if (self::hasBeenCalled(__METHOD__)) {
            return;
        }
        foreach (( array )$GLOBALS['_apf_sub_menus_to_sort'] as $_sIndex => $_sMenuSlug) {
            if (!isset($GLOBALS['submenu'][$_sMenuSlug])) {
                continue;
            }
            ksort($GLOBALS['submenu'][$_sMenuSlug]);
            unset($GLOBALS['_apf_sub_menus_to_sort'][$_sIndex]);
        }
    }
    static public function getFrameworkVersion($bTrimDevVer = false) {
        $_sVersion = AdminPageFramework_Registry::getVersion();
        return $bTrimDevVer ? self::getSuffixRemoved($_sVersion, '.dev') : $_sVersion;
    }
    static public function getFrameworkName() {
        return AdminPageFramework_Registry::NAME;
    }
    static public function getFrameworkNameVersion() {
        return self::getFrameworkName() . ' ' . self::getFrameworkVersion();
    }
    }
    