=== LTL Freight Quotes - SAIA Edition ===
Contributors: enituretechnology
 Tags: eniture,SAIA,LTL freight rates,LTL freight quotes, shipping estimates
Requires at least: 6.4
Tested up to: 6.6
Stable tag: 2.2.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Real-time LTL freight quotes from SAIA Freight. Fifteen day free trial.

== Description ==

SAIA Freight is a holding of SAIA Worldwide Inc. (NASDAQ: SAIAW) and is headquartered in Overland Park, Kansas. SAIA Worldwide has a comprehensive network in North America, and offers shipping of industrial, commercial and retail goods. The plugin retrieves 
the LTL freight rates you negotiated SAIA Freight, takes action on them according to the plugin settings, and displays the 
result as shipping charges in your WooCommerce shopping cart. To establish a SAIA Freight account call 1-800-610-6500.

**Key Features**

* Displays negotiated LTL shipping rates in the shopping cart.
* Provide quotes for shipments within the United States and to Canada.
* Custom label results displayed in the shopping cart.
* Display transit times with returned quotes.
* Product specific freight classes.
* Support for variable products.
* Define multiple warehouses.
* Identify which products drop ship from vendors.
* Product specific shipping parameters: weight, dimensions, freight class.
* Option to determine a product's class by using the built in density calculator.
* Option to include residential delivery fees.
* Option to include fees for lift gate service at the destination address.
* Option to mark up quoted rates by a set dollar amount or percentage.
* Works seamlessly with other quoting apps published by Eniture Technology.

**Requirements**

* WooCommerce 6.4 or newer.
* A SAIA Account Number .
* A SAIA Account Number Postal Code.
* A Third Party Account Number.
* Your username and password to SAIA Freight online shipping system.
* A license from Eniture Technology.

== Installation ==

**Installation Overview**

Before installing this plugin you should have the following information handy:

* Your SAIA Freight Business ID.
* Your username and password to SAIA Freight's online shipping system.

