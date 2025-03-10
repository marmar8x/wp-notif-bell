=== Notif Bell ===
Contributors: marmar8x
Tags: notification, notification bell, notif bell
Requires at least: 5.0
Tested up to: 6.7.1
Stable tag: 1.0.1
Requires PHP: 7.4
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight and flexible environment for implementing notification bells!

== Description ==

A plugin for implementing user notifications on a WordPress site! A completely flexible plugin with the possibility of arbitrary implementation and completely light and clean coding.

In terms of implementation, this plugin needs to be implemented by the programmer and you cannot use the plugin just by installing this plugin unless it has already been implemented by another theme or plugin.

The capabilities and sending pages, as well as notification management, exist in the WordPress admin section, but you need to display the received notifications for each user to themselves. For this, you need to know a bit of coding and implement the menu using the functions of this plugin along with some styling.

Docs: [https://github.com/marmar8x/wp-notif-bell/wiki](https://github.com/marmar8x/wp-notif-bell/wiki)


* Lightweight and flexible coding
* All features of pagination and page layout
* Ajax active for management
* Compact and easy to use
* Completely free

== Asked Questions ==

= How do I use the plugin? =

You are divided into two groups:

1- The group that is looking for a notification bell plugin to set up the notification section for the users of their website, and
2- The group that wants a miraculous display of a list of notifications with the current theme style for their users after installing the plugin.

For the first group, with a simple review of the behavior of the wpnb notification functions, you only need one style and everything else is ready. For the second group, this plugin cannot be implemented without initial implementation by yourself and coding.

== Screenshots ==

1. List of notifications
2. Send notification
3. Recipients
4. Writing content by text-magic
5. Settings

== Installation ==

1. Upload `notif-bell` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Upgrade Notice ==

= 1.0.0 =
XSS bug fixed, Upgrade immediately!

= 0.9.9 =
The XSS security bug fixed in this version. Upgrade immediately.

= 0.9.6 =
This version is a stable and basic version of the plugin.

== Changelog ==

= 1.0.1 =
* Fix element render bug in settings fields.
* Add Eye module settings fields. [binary/comma list]
* Add new `Eye` module for manage Seen notifs list.
* Change Collector->Observer based on new Eye module.
* Add "Reset" tool page.
* New helper: `Notif::get_max_id()`.
* Collector new method: `get_results()`, `target_by_tags(array $tags), __clone()`.
* Add rest api endpoints:
- `/wpnb/v1/notifs`                 `GET`                     **Getting all user notifications [own]**
- `/wpnb/v1/eye`                    `GET, POST, PUT, PATCH`   **Getting or Updating seen\unseen status**

= 1.0.0 =
* XSS bug fixed.
* Notif content not filtering based on format.

= 0.9.9 =
* Security: fix XSS bug in formatter and send page preview.
* Improve debugging logs.

= 0.9.8 =
* Fix versions: not any changes.

= 0.9.7 =
* Sync with wordpress `6.7`.

= 0.9.6 =
* Improve `Notification` instance.
* Fix some plugin issues.
