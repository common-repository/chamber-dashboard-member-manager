=== Chamber Dashboard Member Manager ===
Contributors: cguntur, gwendydd, jpkay, LisaK.social
Tags: Chamber of Commerce, business directory, businesses, membership, membership fees
Donate link: http://chamberdashboard.com/donate
Requires at least: 4.6
Tested up to: 5.7
Stable tag: 2.5.8
Requires PHP: 7.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Manage the membership levels and payments for your chamber of commerce or other membership-based organization.

== Description ==
Chamber Dashboard Member Manager is a part of the [Chamber Dashboard](http://chamberdashboard.com) collection of plugins and themes designed to meet the needs of chambers of commerce and other member organizations. Member Manager works with [Chamber Dashboard Business Directory](https://wordpress.org/plugins/chamber-dashboard-business-directory/) to allow members or businesses to signup or renew online.

= With Chamber Dashboard Member Manager, you can: =
*   Create different membership levels
*   Describe and display the membership levels' prices and perks
*   Let organizations sign up for membership on your website
*   Collect payment with PayPal
*   Automatically set the membership renewal date
*   Automatic email notifications to the admin and the business/member
*   Track when membership payments are due
*   [Premium] Use payment methods other than PayPal
*   [Premium] Automatically send invoices when membership payments are due

> Note: The premium features are available through our premium plugins. You can get more information on our premium plugins [here](https://chamberdashboard.com/add-ons/).

The Chamber Dashboard Member Manager requires that you have the [Chamber Dashboard Business Directory](https://wordpress.org/plugins/chamber-dashboard-business-directory/) installed.
You can learn more at [chamberdashboard.com](http://chamberdashboard.com)

= Basic Usage =

Go to Businesses->Membership Levels to enter in your membership levels, perks, and prices.
To display the membership levels and perks on your site, use the shortcode [membership_levels]
Enter your PayPal email address on the Member Manager settings page.
Use the [membership_form] shortcode to display a form that will let people sign up and pay for membership.

For full instructions about how to use the plugin, go to [Chamber Dashboard Documentation](https://chamberdashboard.com/docs/plugin-features/online-payments/)

If you want to track the people associated with businesses in your organization, check out [the Chamber Dashboard CRM](https://wordpress.org/plugins/chamber-dashboard-crm/) plugin!

To display an event calendar, you can use the [the Chamber Dashboard Events Calendar](https://wordpress.org/plugins/chamber-dashboard-events-calendar/) plugin!

= Support =

If you are using our free plugins, you can access our documentation <a href="https://chamberdashboard.com/chamber-dashboard-support/" target="_blank">here</a>

If you have purchased any of our premium plugins or our support plan, you can open a <a href="https://chamberdashboard.com/submit-support-ticket/" target="_blank">support ticket</a>


== Installation ==

= Using The WordPress Dashboard =
1. Navigate to the \'Add New\' in the plugins dashboard
2. Search for \'chamber dashboard member manager\'
3. Click \'Install Now\'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =
1. Navigate to the \'Add New\' in the plugins dashboard
2. Navigate to the \'Upload\' area
3. Select `chamber-dashboard-member-manager.zip` from your computer
4. Click \'Install Now\'
5. Activate the plugin in the Plugin dashboard

= Using FTP =
1. Download `chamber-dashboard-member-manager.zip`
2. Extract the `chamber-dashboard-member-manager` directory to your computer
3. Upload the `chamber-dashboard-business-directory` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

== Frequently Asked Questions ==

= What payment methods are accepted? =
PayPal is the only accepted payment method.  We offer a [premium version](https://chamberdashboard.com/downloads/member-manager-pro/) that gives you the option to choose additional payment methods via WooCommerce.

== Screenshots ==
1. The membership form
2. Public invoice view
3. Entering an invoice in the dashboard
4. Payment report

== Changelog ==
= 2.5.8 =
* Fixed some emails not being sent to the members and admin 

= 2.5.7 =
* Added an option to offer free membership levels 

= 2.5.6 =
* Fixed a few issues with the member account shortcode 
* Added the login form block 
* Added a custom filter to display member information on the member account page

= 2.5.5 =
* Fixed an issue with WC Payments

= 2.5.4 =
* Fixed the error in the admin dashboard
* Works with WP 5.6 

= 2.5.3 =
* Fixed a few plugin issues
* Description and logo fields can be added to the membership form through the membership form block 

= 2.5.2 =
* Custom fields can be added to the membership form through the form block 

= 2.5.1 =
* Membership form available as a Gutenberg block
* Fixed a couple of issues with the membership level perks

= 2.5.0 =
* Fixed few issues to make it compatible with WP 5.5

= 2.4.9 = 
* The plugin now integrates with the Chamber Dashboard Payment Options plugin to display membership levels using a Gutenberg block.

= 2.4.8 =
* The plugin now works with payment options and wc payments 

= 2.4.7 =
* Fixed the issue of businesses not getting automatically lapsed

= 2.4.6 =
* Added an option to add a placeholder for phone numbers and option to disable check payments 
* Fixed the processing fee and tax fields display issues on the front end invoices 

= 2.4.5 =
* Updated the settings pages

= 2.4.4 =
* The member account page shows the add event link and the connected events if the option to add events is enabled

= 2.4.3 =
* Option to add the login/logout to the selected menu
* Option to display member account page

= 2.4.2 =
* Fixed some JavaScript errors in the admin

= 2.4.1 =
* Members only feature added. Content can be restricted based on membership levels

= 2.4.0 =
* Fixed a few errors and warnings

= 2.3.9 =
* Country field is now being pulled correctly from the Join Now form and the renewals form

= 2.3.8 =
* Fixed the issue with Join now form not creating orders with MM Pro

= 2.3.7 =
* Compatible with the new addons
* Fixed a couple of issues related to recurring payments plugin

= 2.3.6 =
* The MRD and the PayPal payments now work with the new RP option

= 2.3.5 =
* Renewal form now works only with an existing business

= 2.3.4 =
* Invoice formatting has been fixed.

= 2.3.3 =
* A return url for PayPal can now be specified in the settings page
* Works with Gutenberg

= 2.3.2 =
* Added option to add a consent box and a Terms and Conditions statement along with the Join Now form.
* Added an option to add referral dropdown to the Join Now form

= 2.3.1 =
* Fixed some issues with PHP 7.2

= 2.3.0 =
* Fixed the issue with payment reports

= 2.2.9 =
* Added country field to the Join Now form
* Added the option to set the business address the same as the billing address

= 2.2.8.1 =
* Corrected some typos in readme.txt

= 2.2.8 =
* Fixed the password field not being displayed when Member Updater is active

= 2.2.7 =
* Fixed the issue of people records being created while using the renewal form

= 2.2.6 =
* Fixed some minor issues with invoice numbers and membership form fields.

= 2.2.5 =
* Fixed some minor errors to make it compatible with MM Pro.

= 2.2.4 =
* Fixed the issue of PayPal sending error messages

= 2.2.3 =
* Sign up form is now separated from renewal form
* Ability to add category when a member uses the sign up form

= 2.2.2 =
* Fixed some warnings and error messages

= 2.2.1 =
* Fixed the warnings showing up on plugin activation

= 2.2 =
* Fixed the processing fee not being sent to PayPal

= Earlier versions =

For the changelog of earlier versions, please refer to the separate changelog.txt file.
