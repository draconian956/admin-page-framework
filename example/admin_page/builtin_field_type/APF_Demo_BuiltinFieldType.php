<?php
/**
 * Admin Page Framework Loader
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a page to the loader plugin.
 * 
 * @since       3.6.2
 * @package     AdminPageFramework
 * @subpackage  Example 
 */
class APF_Demo_BuiltinFieldType {

    public function __construct( $oFactory ) {
     
        /* ( required ) Add sub-menu items (pages or links) */
        $oFactory->addSubMenuItems(
            /*     Example
              for sub-menu pages, e.g.
                  'title' => 'Your Page Title',
                'page_slug' => 'your_page_slug', // avoid hyphen(dash), dots, and white spaces
                'screen_icon' => 'edit', // for WordPress v3.7.x or below
                'capability' => 'manage-options',
                'order' => 10,
                
              for sub-menu links, e.g.
                'title' => 'Google',
                'href' => 'http://www.google.com',
                
            */
            array(
                'title'         => __( 'Built-in Field Types', 'admin-page-framework-loader' ),
                'page_slug'     => 'apf_builtin_field_types',
                'screen_icon'   => 'options-general', // one of the screen type from the below can be used.
                /* Screen Types (for WordPress v3.7.x or below) :
                    'edit', 'post', 'index', 'media', 'upload', 'link-manager', 'link', 'link-category', 
                    'edit-pages', 'page', 'edit-comments', 'themes', 'plugins', 'users', 'profile', 
                    'user-edit', 'tools', 'admin', 'options-general', 'ms-admin', 'generic',  
                */     
                'order' => 1, // ( optional ) - if you don't set this, an index will be assigned internally in the added order
            )
        );        
        
        // Define in-page tabs - here tabs are defined in the below classes.
        $_aTabClasses = array(
            'APF_Demo_BuiltinFieldTypes_Text',
            'APF_Demo_BuiltinFieldTypes_Selector',
            'APF_Demo_BuiltinFieldTypes_File',
            'APF_Demo_BuiltinFieldTypes_Checklist',
            'APF_Demo_BuiltinFieldTypes_MISC',
            'APF_Demo_BuiltinFieldTypes_System', 
        );
        foreach ( $_aTabClasses as $_sTabClassName ) {
            if ( ! class_exists( $_sTabClassName ) ) {
                continue;                
            }        
            new $_sTabClassName;
        }
        
    }

}