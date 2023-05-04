=== WP Advanced PDF ===

Contributors: CedCommerce
Donate link: https://cedcommerce.com
Tags: Wp advanced pdf, PDF, Post, CedCommerce, Bulk PDF, PDF Plugin, Convert, Export to PDF, Convert To PDF, Article To PDF, Print 
Requires at least: 5.0
Tested up to: 6.0
Stable tag: 1.1.7
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Create PDF from Posts & Pages

== Description ==

WP Advanced PDF is a pdf generator for posts to pages. This plugin enables your blog readers to create pdf of posts of your blog. WP Advanced PDF relies on the TCPDF class to render PDF.
This makes WP Advanced PDF a completely self contained PDF generating plugin.
This plugin has possibility to limit access to pdfs to registered users or present links to all visitors.

= Main features =
* **Feature to set Custom fonts for PDF**
* **Feature to set Stretching and Spacing for PDF**
* **Gives a pdf extract link into your blog and you blog reader can easily extract the blog on one click only**
* You can include header logo to pdf.
* **Can set custom bullet style**.
* **Can add watermark text and images to look PDF wonderful**.
* **Can customize fonts of header and content**.
* **Can easily change margins, alignment, and pages for your pdf**.
* Can set file name for pdf. Default file name of plugin is Post Id.
* Have ability to generate pdf from cache to reduce load from server and can schedule backup of cache if Your blog is not updating too frequently and many more options.
* Can add custom style to post contents
* Can set default blog title for pdf
* Easy to use and install
* Easy to configure
== Premium Feature Now In Free ==


= Enhanced Custom Font Support =

* Now fonts will be saved on wordpress upload directory instead of plugin's directory keeping plugin size fix and safe.

= WP ADVANCED PDF PRO FEATURES =

* To get premium plugin click [here](http://cedcommerce.com/wordpress-plugins/wp-advanced-pdf-pro)

With all your support and help we have successfully launched **premium version** of this plugin with lots of useful and handy features. Along with the features of WP Advanced PDF following are the main features included in premium version:

= Compatible with ACF =

* Advanced Custom Fields Plugin for WordPress

= META FIELD SUPPORT =
* Ability to choose meta fields for each post types to be display on PDF.
* Ability to provide name of the meta fields to overwrite default name.
* Ability to include custom meta fields per post wise into PDF. Which will then also be present on general pdf setting page for all posts of particular post type.

= CUSTOM SETTING =
* Created a **Default setting** to go live as soon as activating plugin, excluding the headache of doing those settings first.
* Apart from default setting you can also make your **Custom Settings** as much as you can. This way you will play with those settings easily.
* Admin can apply custom setting and can remove them as well.
* Admin can get preview PDF of selected setting configuration's.

= SHORTCODE FEATURE =
* Made a shortcode for PDF export button so that admin can place wherever he/she want. **[ptpdf]**
* If shortcode feature is activated then admin must have to use shortcode to show PDF export button.

= PDF EXPORT LOG =
* You can view log of exported PDFs.
* Can select what field to display in log like export date, post type etc through screen options.
* Can remove single log entry as well as can delete in bulk.
* Pagination is provided and log entries are listed by latest at top.

= MINOR CHANGES =
* Change layout of setting page for easy access.
* Changed icon of menu link

= DEMO LINKS =
* FrontEnd: click [here](http://demo.cedcommerce.com/wordpress/advanced-pdf/)
* BackEnd: click [here](http://demo.cedcommerce.com/wordpress/advanced-pdf/wp-admin/)


= Support =

If you need support or have any question then kindly use our online chat window [here](http://cedcommerce.com) or send us email at [support@cedcommerce.com](mailto:support@cedcommerce.com)

= Follow Us =

* **Our Official Website** - [http://cedcommerce.com/](http://cedcommerce.com/) 
* **Our Facebook Page** - [https://www.facebook.com/CedCommerce](https://www.facebook.com/CedCommerce)
* **Our Google+ Account** - [https://plus.google.com/u/0/118378364994508690262](https://plus.google.com/u/0/118378364994508690262)
* **Our Twitter Account** - [https://twitter.com/cedcommerce](https://twitter.com/cedcommerce)
* **Our LinkedIn Account** - [https://www.linkedin.com/company/cedcommerce](https://www.linkedin.com/company/cedcommerce)

== Installation ==

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install of WP Advanced PDF, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type **WP Advanced PDF** and click Search Plugins. Once you've found our WP Advanced PDF plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking "Install Now".

= Manual installation =

The manual installation method involves downloading our Product Auto Share plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Frequently Asked Questions ==

= How to change position buttons in content =

Go to the Settings page and change value for the 'Position of buttons in the content' field.

= Buttons for content do not appear on page =

Go to the Settings page and change value for the 'Display Options' or 'Availability' fields.

= Buttons for certain post type do not appear on page =

Go to the Settings page and Check options 'Allowed Post Types' field.

= How can I change the content pdf document ? =

In order to change main content of pdf/print document you can use following filter:

`add_filter( 'the_post_export_content', {your_function} );`

For example, add the following code to the 'functions.php' file of your theme:

`add_filter( 'the_post_export_content', custom_function );

function custom_function( $post_content ){
	$my_content = '<p>Adipisci ipsam quod odit eius et et voluptas corporis.</p>';
	$more_content = '<p> Velit repudiandae earum ducimus odit excepturi cum laboriosam.</p>';
	return $my_content . $more_content;
}`

= How I add different styles to pdf =

To add style in pdfs, Go to Body Section, check Custom CSS and enter the required styles in textarea.

== Screenshots ==

1. Setting page
2. Export In post edit screen
3. Admin bulk export 
4. Export option on front end
5. Popup for guest user

== Changelog ==

= 1.1.7 =
* Bug fix and design fix

= 1.1.6 =
* Bug fix

= 1.1.5 =
* Bug fix

= 1.1.4 =
* Fixed Bulk action export pdf issue.

= 1.1.3 =
* Added feature to send pdf in mail to users for selected user role whenever a post is published.

= 1.1.2 =
* Fixed the issue of using deprecated way of defining constructor

= 1.1.1 =
* Provide premium feature of font uploads in uploads directory wordpress 
* Provide new settings for header to choose the content of header from options like logo, Site name, Site description, Site URL 

= 1.1.0 =
* Fixed the issue of notice on admin page

= 1.0.9 =
* Removed the Empty div on frontend when frontend is not selected from admin
* Fixed the issue of notice on 404 page

= 1.0.8 =
* Fixed issue with edit or view links on pages in admin panel

= 1.0.7 =
* Fixed issue with uploads directory permission
* Fixed error on plugin activation

= 1.0.6 =
* Launched WP Advanced PDF PRO

= 1.0.5 =
* fixed issue with style and script in https enabled site.
* Added RTL support 

= 1.0.4 =
* Updated tcpdf library to 6.2.12.
**Added feature to set Custom fonts for PDF
**Added feature to set Stretching and Spacing for PDF

= 1.0.3 =
* Made Important changes.

= 1.0.2 =
* Added body wrapper to post contents to be printed.
* Can now remove footer from pdf if you want

= 1.0.1 =
* Fixed minor bugs related to admin settings(Javascript issue).

= 1.0.0 =
* This is initial version

== Upgrade Notice ==

= 1.1.4 =
* Fixed Bulk action export pdf issue.

= 1.1.5 =
* Bug fix

= 1.1.6 =
* Bug fix

= 1.1.7 =
* Bug fix

