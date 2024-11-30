<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to enqueue or insert resource elements.
 *
 * The class handles `<link>`, `<style>` and `<script>` tags to be inserted conditionally into the head tag or the footer of the page.
 *
 * @internal
 * @since    2.1.5
 * @since    3.3.0 Changed the name from `AdminPageFramework_HeadTag_Base`.
 * @since    3.6.3 Changed it to extend `AdminPageFramework_WPUtility`.
 * @package  AdminPageFramework/Common/Factory/Resource
 */
abstract class AdminPageFramework_Resource_Base extends AdminPageFramework_FrameworkUtility
{

	/**
	 * Represents the structure of the array for enqueuing scripts and styles.
	 *
	 * @since       2.1.2
	 * @since       2.1.5       Moved to the base class.
	 * @since       3.0.0       Moved from the property class.
	 * @since       3.3.0       Changed the name to `$_aStructure_EnqueuingResources` from `$_aStructure_EnqueuingScriptsAndStyles`.
	 * @internal
	 */
	protected static $_aStructure_EnqueuingResources = array(

		/* The system internal keys. */
		'sSRC' => null,
		'sSRCRaw' => null,
		'aPostTypes' => array(),     // for meta box class
		'sPageSlug' => null,
		'sTabSlug' => null,
		'sType' => null,        // script or style

		/* The below keys are for users. */
		'handle_id' => null,
		'dependencies' => array(),
		'version' => false,       // although the type should be string, the wp_enqueue_...() functions want false as the default value.
		'attributes' => array(),     // [3.3.0+] - the attribute array @deprecated 3.9.0
		'conditional' => null,        // [3.9.0+] Comments for IE 6, lte IE 7 etc. @see wp_style_add_data() and wp_script_add_data()

		// script specific
		'translation' => array(),     // only for scripts
		'translation_var' => '',          // [3.9.0+] the object name of the passed translation data to JavaScript script
		'in_footer' => false,       // only for scripts

		// style specific
		'media' => 'all',       // only for styles
		/// [3.9.0+] @see wp_style_add_data()
		'rtl' => null,        // bool|string To declare an RTL stylesheet.
		'suffix' => null,        // string      Optional suffix, used in combination with RTL.
		'alt' => null,        // bool        For rel="alternate stylesheet".
		'title' => null,        // string      For preferred/alternate stylesheets.
	);

	/**
	 * Stores the class selector used for the class-specific style.
	 *
	 * @since       3.2.0
	 * @remark      This value should be overridden in an extended class.
	 * @internal
	 */
	protected $_sClassSelector_Style = 'admin-page-framework-style';

	/**
	 * Stores the class selector used to the class-specific script.
	 *
	 * @since       3.2.0
	 * @remark      This value should be overridden in an extended class.
	 * @internal
	 */
	protected $_sClassSelector_Script = 'admin-page-framework-script';

	/**
	 * Stores hand IDs by resource url to look up handle id and add custom arguments.
	 * @since       3.3.0
	 * @internal
	 */
	protected $_aHandleIDs = array();

	/**
	 * A property object.
	 *
	 * @var         AdminPageFramework_Property_Base
	 * @remark      Set in the constructor.
	 */
	public $oProp;

	/**
	 * A utility object.
	 *
	 * @remark      Set in the constructor.
	 * @deprecated  3.6.3
	 * @remark      kept for backward compatibility.
	 * @var         AdminPageFramework_WPUtility
	 */
	public $oUtil;

	/**
	 * Sets up properties and hooks.
	 * @internal
	 */
	public function __construct($oProp)
	{

		$this->oProp = $oProp;

		// for backward compatibility
		$this->oUtil = new AdminPageFramework_WPUtility;

		if ($this->isDoingAjax())
		{
			return;
		}

		$this->registerAction('current_screen', array($this, '_replyToSetUpHooks'));

	}

