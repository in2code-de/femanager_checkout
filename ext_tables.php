<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

/**
 * Static TypoScripts
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'femanager_checkout',
    'Configuration/TypoScript/Main',
    'Main Settings'
);

    $config = function ($extKey, &$TCA) {

    /**
     * FE Plugin
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('In2code.' . $extKey, 'Pi1', 'FE_Manager checkout');

    /**
     * Disable non needed fields in tt_content
     */
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['femanager_checkout_pi1'] = 'select_key';
};


$config($_EXTKEY, $TCA);
unset($config);
