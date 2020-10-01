# Admin Color Schemer #
Contributors: wordpressdotorg, helen, markjaquith  
Requires at least: 3.8  
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html  
Stable tag: 1.1  

Create your own admin color scheme, right in the WordPress admin under the Tools menu.

## Description ##

Create your own admin color scheme, right in the WordPress admin under the Tools menu. Go simple with the four basic colors, or get into the details and customize to your heart's content.

### Contributing ###

Pull requests and issues on [GitHub](https://github.com/helen/admin-color-schemer) welcome.

## Installation ##

Admin Color Schemer is most easily installed automatically via the Plugins tab in your dashboard. Your uploads folder also needs to be writeable in order to save the scheme.

## Frequently Asked Questions ##

### Why do I have to click a button to preview? ###

The preview currently operates by generating a complete stylesheet and reloading it. While in some environments this happens near-instantaneously, the time and resources it takes to reflect a change are not ideal for live previews. We do hope to solve this in a later release.

### I'm getting an ugly screen asking me for a username and password. ###

This means that your uploads folder requires credentials in order to save the the scheme files. If you're not sure how to fix that, please try the [support forums](http://wordpress.org/support/). We'll also work on making this prompt more beautiful in the future.

### What should I do if I've updated this plugin or WordPress recently and now my color scheme doesn't look right? ###
Some versions of WordPress or this plugin may change how the custom color scheme output is generated, so if you see something funny, first try re-saving your custom color scheme. If it's still broken, then please let us know.

## Screenshots ##

1. Admin color schemer in action

## Changelog ##

### 1.1 ###
* Updated phpsass library to fix PHP 7 bug (with many thanks to @KZeni).
* Ensure custom color scheme can be previewed when using the default admin color scheme.
* Avoid a potential PHP notice.

### 1.0 ###
* Initial release