	/**
	 * @since 3.9.0
	 */
	public function _replyToSetUpHooks()
	{

		if (!$this->oProp->oCaller->isInThePage())
		{
			return;
		}

		// Hook the admin header to insert custom admin stylesheets and scripts.
		// add_action( 'admin_enqueue_scripts', array( $this, '_replyToEnqueueCommonScripts' ), 1 );    // @deprecated 3.9.0
		add_action('admin_enqueue_scripts', array($this, '_replyToEnqueueCommonStyles'), 1);

		add_action('admin_enqueue_scripts', array($this, '_replyToEnqueueScripts'));
		add_action('admin_enqueue_scripts', array($this, '_replyToEnqueueStyles'));

		/// A low priority is required to let dependencies loaded fast especially in customizer.php.
		add_action(did_action('admin_print_styles') ? 'admin_print_footer_scripts' : 'admin_print_styles', array($this, '_replyToAddStyle'), 999);
		add_action(did_action('admin_print_scripts') ? 'admin_print_footer_scripts' : 'admin_print_scripts', array($this, '_replyToAddScript'), 999);

		// Take care of items that could not be added in the head tag.

		/// For wp-admin/customizer.php
		add_action('customize_controls_print_footer_scripts', array($this, '_replyToEnqueueScripts'));
		add_action('customize_controls_print_footer_scripts', array($this, '_replyToEnqueueStyles'));

		/// For admin pages other than wp-admin/customizer.php
		add_action('admin_footer', array($this, '_replyToEnqueueScripts'));
		add_action('admin_footer', array($this, '_replyToEnqueueStyles'));

		/// For all admin pages.
		add_action('admin_print_footer_scripts', array($this, '_replyToAddStyle'), 999);
		add_action('admin_print_footer_scripts', array($this, '_replyToAddScript'), 999);


		// To add the custom attributes to the enqueued style and script tags.
		add_filter('script_loader_src', array($this, '_replyToSetupArgumentCallback'), 1, 2);
		add_filter('style_loader_src', array($this, '_replyToSetupArgumentCallback'), 1, 2);

	}

	/*
	 * Methods that should be overridden in extended classes.
	 * @internal
	 */

	// @deprecated 3.8.31 Unused
	// public function _forceToEnqueueStyle( $sSRC, $aCustomArgs=array() ) {}
	// public function _forceToEnqueueScript( $sSRC, $aCustomArgs=array() ) {}

	/**
	 * A helper function for the _replyToEnqueueScripts() and the `_replyToEnqueueStyle()` methods.
	 *
	 * @since    2.1.5
	 * @since    3.7.0      Fixed a typo in the method name.
	 * @internal
	 * @remark   The widget fields type does not have conditions unlike the meta-box type that requires to check currently loaded post type.
	 * @remark   This method should be redefined in the extended class.
	 */
	protected function _enqueueSRCByCondition($aEnqueueItem)
	{
		$this->_enqueueSRC($aEnqueueItem);
	}

	/*
	 * Shared methods
	 */
	/**
	 * Checks the src url of the enqueued script/style to determine whether or not to set up a attribute modification callback.
	 *
	 * If it is one of the framework added item, the method sets up a hook to modify the url to add custom attributes.
	 *
	 * @since    3.3.0
	 * @internal
	 * @callback add_action() script_loader_src
	 * @callback add_action() style_loader_src
	 */
	public function _replyToSetupArgumentCallback($sSRC, $sHandleID)
	{
		if (isset($this->oProp->aResourceAttributes[$sHandleID]))
		{
			$this->_aHandleIDs[$sSRC] = $sHandleID;
			add_filter('clean_url', array($this, '_replyToModifyEnqueuedAttributes'), 1, 3);
			remove_filter(current_filter(), array($this, '_replyToSetupArgumentCallback'), 1);
		}
		return $sSRC;
	}
	/**
	 * Modifies the attributes of the enqueued script tag.
	 *
	 * @since    3.3.0
	 * @internal
	 */
	public function _replyToModifyEnqueuedAttributes($sSanitizedURL, $sOriginalURL, $sContext)
	{

		if ('display' !== $sContext)
		{
			return $sSanitizedURL;
		}

		// Returns the modified url which attributes are embedded at the end.
		if (isset($this->_aHandleIDs[$sOriginalURL]))
		{

			$_sHandleID = $this->_aHandleIDs[$sOriginalURL];
			$_aAttributes = $this->oProp->aResourceAttributes[$_sHandleID];

			if (empty($_aAttributes))
			{
				return $sSanitizedURL;
			}

			$_sAttributes = $this->getAttributes($_aAttributes);
			return $sSanitizedURL . "' " . rtrim($_sAttributes, "'\"");

		}

		return $sSanitizedURL;

	}

