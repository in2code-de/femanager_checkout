.. include:: ../Includes.txt
.. include:: Images.txt

.. _installation:

Configuration
=============

.. only:: html


TypoScript Configuration
^^^^^^^^^^^^^^^^^^^^^^^^

Explanation Plugin Settings
"""""""""""""""""""""""""""

All typoscript settings refer to plugin.tx_femanagercheckout.settings

.. t3-field-list-table::
 :header-rows: 1

 - :Tab:
      TS Key
   :Field:
         Field Name
      :Description:
         Description
      :Default:
         Default Value

    - :Tab:
         paypal
      :Field:
         env
      :Description:
         possible value: sandbox or production - use sandbox for testing
      :Default:
         sandbox

    - :Tab:
         paypal.client
      :Field:
         sandbox
      :Description:
         Secret key for sandbox environment
      :Default:
         [empty]

    - :Tab:
         paypal.client
      :Field:
         production
      :Description:
         Secret key for production environment
      :Default:
         [empty]

    - :Tab:
         paypal.client.style
      :Field:
         color
      :Description:
         setup the style for the button, please take a look at the paypal express API, to find allowed values
      :Default:
         gold

    - :Tab:
         paypal.client.style
      :Field:
         size
      :Description:
         setup the size for the button, please take a look at the paypal express API, to find allowed values
      :Default:
         small

    - :Tab:
         Registration
      :Field:
         Notify admin on registration (add one or more emails)
      :Description:
         Notify one or more email receivers (one per line) if a new user was completely registered
      :Default:
         [empty]

    - :Tab:
         paypal.client.payment.transactions.amount
      :Field:
         total
      :Description:
         The amount, which the user has to pay
      :Default:
         1

    - :Tab:
         paypal.client.payment.transactions.amount
      :Field:
         currency
      :Description:
         Setup the curreny for the payment
      :Default:
         EUR

    - :Tab:
         groups
      :Field:
         premium
      :Description:
         setup the UID of the group which should get access after the sucessful payment
      :Default:
         [empty]

    - :Tab:
         groups
      :Field:
         basis
      :Description:
         setup the UID of the group which has limited access. This group is removed after the successful payment
      :Default:
         [empty]


Plain Text
""""""""""

.. code-block:: text

plugin.tx_femanagercheckout {
	view {
		templateRootPaths {
			0 = EXT:femanager_checkout/Resources/Private/Templates/
			1 = {$plugin.femanager_checkout.view.templateRootPath}
		}
		partialRootPaths {
			0 = EXT:femanager/Resources/Private/Partials/
			1 = {$plugin.femanager_checkout.view.partialRootPath}
		}
		layoutRootPaths {
			0 = EXT:femanager_checkout/Resources/Private/Layouts/
			1 = {$plugin.femanager_checkout.view.layoutRootPath}
		}
	}
	persistence {
		storagePid = {$plugin.femanager_checkout.persistence.storagePid}
	}
	features {
	}

	settings {
		paypal {
			env = sandbox
			client {
				sandbox =
				production =
				style {
					color = gold
					size = small
				}
				payment.transactions.amount {
					total = 1
					currency = EUR
				}
			}
		}
		groups {
			premium =
			basis =
		}
	}
}
