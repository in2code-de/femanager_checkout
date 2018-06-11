<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$config = function ($extKey, &$TCA) {

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'In2code.' . $extKey,
        'Pi1',
        [
            'Checkout' => 'checkout, message, pay, cancelPayment',
        ],
        [
            'Checkout' => 'checkout, message, pay, cancelPayment',
        ]
    );
};
    $config($_EXTKEY, $TCA);
    unset($config);