	/**
	 * Prints the inline stylesheet of the meta-box common CSS rules with the style tag.
	 *
	 * @internal
	 * @since    3.0.0
	 * @since    3.2.0       Moved to the base class from the meta box class.
	 * @remark   The meta box class may be instantiated multiple times so prevent echoing the same styles multiple times.
	 * @param    string      $sIDPrefix   The id selector embedded in the script tag.
	 * @param    string      $sClassName  The class name that identify the call group. This is important for the meta-box class because it can be instantiated multiple times in one particular page.
	 */
	protected function _printCommonStyles($sIDPrefix, $sClassName)
	{

		if ($this->hasBeenCalled('COMMON_STYLES: ' . get_class($this) . '::' . __METHOD__))
		{
			return;
		}
		$_oCaller = $this->oProp->oCaller;
		echo $this->___getCommonStyleTag($_oCaller, $sIDPrefix);
		echo $this->___getCommonIEStyleTag($_oCaller, $sIDPrefix);

	}
	/**
	 * @internal
	 * @since    3.5.7
	 * @since    3.8.22  Renamed from `_getStyleTag()`.
	 * @return   string
	 */
	private function ___getCommonStyleTag($oCaller, $sIDPrefix)
	{

		$_sStyle = $this->addAndApplyFilters(
			$oCaller,
			array(
				"style_common_admin_page_framework",            // 3.2.1+
				"style_common_{$this->oProp->sClassName}",
			),
			'' // AdminPageFramework_CSS::getDefaultCSS() @deprecated 3.9.0 No longer uses internal stylesheets
		);
		$_sStyle = $this->isDebugMode() ? $_sStyle : $this->getCSSMinified($_sStyle);
		$_sStyle = trim($_sStyle);
		if ($_sStyle)
		{
			return "<style type='text/css' id='" . esc_attr(strtolower($sIDPrefix)) . "'>"
				. $_sStyle
				. "</style>";
		}


	}
	/**
	 * @internal
	 * @since    3.5.7
	 * @since    3.8.22  Renamed from `_getIEStyleTag()`.
	 * @return   string
	 */
	private function ___getCommonIEStyleTag($oCaller, $sIDPrefix)
	{
		$_sStyleIE = $this->addAndApplyFilters(
			$oCaller,
			array(
				"style_ie_common_admin_page_framework",         // 3.2.1+
				"style_ie_common_{$this->oProp->sClassName}",
			),
			AdminPageFramework_CSS::getDefaultCSSIE()
		);
		$_sStyleIE = $this->isDebugMode() ? $_sStyleIE : $this->getCSSMinified($_sStyleIE);
		$_sStyleIE = trim($_sStyleIE);
		return $_sStyleIE
			? "<!--[if IE]><style type='text/css' id='" . esc_attr(strtolower($sIDPrefix . "-ie")) . "'>"
			. $_sStyleIE
			. "</style><![endif]-->"
			: '';
	}

	/**
	 * Prints the inline scripts of the meta-box common scripts.
	 *
	 * @internal
	 * @since    3.0.0
	 * @since    3.2.0  Moved to the base class from the meta box class.
	 * @remark   The meta box class may be instantiated multiple times so prevent echoing the same styles multiple times.
	 * @param    string $sIDPrefix  The id selector embedded in the script tag.
	 * @param    string $sClassName The class name that identify the call group. This is important for the meta-box class because it can be instantiated multiple times in one particular page.
	 */
	protected function _printCommonScripts($sIDPrefix, $sClassName)
	{

		if ($this->hasBeenCalled('COMMON_SCRIPT: ' . get_class($this) . '::' . __METHOD__))
		{
			return;
		}

		$_sScript = $this->addAndApplyFilters(
			$this->oProp->oCaller,
			array(
				"script_common_admin_page_framework",       // 3.2.1+
				"script_common_{$this->oProp->sClassName}",
			),
			AdminPageFramework_Property_Base::$_sDefaultScript
		);
		$_sScript = trim($_sScript);
		if (!$_sScript)
		{
			return;
		}
		echo "<script type='text/javascript' id='" . esc_attr(strtolower($sIDPrefix)) . "'>"
			. '/* <![CDATA[ */'
			. $_sScript
			. '/* ]]> */'
			. "</script>";

	}