If you need assistance obtaining any of the above information, contact your local SAIA Freight office
or call the [SAIA Freight](http://saia.com) corporate headquarters at 1-800-610-6500.

A more comprehensive and graphically illustrated set of instructions can be found on the *Documentation* tab at
[eniture.com](https://eniture.com/woocommerce-saia-ltl-freight/).

**1. Install and activate the plugin**
In your WordPress dashboard, go to Plugins => Add New. Search for "LTL Freight Quotes - SAIA Edition", and click Install Now.
After the installation process completes, click the Activate Plugin link to activate the plugin.

**2. Get a license from Eniture Technology**
Go to [Eniture Technology](https://eniture.com/woocommerce-saia-ltl-freight/) and pick a
subscription package. When you complete the registration process you will receive an email containing your license key and
your login to eniture.com. Save your login information in a safe place. You will need it to access your customer dashboard
where you can manage your licenses and subscriptions. A credit card is not required for the free trial. If you opt for the free
trial you will need to login to your [Eniture Technology](http://eniture.com) dashboard before the trial period expires to purchase
a subscription to the license. Without a paid subscription, the plugin will stop working once the trial period expires.

**3. Establish the connection**
Go to WooCommerce => Settings => SAIA Freight. Use the *Connection* link to create a connection to your SAIA Freight
account.

**5. Select the plugin settings**
Go to WooCommerce => Settings => SAIA Freight. Use the *Quote Settings* link to enter the required information and choose
the optional settings.

**6. Enable the plugin**
Go to WooCommerce => Settings => Shipping. Click on the link for SAIA Freight and enable the plugin.

**7. Configure your products**
Assign each of your products and product variations a weight, Shipping Class and freight classification. Products shipping LTL freight should have the Shipping Class set to “LTL Freight”. The Freight Classification should be chosen based upon how the product would be classified in the NMFC Freight Classification Directory. If you are unfamiliar with freight classes, contact the carrier and ask for assistance with properly identifying the freight classes for your  products. 

== Frequently Asked Questions ==

= What happens when my shopping cart contains products that ship LTL and products that would normally ship FedEx or USPS? =

If the shopping cart contains one or more products tagged to ship LTL freight, all of the products in the shopping cart 
are assumed to ship LTL freight. To ensure the most accurate quote possible, make sure that every product has a weight, dimensions and a freight classification recorded.

= What happens if I forget to identify a freight classification for a product? =

In the absence of a freight class, the plugin will determine the freight classification using the density calculation method. To do so the products weight and dimensions must be recorded. This is accurate in most cases, however identifying the proper freight class will be the most reliable method for ensuring accurate rate estimates.

= Why was the invoice I received from SAIA Freight more than what was quoted by the plugin? =

One of the shipment parameters (weight, dimensions, freight class) is different, or additional services (such as residential 
delivery, lift gate, delivery by appointment and others) were required. Compare the details of the invoice to the shipping 
settings on the products included in the shipment. Consider making changes as needed. Remember that the weight of the packaging 
materials, such as a pallet, is included by the carrier in the billable weight for the shipment.

= How do I find out what freight classification to use for my products? =

Contact your local SAIA Freight office for assistance. You might also consider getting a subscription to ClassIT offered 
by the National Motor Freight Traffic Association (NMFTA). Visit them online at classit.nmfta.org.

= How do I get a SAIA Freight account? =

SAIA Freight is a logistics company. Check your phone book for local listings or call  1-800-610-6500.

= Where do I find my SAIA Freight username and password? =

Usernames and passwords to SAIA Freight’s online shipping system are issued by SAIA Freight. If you have a SAIA Freight account number, go to [saia.com](http://saia.com) and click the login link at the top right of the page. You will be redirected to a page where you can register as a new user. If you don’t have a SAIA Freight account, contact the SAIA Freight at 1-800-610-6500.

= How do I get a license key for my plugin? =

You must register your installation of the plugin, regardless of whether you are taking advantage of the trial period or 
purchased a license outright. At the conclusion of the registration process an email will be sent to you that will include the 
license key. You can also login to eniture.com using the username and password you created during the registration process 
and retrieve the license key from the My Licenses tab.

= How do I change my plugin license from the trail version to one of the paid subscriptions? =

Login to eniture.com and navigate to the My Licenses tab. There you will be able to manage the licensing of all of your 
Eniture Technology plugins.

= How do I install the plugin on another website? =

The plugin has a single site license. To use it on another website you will need to purchase an additional license. 
If you want to change the website with which the plugin is registered, login to eniture.com and navigate to the My Licenses tab. 
There you will be able to change the domain name that is associated with the license key.

= Do I have to purchase a second license for my staging or development site? =

No. Each license allows you to identify one domain for your production environment and one domain for your staging or 
development environment. The rate estimates returned in the staging environment will have the word “Sandbox” appended to them.

= Why isn’t the plugin working on my other website? =

If you can successfully test your credentials from the Connection page (WooCommerce > Settings > SAIA Freight > Connections) 
then you have one or more of the following licensing issues:

1) You are using the license key on more than one domain. The licenses are for single sites. You will need to purchase an additional license.
2) Your trial period has expired.
3) Your current license has expired and we have been unable to process your form of payment to renew it. Login to eniture.com and go to the My Licenses tab to resolve any of these issues.

== Screenshots ==

1. Quote settings page
2. Warehouses and Drop Ships page
3. Quotes displayed in cart

== Changelog ==

= 2.2.9 =
* Update: Updated connection tab according to WordPress requirements 

= 2.2.8 =
* Fix:  Fixed issue with origin markup for certain customers. 

= 2.2.7 =
* Update:Introduced a new hook used by the Microwarehouse add-on plugin

= 2.2.6 =
* Update: Introduced capability to suppress parcel rates once the weight threshold has been reached.
* Update: Compatibility with WordPress version 6.5.3
* Update: Compatibility with PHP version 8.2.0
* Fix:  Incorrect product variants displayed in the order widget.

= 2.2.5 =
* Update: Display "Free Shipping" at checkout when handling fee in the quote settings is  -100% .
* Update: Introduce the Shipping Logs feature.

= 2.2.4 =
* Update: Compatibility with WooCommerce HPOS(High-Performance Order Storage)

= 2.2.3 =
* Update: Modified expected delivery message at front-end from “Estimated number of days until delivery” to “Expected delivery by”.
* Fix: Inherent Flat Rate value of parent to variations.

= 2.2.2 =
* Update: Added compatibility with "Address Type Disclosure" in Residential address detection

= 2.2.1 =
* Fix: Fixed handling fee/ markup not being included

= 2.2.0 =
* Update: Introduced origin level markup and product level markup

= 2.1.11 =
* Update: Compatibility with WordPress version 6.1
* Update: Compatibility with WooCommerce version 7.0.1

= 2.1.10 =
* Fix: Fixed variable type conversion in php 8 and greater. 

= 2.1.9 =
* Fix: Product variant was not picking it's parent freight class.

= 2.1.8 =
* Update: Included product parent id along with variant ID required by freightdesk.online

= 2.1.7 =
* Update: Introduced connectivity from the plugin to FreightDesk.Online using Company ID

= 2.1.6 =
* Update: Compatibility with WordPress version 6.0.
* Update: Included tabs for freightdesk.online and validate-addresses.com

= 2.1.5 =
* Update: Compatibility with WordPress multisite network
* Fix: Fixed support link.

= 2.1.4 =
* Update: Compatibility with PHP version 8.1.
* Update: Compatibility with WordPress version 5.9.

= 2.1.3 =
* Update: Added feature "Show terminal information for HAT option".

= 2.1.2 =
* Update: Revised 2.1.1. code optimization about freight class.

= 2.1.1 =
* Update: Relocation of NMFC Number field along with freight class.

= 2.1.0 =
* Update: Updated compatibility with the Pallet Packaging plugin and analytics.

= 2.0.0 =
* Update: Compatibility with PHP version 8.0.
* Update: Compatibility with WordPress version 5.8.
* Fix: Corrected product page URL in connection settings tab.

= 1.4.1 =
* Update: Bug fixed.
* Update: Origin terminal address's content updated.

= 1.4.0 =
* Update: Added feature "Weight threshold limit".
* Update: Added feature In-store pickup with terminal information.

= 1.3.0 =
* Update: Microwarehouse.
* Update: FDO images URL.
* Update: Virtual product at order widget.
* Update: CSV columns updated.

= 1.2.1 =
* Update: Introduced new features, Compatibility with WordPress 5.7, Order detail widget for draft orders, improved order detail widget for Freightdesk.online, compatibly with Shippable add-on, compatibly with Account Details(ET) add-don(Capturing account number on checkout page).

= 1.2.0 =
* Update: Compatibility with WordPress 5.6

= 1.1.3 =
* Update: Introduced product nesting feature. 

= 1.1.2 =
* Update: Compatibility with WooCommerce 5.5.

= 1.1.1 =
* Update: Introduced weight of handling unit and maximum weight per handling unit.

= 1.1.0 =
* Update: Compatibility with shipping solution freightdesk.online

= 1.0.6 =
* Update: Compatibility with WooCommerce 5.4.

= 1.0.5 =
* Update: Introduced SAIA account type[Sender/Receiver] in account specific warehouse

= 1.0.4 =
* Update: compatibility with custom programming.

= 1.0.4 =
* Update: compatibility with custom programming.

= 1.0.3 =
* Fix: Removed extra files and rename class name.

= 1.0.2 =
* Update: Introduced Account Number on warehouse tab

= 1.0.1 =
* Update: Introduced Account Number Zipcode and Third Party Account Number fields on connection settings

= 1.0.0 =
* Initial release.

== Upgrade Notice ==
