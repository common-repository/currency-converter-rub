=== Currency Converter Rub ===
Contributors: PahaW
Donate link: http://paha.khspu.ru/blog
Tags: currency, converter, ruble
Requires at least: 5.4
Tested up to: 5.4.1
Stable tag: 1.5.0

This widget displays the Russian ruble, according to central bank (cbr).

== Description ==

This widget displays the Russian ruble, according to central bank (cbr).
The list of currencies of such countries as GBP, CNY, JPY, KRW, EUR, USD.
It is possible to insert a caption to this table, currencies, enable and disable the display of the desired currencies of the world.
Enable and disable the output tables of currencies, if cbr does not work.
It is possible to choose a set of images of flags of the world: large, medium, small.


== Installation ==

1. Unzip the downloaded package and upload the 'currency-converter-rub' folder into the 'wp-content/plugins/' directory, directory 'currency-converter-rub' must be writeable
1. Log into your WordPress admin panel
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Be sure to check the widget page (available from the 'Appearance' > Widgets menu)
1. Add widget 'Currency Converter Rub' into your sidebar

== Usage ==

[ccr amount = 1 from = USD]

== Frequently Asked Questions ==

= How do I customize CSS tags for plugin? =

Edit the `currency-converter-rub.css` file in plugin directory:

* .kurs td (td container)
* .kurs th (th container)
* .kurs caption (caption container)
* #cvet (td tag for images)
* input.input (list fields)
* input.input2 (edit fileds)

= How to add a shortcode to the page? =

* amount - it count
* from - currency
* [ccr amount = 1 from = USD]

== Screenshots ==

1. Currency Converter Ruble Widget Frontend User Interface
2. Currency Converter Ruble Widget Post edit screen
3. Currency Converter Ruble Widget Post Online Convert

== Changelog ==

= 1.5.0 =
* Add support php 7
* Fix some bugs
* Fix readme.txt

= 1.4.2 =
* Fix cache
* Fix readme.txt

= 1.4.0 =
* Fix cbr parsing
* Fix readme.txt

= 1.3.9 =
* Fix function timezone (sorry:))
* Fix day (j=1, d=01), time update 01/02/2013
* Fix readme.txt

= 1.3.7 =
* Fix online convert CH
* Add timezone
* Add shortcode
* Add cache for file parsing
* Add time update  (3 hours)
* Add Swiss Franc
* Update language file
* Fix readme.txt

= 1.2.5 =
* Fix online convert JP
* Fix readme.txt

= 1.2.4 =
* Fix english languages
* Fix bug online convert in js file
* Fix readme.txt

= 1.2.3 =
* Add online convert
* Fix readme.txt

= 1.2.2 =
* Add support english languages
* Fix readme.txt

= 1.2.1 =
* Edit readme.txt

= 1.2.0 =
* Added three types of images: large, medium, small

= 1.1.0 =
* Add field text.

== Upgrade Notice ==

= 1.0.0 is the initial release =

== Support ==

I will do my best to correct any reported defects as soon as I can make time, but please understand that this is side work. That said, I also use this plugin and am keen to ensure it provides the intended functionality. As to requests for enhancements, feel free to make these. I'll do my best to respond to your requests and, for those requests that I feel would benefit the majority of users, I'll get them on the enhancement list. I can't say just how quickly these would be implemented but funding the request would definitely move it up in the queue.
If you have some of the questions or problems contact me on the [Currency Converter Ruble](http://paha.khspu.ru/blog/ "Currency Converter Ruble Pages")