	/**
	 * Prints the inline stylesheet of this class stored in this class property.
	 *
	 * @since    3.0.0
	 * @since    3.2.0 Made the properties storing styles empty. Moved to the base class.
	 * @internal
	 */
	protected function _printClassSpecificStyles($sIDPrefix)
	{

		$_oCaller = $this->oProp->oCaller;
		echo $this->_getClassSpecificStyleTag($_oCaller, $sIDPrefix);
		echo $this->_getClassSpecificIEStyleTag($_oCaller, $sIDPrefix);

		// Since 3.2.0, this method also gets called in the footer to ensure there is not any left styles.
		// This happens when a head tag item is added after the head tag is already rendered such as for widget forms.
		$this->oProp->sStyle = '';
		$this->oProp->sStyleIE = '';

	}
	/**
	 *
	 * @internal
	 * @since    3.5.7
	 * @return   string
	 */
	private function _getClassSpecificStyleTag($_oCaller, $sIDPrefix)
	{

		static $_iCallCount = 0;

		$_sFilterName = "style_{$this->oProp->sClassName}";
		if ($this->hasBeenCalled('FILTER: ' . $_sFilterName))
		{ // 3.8.22
			return '';
		}
		$_sStyle = $this->addAndApplyFilters($_oCaller, $_sFilterName, $this->oProp->sStyle);
		$_sStyle = $this->isDebugMode() ? $_sStyle : $this->getCSSMinified($_sStyle);
		$_sStyle = trim($_sStyle);
		if (!$_sStyle)
		{
			return '';
		}
		$_iCallCount++;
		$_sID = strtolower("{$sIDPrefix}-" . $this->oProp->sClassName . "_{$_iCallCount}");
		return "<style type='text/css' id='" . esc_attr($_sID) . "'>"
			. $_sStyle
			. "</style>";

	}
	/**
	 *
	 * @internal
	 * @since    3.5.7
	 * @return   string
	 */
	private function _getClassSpecificIEStyleTag($_oCaller, $sIDPrefix)
	{

		static $_iCallCountIE = 1;

		$_sFilterName = "style_ie_{$this->oProp->sClassName}";
		if ($this->hasBeenCalled('FILTER: ' . $_sFilterName))
		{ // 3.8.22
			return '';
		}
		$_sStyleIE = $this->addAndApplyFilters($_oCaller, $_sFilterName, $this->oProp->sStyleIE);
		$_sStyleIE = $this->isDebugMode() ? $_sStyleIE : $this->getCSSMinified($_sStyleIE);
		$_sStyleIE = trim($_sStyleIE);
		if (!$_sStyleIE)
		{
			return '';
		}
		$_iCallCountIE++;
		$_sID = strtolower("{$sIDPrefix}-ie-{$this->oProp->sClassName}_{$_iCallCountIE}");
		return "<!--[if IE]><style type='text/css' id='" . esc_attr($_sID) . "'>"
			. $_sStyleIE
			. "</style><![endif]-->";

	}

	/**
	 * Prints the inline scripts of this class stored in this class property.
	 *
	 * @since    3.0.0
	 * @since    3.2.0 Made the property empty that stores scripts. Moved to the base class.
	 * @internal
	 */
	protected function _printClassSpecificScripts($sIDPrefix)
	{

		static $_iCallCount = 1;
		$_sFilterName = "script_{$this->oProp->sClassName}";
		if ($this->hasBeenCalled('FILTER: ' . $_sFilterName))
		{ // 3.8.22
			return '';
		}
		$_sScript = $this->addAndApplyFilters($this->oProp->oCaller, $_sFilterName, $this->oProp->sScript);
		$_sScript = trim($_sScript);
		if (!$_sScript)
		{
			return '';
		}

		$_iCallCount++;
		$_sID = strtolower("{$sIDPrefix}-{$this->oProp->sClassName}_{$_iCallCount}");
		echo "<script type='text/javascript' id='" . esc_attr($_sID) . "'>"
			. '/* <![CDATA[ */'
			. $_sScript
			. '/* ]]> */'
			. "</script>";

		// As of 3.2.0, this method also gets called in the footer to ensure there is not any left scripts.
		// This happens when a head tag item is added after the head tag is already rendered such as for widget forms.
		$this->oProp->sScript = '';

	}

