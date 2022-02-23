<?php
/**
 * Beautify PHP files.
 */

// Disable PHP notices produced in recent PHP versions by Beautifier (as it is old)
error_reporting( error_reporting() & ~E_NOTICE );

$_nStart = microtime( true );

/* Set necessary paths */
$sTargetBaseDir              = dirname( dirname( __DIR__ ) );
$sTargetDir                  = $sTargetBaseDir . '/development';
$sDestinationDirectoryPath   = $sTargetBaseDir . '/library/apf';
$sLicenseFileName            = 'LICENSE.txt';
$sLicenseSourceFilePath      = $sTargetDir . '/' . $sLicenseFileName;
$sHeaderClassName            = 'AdminPageFramework_BeautifiedVersionHeader';
$sHeaderClassPath            = $sTargetDir . '/cli/AdminPageFramework_BeautifiedVersionHeader.php';

// For get about the rest.

/* If accessed from a browser, exit. */
$bIsCLI                      = php_sapi_name() == 'cli';
$sCarriageReturn             = $bIsCLI ? PHP_EOL : '<br />';
if ( ! $bIsCLI ) { 
    exit; 
}

/* Include necessary files */
require( __DIR__ . '/class/PHP_Class_Files_Beautifier.php' );

/* Check the permission to write. */
if (  ! is_writable( dirname( $sDestinationDirectoryPath ) ) ) {
    exit( sprintf( 'The permission denied. Make sure if the folder, %1$s, allows the script to modify/create a file.', dirname( $sDestinationDirectoryPath ) ) );
}

/* Create a beautified version of the framework. */
echo 'Started...' . $sCarriageReturn;
new PHP_Class_Files_Beautifier( 
    $sTargetDir, 
    $sDestinationDirectoryPath, 
    array(
    
        'header_class_name'    => $sHeaderClassName,
        'header_class_path'    => $sHeaderClassPath,
        'output_buffer'        => true,
        'header_type'          => 'CONSTANTS',    
        'exclude_classes'      => array(),
        
        'css_heredoc_keys'     => array( 'CSSRULES' ),
        'js_heredoc_keys'   => array( 'JAVASCRIPTS' ),  
        
        'search'               => array(
            'allowed_extensions'    => array( 'php' ),    // e.g. array( 'php', 'inc' )
            // 'exclude_dir_paths'  => array( $sTargetBaseDir . '/include/class/admin' ),
            'exclude_dir_names'     => array( '_document', 'document', 'cli' ),
            'exclude_dir_names_regex' => array(
                '/\.bundle$/'
            ),
            'exclude_file_names'    => array(
                'AdminPageFramework_InclusionClassFilesHeader.php',
                'AdminPageFramework_MinifiedVersionHeader.php',
                'AdminPageFramework_BeautifiedVersionHeader.php',            
            ),
            'is_recursive'            => true,
        ),
        'include'               => array(
            'allowed_extensions'    => array( 'js', 'css', 'map' ),    // e.g. array( 'php', 'inc' )
        ),
        'combine'              => array(
            'inheritance'       => false,
            'exclude_classes' => array( 
                'AdminPageFramework_Form_Meta',
                'AdminPageFramework_MetaBox_Page',
            ),
        ),
    )
);

// Copy the license text.
@copy( 
    $sLicenseSourceFilePath,  // source
    $sDestinationDirectoryPath . '/' . $sLicenseFileName     // destination
);

// Generate a inclusion class list.
include( __DIR__ . '/generate-class-map-compiled.php' );

echo 'Done!' . $sCarriageReturn;
echo 'Elapsed Seconds: ' . ( microtime( true ) - $_nStart ) . $sCarriageReturn;