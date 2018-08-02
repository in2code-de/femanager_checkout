.. include:: ../Includes.txt
.. include:: Images.txt

.. _installation:

Installation and Configuration
==============================

.. only:: html


* Install extension via composer or extension manager
* Include Main ts in typoscript template
* Installtool: Add these params to $GLOBALS['TYPO3_CONF_VARS']['FE']['cHashExcludedParameters'] -> paymentId, token, PayerID
* Setup the paypal settings for client, groups and payment (see configuration)
* Setup up at least two usergroups, for example:
  * "registered"
  * "premium"
* Setup femanager, that new users are added to the usergroup "registered"
* Add the femanager_checkout plugin to a page, where only logged users have access
* setup the typoscript setting for groups.premium (should contain the ID of the usergroup, which a user is assigned after
the payment) and groups.basis (the usergroup which will be removed after the payment)