	/**
	 * Appends the CSS rules of the framework in the head tag.
	 *
	 * @since    2.0.0
	 * @since    2.1.5        Moved from `AdminPageFramework_MetaBox`. Changed the name from `addAtyle()` to `replyToAddStyle()`.
	 * @callback add_action() admin_head
	 * @internal
	 */
	public function _replyToAddStyle()
	{

		$_oCaller = $this->oProp->oCaller;
		if (!$_oCaller->isInThePage())
		{
			return;
		}

		$this->_printCommonStyles('admin-page-framework-style-common', __CLASS__);
		$this->_printClassSpecificStyles($this->_sClassSelector_Style . '-' . $this->oProp->sStructureType);

	}
	/**
	 * Appends the JavaScript script of the framework in the head tag.
	 *
	 * @callback add_action() admin_head
	 * @since    2.0.0
	 * @since    2.1.5        Moved from AdminPageFramework_MetaBox. Changed the name from `addScript()` to `replyToAddScript()`.
	 * @since    3.2.0        Moved from AdminPageFramework_Resource_post_meta_box.
	 * @internal
	 */
	public function _replyToAddScript()
	{

		$_oCaller = $this->oProp->oCaller;
		if (!$_oCaller->isInThePage())
		{
			return;
		}

		$this->_printCommonScripts('admin-page-framework-script-common', __CLASS__);
		$this->_printClassSpecificScripts($this->_sClassSelector_Script . '-' . $this->oProp->sStructureType);

	}

	/**
	 * Performs actual enqueuing items.
	 *
	 * @since       2.1.2
	 * @since       2.1.5       Moved from the main class.
	 * @param       array       $aEnqueueItem
	 * @internal
	 */
	protected function _enqueueSRC($aEnqueueItem)
	{

		$_sSRC = $this->___getSRCFormatted($aEnqueueItem);

		// For styles
		if ('style' === $aEnqueueItem['sType'])
		{
			$this->___enqueueStyle($_sSRC, $aEnqueueItem);
			return;
		}

		$this->___enqueueScript($_sSRC, $aEnqueueItem);

	}
	/**
	 * @param string $sSRC
	 * @param array $aEnqueueItem
	 * @since 3.9.0
	 */
	private function ___enqueueScript($sSRC, array $aEnqueueItem)
	{
		wp_enqueue_script(
			$aEnqueueItem['handle_id'],
			$sSRC,
			$aEnqueueItem['dependencies'],
			$aEnqueueItem['version'],
			did_action('admin_body_class') || (boolean) $aEnqueueItem['in_footer']
		);
		if ($aEnqueueItem['translation'])
		{
			wp_localize_script(
				$aEnqueueItem['handle_id'],
				empty($aEnqueueItem['translation_var']) ? $aEnqueueItem['handle_id'] : $aEnqueueItem['translation_var'],
				$aEnqueueItem['translation']
			);
		}
		if ($aEnqueueItem['conditional'])
		{
			wp_script_add_data($aEnqueueItem['handle_id'], 'conditional', $aEnqueueItem['conditional']);
		}
	}
	/**
	 * @param string $sSRC
	 * @param array $aEnqueueItem
	 * @since 3.9.0
	 */
	private function ___enqueueStyle($sSRC, array $aEnqueueItem)
	{
		wp_enqueue_style(
			$aEnqueueItem['handle_id'],
			$sSRC,
			$aEnqueueItem['dependencies'],
			$aEnqueueItem['version'],
			$aEnqueueItem['media']
		);
		$_aAddData = array('conditional', 'rtl', 'suffix', 'alt', 'title');
		foreach ($_aAddData as $_sDataKey)
		{
			if (!isset($aEnqueueItem[$_sDataKey]))
			{
				continue;
			}
			wp_style_add_data($aEnqueueItem['handle_id'], $_sDataKey, $aEnqueueItem[$_sDataKey]);
		}
	}

