=== Connections Business Directory Open Hours ===
Contributors: shazahm1@hotmail.com
Donate link: https://connections-pro.com/
Tags: addresses, address book, addressbook, bio, bios, biographies, business, businesses, business directory, business-directory, business directory plugin, directory plugin, directory widget, church, contact, contacts, connect, connections, directory, directories, hcalendar, hcard, ical, icalendar, image, images, list, lists, listings, member directory, members directory, members directories, microformat, microformats, page, pages, people, profile, profiles, post, posts, plugin, shortcode, staff, user, users, vcard, wordpress business directory, wordpress directory, wordpress directory plugin, wordpress business directory, business hours, widget
Requires at least: 5.1
Tested up to: 5.7
Requires PHP: 5.6.20
Stable tag: 1.2.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Extension for the Connections Business Directory that adds the ability to add business hours to an entry.

== Description ==

This is an extension plugin for the [Connections Business Directory Plugin](https://wordpress.org/plugins/connections/) please be sure to install and active it before adding this plugin.

What does this plugin do? It adds the ability to add hours of operation to entries.

Business hours can be entered for each day of the week, and if needed multiple hours open/close periods can be added per day.

It comes with a widget that will display only on the detail view of a single entry. The widget can be configured to show a "We are currently open." status message and highlight the current open period as well as either displaying a "Closed Today" message or simply not showing the closed day.

The business hours can also be optionally shown within the entry card in both the results list view and single entry detail view.

[Checkout the screenshots and video of this plugin in action.](https://connections-pro.com/add-on/hours/)

Here are some great **free extensions** (with more on the way) that enhance your experience with Connections Business Directory:

**Utility**

* [Toolbar](https://wordpress.org/plugins/connections-toolbar/) :: Provides quick links to the admin pages from the admin bar.
* [Login](https://wordpress.org/plugins/connections-business-directory-login/) :: Provides a simple to use login shortcode and widget.

**Custom Fields**

* [Local Time](https://wordpress.org/plugins/connections-business-directory-local-time/) :: Add the business local time.
* [Facilities](https://wordpress.org/plugins/connections-business-directory-facilities/) :: Add the business facilities.
* [Income Level](https://wordpress.org/plugins/connections-business-directory-income-levels/) :: Add an income level.
* [Education Level](https://wordpress.org/plugins/connections-business-directory-education-levels/) :: Add an education level.
* [Languages](https://wordpress.org/plugins/connections-business-directory-languages/) :: Add languages spoken.
* [Hobbies](https://wordpress.org/plugins/connections-business-directory-hobbies/) :: Add hobbies.

**Misc**

* [Face Detect](https://wordpress.org/plugins/connections-business-directory-face-detect/) :: Applies face detection before cropping an image.

**[Premium Extensions](https://connections-pro.com/extensions/)**

* [Authored](https://connections-pro.com/add-on/authored/) :: Displays a list of blog posts written by the entry on their profile page.
* [Contact](https://connections-pro.com/add-on/contact/) :: Displays a contact form on the entry's profile page to allow your visitors to contact the entry without revealing their email address.
* [CSV Import](https://connections-pro.com/add-on/csv-import/) :: Bulk import your data in to your directory.
* [Custom Category Order](https://connections-pro.com/add-on/custom-category-order/) :: Order your categories exactly as you need them.
* [Custom Entry Order](https://connections-pro.com/add-on/custom-entry-order/) :: Allows you to easily define the order that your business directory entries should be displayed.
* [Enhanced Categories](https://connections-pro.com/add-on/enhanced-categories/) :: Adds many features to the categories.
* [Form](https://connections-pro.com/add-on/form/) :: Allow site visitor to submit entries to your directory. Also provides frontend editing support.
* [Link](https://connections-pro.com/add-on/link/) :: Links a WordPress user to an entry so that user can maintain their entry with or without moderation.
* [ROT13 Encryption](https://connections-pro.com/add-on/rot13-email-encryption/) :: Protect email addresses from being harvested from your business directory by spam bots.
* [SiteShot](https://connections-pro.com/add-on/siteshot/) :: Show a screen capture of the entry's website.
* [Widget Pack](https://connections-pro.com/add-on/widget-pack/) :: A set of feature rich, versatile and highly configurable widgets that can be used to enhance your directory.


== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

== Frequently Asked Questions ==

None yet....

== Screenshots ==

[Screenshots can be found here.](https://connections-pro.com/add-on/hours/)

== Changelog ==

= 1.2.1 05/03/2021 =
* TWEAK: Remove use of `create_function()`.
* TWEAK: Remove invalid `name` attribute from the hours of operation table.
* DEV: Correct code alignment.
* DEV: phpDoc correction.

= 1.2 08/19/2020 =
* TWEAK: Add a few additional styles for the add/remove period buttons to help prevent themes/plugins from breaking their display.
* OTHER: Bump copyright year.
* OTHER: Bump "Tested up to:" to version 5.5.
* OTHER: Bump "Requires PHP:" to version 5.6.20.
* OTHER: Bump "Requires at least:" to version 5.0.
* OTHER: Update URL from http to https.
* OTHER: Update minified files.

= 1.1 10/16/2018 =
* TWEAK: Remove use of `create_function()` to register the widget.
* TWEAK: Restructure code to more closely match the addons so it is more consistent.
* TWEAK: Minor tweak to CSS to help make sure the time sliders are aligned with their labels.
* OTHER: Plugin header updates.
* OTHER: Remove extra whitespace and comment out unused methods.
* OTHER: Bump minimum required PHP version to match core.
* DEV: phpDoc additions and corrections.

= 1.0.11 06/08/2018 =
* TWEAK: Only save open hours data array if open hours have been added.
* BUG: Fix ability to clear an open/close time period input field.

= 1.0.10 12/14/2017 =
* BUG: Corrected PHP 7.1 warning, "A non-numeric value encountered".

= 1.0.9 09/01/2017 =
* BUG: n displaying the time on the frontend, supplied the convert from time format so it is properly displayed when converting from one format to another.
* DEV: phpDoc corrections.

= 1.0.8 08/25/2017 =
* TWEAK: Refactor Connections_Business_Hours::dateTimePickerOptions() to utilize cnFormatting::dateFormatPHPTojQueryUI() to handle converting the PHP datetime format to the jQueryUI datetime format.
* TWEAK: Refactor Connections_Business_Hours::formatTime() to utilize cnDate::createFromFormat() to handle the conversion of to/from date formats.
* TWEAK: Update usages of Connections_Business_Hours::formatTime() to new signature.
* TWEAK: CSS tweak.
* BUG: PHP error notice fix, check for array value first.
* I18N: Add German translation.
* DEV: Correct code indent.
* DEV: phpDoc correction.

= 1.0.7 06/29/2016 =
* BUG: `<th>` tags should be within `<tr>` tags.
* OTHER: Readme tweaks.
* I18N: Add Dutch (Netherlands) translation.
* DEV: Update .gitignore.

= 1.0.6 05/05/2015 =
* BUG: Add missing text domain to "Business Hours" so it is translatable.
* BUG: Correct text domain in the widget.
* BUG: Fix filters for language files.
* TWEAK: Comment out unused activation/deactivation hooks since they are not used.
* I18N: Add Swedish (Sweden) translation.
* I18N: Update POT file.

= 1.0.5 05/01/2015 =
* BUG: Load plugins textdomain on plugins_loaded action hook.
* BUG: Make two missed strings translation ready.
* BUG: Empty string should not be run thru gettext.
* TWEAK: Add POT file for translation.
* TWEAK: Add Income Level link to readme.txt.

= 1.0.4 07/14/2014 =
* TWEAK: If no open hours have been set for any day or any periods within a day, do not show the open hours content block.

= 1.0.3 06/05/2014 =
* BUG: The front-end CSS file was not being enqueued if Form was not installed and activated.

= 1.0.2 05/30/2014 =
* TWEAK: CSS tweak for better Form 2.0 integration.

= 1.0.1 05/26/2014 =
* TWEAK: Refine CSS and JS enqueueing logic. Requires Connections >= 0.8.9.

= 1.0 05/03/2014 =
* Initial release.

== Upgrade Notice ==

= 1.0 =
Initial release.

= 1.0.11 =
It is recommended to backup before updating. Requires WordPress >= 4.5.3 and PHP >= 5.3 PHP version >= 7.1 recommended.

= 1.1 =
It is recommended to backup before updating. Requires WordPress >= 4.5.3 and PHP >= 5.4 PHP version >= 7.1 recommended.

= 1.0.6 =
It is recommended to backup before updating. Requires WordPress >= 5.0 and PHP >= 5.6.20 PHP version >= 7.2 recommended.

= 1.2.1 =
It is recommended to backup before updating. Requires WordPress >= 5.1 and PHP >= 5.6.20 PHP version >= 7.2 recommended.

