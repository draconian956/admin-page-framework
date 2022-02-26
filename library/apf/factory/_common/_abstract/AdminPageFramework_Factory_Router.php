<?php
/*
 * Admin Page Framework v3.9.0b17 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Factory_Router {
    public $oProp;
    public $oDebug;
    public $oUtil;
    public $oMsg;
    public $oForm;
    protected $oPageLoadInfo;
    protected $oResource;
    protected $oHeadTag;
    protected $oHelpPane;
    protected $oLink;
    protected $_aSubClassPrefixes = array( 'oForm' => 'AdminPageFramework_Form_', 'oPageLoadInfo' => 'AdminPageFramework_PageLoadInfo_', 'oResource' => 'AdminPageFramework_Resource_', 'oHelpPane' => 'AdminPageFramework_HelpPane_', 'oLink' => 'AdminPageFramework_Link_', );
    private $_aSubClassNames = array( 'oProp' => null, 'oDebug' => 'AdminPageFramework_Debug', 'oUtil' => 'AdminPageFramework_FrameworkUtility', 'oMsg' => 'AdminPageFramework_Message', 'oForm' => null, 'oPageLoadInfo' => null, 'oResource' => null, 'oHelpPane' => null, 'oLink' => null, );
    public $aSubClassNames = array();
    public function __construct($oProp)
    {
        $this->aSubClassNames = $this->___getSubClassNames();
        unset($this->oDebug, $this->oUtil, $this->oMsg, $this->oForm, $this->oPageLoadInfo, $this->oResource, $this->oHelpPane, $this->oLink);
        $this->oProp = $oProp;
        if ($this->oProp->bIsAdmin) {
            $this->oUtil->registerAction('current_screen', array( $this, '_replyToLoadComponents' ));
        }
        $this->start();
        $this->oUtil->addAndDoAction($this, 'start_' . $this->oProp->sClassName, $this);
    }
    private function ___getSubClassNames()
    {
        foreach ($this->_aSubClassPrefixes as $_sObjectVariableName => $_sPrefix) {
            $this->aSubClassNames[ $_sObjectVariableName ] = $_sPrefix . $this->_sStructureType;
        }
        return $this->aSubClassNames + $this->_aSubClassNames;
    }
    public function _replyToLoadComponents()
    {
        if (! $this->_isInThePage()) {
            return;
        }
        if (! isset($this->oResource)) {
            $this->oResource = $this->_replyTpSetAndGetInstance_oResource();
        }
        if (! isset($this->oLink)) {
            $this->oLink = $this->_replyTpSetAndGetInstance_oLink();
        }
        if ($this->oUtil->isDebugMode()) {
            $this->oPageLoadInfo = $this->oPageLoadInfo;
        }
    }
    protected function _load($aActions=array())
    {
        $aActions = empty($aActions) ? array( 'load_' . $this->oProp->sClassName, ) : $aActions;
        $this->load();
        $this->oUtil->addAndDoActions($this, $aActions, $this);
    }
    protected function _setUp()
    {
        $aActions = array( 'set_up_' . $this->oProp->sClassName, );
        $this->setUp();
        $this->oUtil->addAndDoActions($this, $aActions, $this);
    }
    protected function _isInstantiatable()
    {
        if ($this->_isWordPressCoreAjaxRequest()) {
            return false;
        }
        return true;
    }
    protected function _isWordPressCoreAjaxRequest()
    {
        if (! isset($GLOBALS[ 'pagenow' ])) {
            return false;
        }
        if ('admin-ajax.php' !== $GLOBALS[ 'pagenow' ]) {
            return false;
        }
        return in_array(isset($_POST[ 'action' ]) ? $_POST[ 'action' ] : '', array( 'heartbeat', 'closed-postboxes', 'meta-box-order' ), true);
    }
    protected function _isInThePage()
    {
        return true;
    }
    protected function _isValidAjaxReferrer()
    {
        if (! $this->oProp->bIsAdminAjax) {
            return false;
        }
        return true;
    }
    public function _replyToDetermineToLoad()
    {
        if (! $this->_isInThePage()) {
            return;
        }
        $this->_setUp();
    }
    protected function _getFormObject()
    {
        $this->oProp->setFormProperties();
        $_sFormClass = $this->aSubClassNames[ 'oForm' ];
        return new $_sFormClass($this->oProp->aFormArguments, $this->oProp->aFormCallbacks, $this->oMsg);
    }
    protected function _getLinkObject()
    {
        return null;
    }
    protected function _getPageLoadObject()
    {
        return null;
    }
    public function __get($sPropertyName)
    {
        if (isset($this->aSubClassNames[ $sPropertyName ])) {
            return call_user_func(array( $this, "_replyTpSetAndGetInstance_{$sPropertyName}" ));
        }
    }
    public function _replyTpSetAndGetInstance_oUtil()
    {
        $_sClassName = $this->aSubClassNames[ 'oUtil' ];
        $this->oUtil = new $_sClassName;
        return $this->oUtil;
    }
    public function _replyTpSetAndGetInstance_oDebug()
    {
        $_sClassName = $this->aSubClassNames[ 'oDebug' ];
        $this->oDebug = new $_sClassName;
        return $this->oDebug;
    }
    public function _replyTpSetAndGetInstance_oMsg()
    {
        $this->oMsg = call_user_func_array(array( $this->aSubClassNames[ 'oMsg' ], 'getInstance'), array( $this->oProp->sTextDomain ));
        return $this->oMsg;
    }
    public function _replyTpSetAndGetInstance_oForm()
    {
        $this->oForm = $this->_getFormObject();
        return $this->oForm;
    }
    public function _replyTpSetAndGetInstance_oResource()
    {
        if (isset($this->oResource)) {
            return $this->oResource;
        }
        $_sClassName = $this->aSubClassNames[ 'oResource' ];
        $this->oResource = new $_sClassName($this->oProp);
        return $this->oResource;
    }
    public function _replyTpSetAndGetInstance_oHeadTag()
    {
        $this->oHead = $this->_replyTpSetAndGetInstance_oResource();
        return $this->oHead;
    }
    public function _replyTpSetAndGetInstance_oHelpPane()
    {
        $_sClassName = $this->aSubClassNames[ 'oHelpPane' ];
        $this->oHelpPane = new $_sClassName($this->oProp);
        return $this->oHelpPane;
    }
    public function _replyTpSetAndGetInstance_oLink()
    {
        $this->oLink = $this->_getLinkObject();
        return $this->oLink;
    }
    public function _replyTpSetAndGetInstance_oPageLoadInfo()
    {
        $this->oPageLoadInfo = $this->_getPageLoadObject();
        return $this->oPageLoadInfo;
    }
    public function __call($sMethodName, $aArguments=null)
    {
        $_mFirstArg = $this->oUtil->getElement($aArguments, 0);
        switch ($sMethodName) { case 'validate': case 'content': return $_mFirstArg; }
        if (has_filter($sMethodName)) {
            return $this->_getAutoCallback($sMethodName, $aArguments);
        }
        $this->_triggerUndefinedMethodWarning($sMethodName);
    }
    private function _getAutoCallback($sMethodName, $aArguments)
    {
        if (false === strpos($sMethodName, "\\")) {
            return $this->oUtil->getElement($aArguments, 0);
        }
        $_sAutoCallbackMethodName = str_replace('\\', '_', $sMethodName);
        return method_exists($this, $_sAutoCallbackMethodName) ? call_user_func_array(array( $this, $_sAutoCallbackMethodName ), $aArguments) : $this->oUtil->getElement($aArguments, 0);
    }
    private function _triggerUndefinedMethodWarning($sMethodName)
    {
        trigger_error(AdminPageFramework_Registry::NAME . ': ' . sprintf(__('The method is not defined: %1$s', $this->oProp->sTextDomain), $sMethodName), E_USER_WARNING);
    }
    public function __toString()
    {
        return AdminPageFramework_FrameworkUtility::getObjectInfo($this);
    }
    public function setFooterInfoRight()
    {}
    public function setFooterInfoLeft()
    {}
}