	/**
	 * Formats the SRC value.
	 * If a path is given and a .min file exists, it will be loaded.
	 * @param  array $aEnqueueItem
	 * @return string
	 * @since  3.8.31
	 */
	private function ___getSRCFormatted(array $aEnqueueItem)
	{

		if (!$this->oProp->bAutoloadMinifiedResource)
		{
			return $aEnqueueItem['sSRC'];
		}

		// If the site debug mode is on, use the one that user gave.
		if ($this->isDebugMode())
		{
			return $aEnqueueItem['sSRC'];
		}

		// If the user gave a url, use it.
		if ($this->isURL($aEnqueueItem['sSRCRaw']))
		{
			return $aEnqueueItem['sSRC'];
		}


		// At this point, the user gave a path.
		$_sMinPrefix = '.min';

		// If the user already handles a min version, then use it.
		if (false !== stripos($aEnqueueItem['sSRC'], $_sMinPrefix))
		{
			return $aEnqueueItem['sSRC'];
		}

		$_aPathParts = pathinfo($aEnqueueItem['sSRCRaw'])
			+ array('dirname' => '', 'filename' => '', 'basename' => '', 'extension' => ''); // avoid undefined index warnings

		// If there is no extension, avoid using a minified version.
		if (!$_aPathParts['extension'])
		{
			return $aEnqueueItem['sSRC'];
		}

		$_aPathPartsURL = pathinfo($aEnqueueItem['sSRC'])
			+ array('dirname' => '', 'filename' => '', 'basename' => '', 'extension' => ''); // avoid undefined index warnings

		$_sPathMinifiedVersion = $_aPathParts['dirname'] . '/' . $_aPathParts['filename'] . $_sMinPrefix . '.' . $_aPathParts['extension'];
		return file_exists($_sPathMinifiedVersion)
			? $_aPathPartsURL['dirname'] . '/' . $_aPathPartsURL['filename'] . $_sMinPrefix . '.' . $_aPathPartsURL['extension']
			: $aEnqueueItem['sSRC'];

	}

	/**
	 * Enqueues framework required common scripts.
	 * @since 3.9.0
	 * @deprecated 3.9.0 Unused at the moment
	 */
	// public function _replyToEnqueueCommonScripts() {
	//     if ( $this->hasBeenCalled( 'COMMON_EXTERNAL_SCRIPTS: ' . __METHOD__ ) ) {
	//         return;
	//     }
	// Currently no common JS script is needed
	// $this->_addEnqueuingResourceByType(
	//     AdminPageFramework_Registry::$sDirPath . '/factory/_common/asset/js/common.js',
	//     array(
	//         'dependencies' => array( 'jquery' ),
	//         'in_footer' => true,
	//     ),
	//     'script'
	// );
	// }
	/**
	 * Enqueues framework required common stylesheets.
	 * @since    3.9.0
	 * @callback action wp_enqueue_scripts
	 */
	public function _replyToEnqueueCommonStyles()
	{
		if ($this->hasBeenCalled('COMMON_EXTERNAL_STYLES: ' . __METHOD__))
		{
			return;
		}
		$this->_addEnqueuingResourceByType(
			AdminPageFramework_Registry::$sDirPath . '/factory/_common/asset/css/common.css',
			array(
				'version' => AdminPageFramework_Registry::VERSION,
			),
			'style'
		);
	}

	/**
	 * Takes care of added enqueuing scripts by checking the currently loading page.
	 *
	 * @remark      A callback for the admin_enqueue_scripts hook.
	 * @since       2.1.2
	 * @since       2.1.5   Moved from the main class. Changed the name from `enqueueStylesCalback` to `replyToEnqueueStyles()`.
	 * @since       3.0.0   Changed the name to `_replyToEnqueueStyles()`.
	 * @since       3.2.0   Changed it unset the enqueued item so that the method can be called multiple times.
	 * @internal
	 */
	public function _replyToEnqueueStyles()
	{
		foreach ($this->oProp->aEnqueuingStyles as $_sKey => $_aEnqueuingStyle)
		{
			$this->_enqueueSRCByCondition($_aEnqueuingStyle);
			unset($this->oProp->aEnqueuingStyles[$_sKey]);
		}
	}

