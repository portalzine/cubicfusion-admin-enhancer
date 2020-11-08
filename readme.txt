 === cubicFUSION Admin Enhancer ===
Contributors: portalzine
Tags: admin, adminbar, admin menu, dashboard, tweaks, white label, templates, shortcodes, branding, custom, administration, plugin, login, client, navigation, appearance,  widgets, customizer
Requires at least: 5.0
Tested up to: 5.5.3
Requires PHP: 7.0
Stable tag: 0.2.5
License: GPLv2 or later
License URI: http://www.opensource.org/licenses/gpl-license.php

This plugin adds useful admin features and resources to help you tweak the wordpress administration. 

== Description ==

**cubicFUSION Admin Enhancer** is a free administration toolbox, that is work in progress.
The plugin is used to centralise things I love & need when sending out a finished website or project.
I am sure it can help others aswell.

= General Features =

* Centralize useful admin tweaks & enhancements.
* **SHORTCODES**: The first addition to the toolbox is Shortcodes. All dashboard widgets are converted to simple shortcodes. You can use those shortcodes within Elementor Pro or any other page builder that allows you to create custom admin dashboards. This makes it easy to build white-label dashboards, while still reusing all those nice dashboard widgets :)
Works perfectly with [Dashboard 
Welcome for Elementor](https://wordpress.org/plugins/dashboard-welcome-for-elementor/) or [Dashboard Welcome for Beaver Builder](https://wordpress.org/plugins/dashboard-welcome-for-beaver-builder/)
* **DASHBOARD GUTENBERG / DASHBOARD TEMPLATES**: This Addon allows you to build a Dashboard with Gutenberg. You can create a new Dashboard under 'Dashboard Templates' and set a default template below. I will be extending this to allow different templates for different roles / groups. This release also includes a Gutenberg block for the shortcodes.
* **ADMIN TOOLBAR**: This Addon allows you to tweak the admin toolbar and footer.
* More to come ...


= Localization =
* English 
* German

[A plugin from Alex @ portalZINE NMN - Development meets Creativity - portalzine.de](https://portalzine.de/)

= Feedback =
* I am open for your suggestions and feedback - Thank you for using or trying out one of my plugins!
* Drop me a line [@pztv](http://twitter.com/pztv) on Twitter
* Follow me on [my Facebook page](http://www.facebook.com/portalzine)
* Or send an Email to [ideas@cubicfusion.com](mailto:ideas@cubicfusion.com) ;-)

== Installation ==

1. Upload the entire folder to your `/wp-content/plugins/` directory via FTP, search for cubicFUSION Admin Enhancer under 'Plugins > Add New' or just upload the ZIP package via 'Plugins > Add New > Upload' in your WP Admin.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You will see a new option within the admin menu 'CF Admin Enhancer' :)
4. Make sure to go to the WordPress Dashboard once after activation. This will cache all the latest dashboard widgets. (Will be adding a background refresher in an upcoming release)
5. Now go, explore and enjoy :)

== Changelog ==

= 0.2.5 - 09.11.2020 =
Allow dashboard widgets with a Closure. Added Opis Closure which allows to serialize closures. 
This makes sure that dashboard widgets, like those from Rank Math work.

= 0.2.3 - 07.11.2020 =
Cleanup and reorganizing of the codebase

= 0.2.2 - 13.08.2020 =
Some minor bug fixes and internal check for WordPress 5.5

= 0.2.1 - 26.05.2020 =
Technical fix for dashboard widgets that use closures as a callback. This fix prevents the serialization of these callbacks. This is a PHP limitation that can be solved, but will wait and see how the demand for such a feature is. 

= 0.2 - 23.05.2020 =
1. **Updated: Shortcodes** (0.2) - Cleanup & Copy to Clipboard added.
2. **New: Dashboard Gutenberg / Dashboard Templates** (0.1) - This Addon allows you to build a Dashboard with Gutenberg. You can create a new Dashboard under 'Dashboard Templates' and set a default template below. I will be extending this to allow different templates for different roles / groups. This release also includes a Gutenberg block for the shortcodes.
3. **New: Admin Toolbar** (0.1) - This Addon allows you to tweak the admin toolbar and footer.

= 0.1 - 08.05.2020 =
1. **Initial Release** - Admin Enhancer is a work in progress.I am using this WordPress plugin to centralise things I love & need, when sending out a finished website or project.
These tools are completely free and will always stay free.
2. **Shortcode Addon (0.1)** - All dashboard widgets are converted to simple shortcodes. You can use those shortcodes within Elementor Pro or any other page builder that allows you to create custom admin dashboards. Makes it easy to build white-label dashboards, while still reusing all those nice dashboard widgets.

== Upgrade Notice ==

== Screenshots ==
1. Main administration
2. Dashboard Widgets to Shortcodes: Introduction
3. Dashboard Widgets to Shortcodes: List of shortcodes
4. Dashboard Gutenberg
5. Dashboard Gutenberg
6. Dashboard Templates
7. Dashboard Templates: Create
8. Dashboard Templates: Create
9. Dashboard Templates: Gutenberg Block
10. Dashboard Templates: Gutenberg Block
11. Welcome Dashboard 1
12. Welcome Dashboard 2

== Additional Info ==
**Idea Behind / Philosophy:** cubicFUSION is my personal playground. I planned to make many of my projects public, but time is limited and running customer projects always a priority.
  
I am still doing a big cleanup of my toolset and will see what I can actually share or reuse. Some of these might be useful, inspiration or just an archive of broken ideas  ;)  
  
Enjoy!