	/**
	 * Takes care of added enqueuing scripts by page slug and tab slug.
	 *
	 * @remark      A callback for the admin_enqueue_scripts hook.
	 * @since       2.1.2
	 * @since       2.1.5   Moved from the main class. Changed the name from `enqueueScriptsCallback` to `callbackEnqueueScripts()`.
	 * @since       3.0.0   Changed the name to `_replyToEnqueueScripts()`.
	 * @since       3.2.0   Changed it unset the enqueued item so that the method can be called multiple times.
	 * @internal
	 */
	public function _replyToEnqueueScripts()
	{
		foreach ($this->oProp->aEnqueuingScripts as $_sKey => $_aEnqueuingScript)
		{
			$this->_enqueueSRCByCondition($_aEnqueuingScript);
			unset($this->oProp->aEnqueuingScripts[$_sKey]);
		}
	}

	/**
	 * A plural version of _addEnqueuingResourceByType()
	 * @since  3.8.31
	 * @param  array    $aSRCs          An array of SRCs
	 * @param  array    $aCustomArgs    A custom argument array.
	 * @param  string   $sType          Accepts 'style' or 'script'
	 * @return string[] Added resource handle IDs.
	 */
	public function _enqueueResourcesByType($aSRCs, array $aCustomArgs = array(), $sType = 'style')
	{
		$_aHandleIDs = array();
		foreach ($aSRCs as $_sSRC)
		{
			$_aHandleIDs[] = call_user_func_array(array($this, '_addEnqueuingResourceByType'), array($_sSRC, $aCustomArgs, $sType));
		}
		return $_aHandleIDs;
	}

	/**
	 * Store an enqueuing resource item to the property by type to process later at once.
	 *
	 * @since       3.5.3
	 * @since       3.8.31      Moved from `AdminPageFramework_Resource_admin_page`.
	 * @since       3.8.31      Renamed from `_enqueueResourceByType()`
	 * @param       string      $sSRC           The source path or url.
	 * @param       array       $aCustomArgs    A custom argument array.
	 * @param       string      $sType          Accepts 'style' or 'script'
	 * @return      string      The script handle ID if added. If the passed url is not a valid url string, an empty string will be returned.
	 * @internal
	 */
	public function _addEnqueuingResourceByType($sSRC, array $aCustomArgs = array(), $sType = 'style')
	{

		$sSRC = trim($sSRC);
		if (empty($sSRC))
		{
			return '';
		}
		$_sRawSRC = wp_normalize_path($sSRC);
		$_sSRC = $this->getResolvedSRC($_sRawSRC);

		// Get the property name for the type
		$_sContainerPropertyName = $this->___getContainerPropertyNameByType($sType);
		$_sEnqueuedIndexPropertyName = $this->___getEnqueuedIndexPropertyNameByType($sType);

		$this->oProp->{$_sContainerPropertyName}[$_sSRC] = array_filter($this->getAsArray($aCustomArgs), array($this, 'isNotNull'))
			+ array(
				'sSRCRaw' => $_sRawSRC,
				'sSRC' => $_sSRC,
				'sType' => $sType,
				'handle_id' => $sType . '_' . strtolower($this->oProp->sClassName) . '_' . (++$this->oProp->{$_sEnqueuedIndexPropertyName}),
			)
			+ self::$_aStructure_EnqueuingResources;

		// Store the attributes in another container by url.
		$this->oProp->aResourceAttributes[$this->oProp->{$_sContainerPropertyName}[$_sSRC]['handle_id']] = $this->oProp->{$_sContainerPropertyName}[$_sSRC]['attributes'];

		return $this->oProp->{$_sContainerPropertyName}[$_sSRC]['handle_id'];

	}
	/**
	 * Returns the property name that contains the information of resources by type.
	 * @since   3.5.3
	 * @since   3.8.31      Moved from `AdminPageFramework_Resource_admin_page`.
	 * @return  string      the property name that contains the information of resources by type.
	 */
	private function ___getContainerPropertyNameByType($sType)
	{
		switch ($sType)
		{
			default:
			case 'style':
				return 'aEnqueuingStyles';
			case 'script':
				return 'aEnqueuingScripts';
		}
	}
	/**
	 * Returns the property name that contains the added count of resources by type.
	 * @since   3.5.3
	 * @since   3.8.31      Moved from `AdminPageFramework_Resource_admin_page`.
	 * @return  string      the property name that contains the added count of resources by type.
	 */
	private function ___getEnqueuedIndexPropertyNameByType($sType)
	{
		switch ($sType)
		{
			default:
			case 'style':
				return 'iEnqueuedStyleIndex';
			case 'script':
				return 'iEnqueuedScriptIndex';
		}
	}

}
