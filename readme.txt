=== Meta Collections ===
Contributors: bastiaaaan

Tags: collection, collection management, post type, custom taxonomy, custom post type, register_post_type, custom fields, custom, taxonomy, edit screen, vimeo API, georeference
Requires at least: 3.0
Tested up to: 3.8
Version: 2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl.html
Stable tag: 2.0
Use Wordpress as a Collection Manager. Customize Wordpress' post edit screen by creating your own metadata schema's.

== Description ==
<h4>Meta Collections</h4>
Meta Collections is developed to turn Wordpress into a Collection manager.
Beside 'Posts' and 'Pages' you can add your own post type (a Collection e.g. 'Films'). 
For that particular post type you can add you own Metadata schema. So for example, if you want a Dublin Core metadata schema you can.
That schema can be a mixture of system and custom fields or a completely customized metadataschema.
On top of that you can intuitively compose the user interface for the post type by dragging and dropping metafields in the right places using neat metaboxes.
 
Fields can be validated and can consist of multiple instances (e.g. more dan one ingredient field).
You can also add metafields in the overview table. 

<h4>Fields types</h4>

* Text
* Textarea
* Wysiwyg (Wordpress' own Editor)
* Vimeo API connector (copies vimeo's preview images and other metadata into wordpress, configure embedding options)
* ColorPicker (jQuery UI Colorpicker with hsb, rgb, lab, cmyk, hex colortype and transparancy support)   
* Date (jQuery UI datepicker)
* Datetime (jQuery Mobiscroll)
* Image 
* Georeference (Using google maps API for displaying a map and (reverse) geocode)
* True / False (checkbox with text comment)
* Taxonomy (adds a 'Wordpress own' taxonomy to your post type)
* Select (for selecting one or multiple values in a dropdownmenu)
* Radio (for multiple values in a radio setup)


Future (shortly available)
* Youtube API field
* File
* multiple checkboxes
* radio
* ask/request!


<h4>Shortcode</h4>
Shorcodes is still quity simple since this plugin is brand new (september 2012)
you can add the follow shorcode in your posts description in order to use the fields
<code> [collections metafield="identifier" instance="all" seperator=" - "]</code>

The shorcode analyzed:
1. 'collections' to identify the shortcode for the collections plugin. alwya begin the shorcode with
1. 'metafield= metafieldID' you can look a metafieldID up by editing a metadataset and then a metadatafield. 
1. 'instance = number'  to enable use of multiple values in a metafield. use 0 for the value for instance 0 or 1 for instance 1. Default if variable is not used is 0.
Use 'all' to return all the values.
1. 'seperator = 'number' if you use 'All'for 'instance' then you can define a 'seperator' which seperate the values.

<h4>Localized interface</h4>
The plugin is published in a language supporing two languages, English and Dutch. Feel free to translate it to another language.


== Installation ==
1. Upload the plugin folder` to the `/wp-content/plugins/` directory 
1. ..or just download it trough the wordpress new plugin interface searching for 'meta collections'
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a Collection
1. Create a Metadata Schema
1. Compose the Collection's User Interface
1. Start managing your collection
1. Build your collection details into your template or use shortcode


== Frequently Asked Questions ==

= I added a metafield but it doesn't show up in the post type edit screen =

You first have to drag it into the user interface. Go to Collections, click on 'edit user interface' and drag the field in a place you wan't it  


== Screenshots ==

1. Edit screen Collections
2. Metadataschema
3. Edit Screen Collections
4. Compose your user Interface
5. Start managing your collection
6. The new Vimeo field

== Changelog ==

= 1.0 =
* This is the first release.

= 1.0.1 = 
* Some issues fixed for php 5.2 compatibility
* fixed some minor bugs

= 1.0.2 = 
* Fixed path errors for metadatafields
* Number of fatal errors fixes
* Bug fixed in save_metabox() name metabox saved properly now
* Added functionality to order fields in the table overview
* Tags and Category support for Collections
* Empty container 'class' caused the drop field in 'Side' to be to short
* Changes all old names 'collections' to 'metacollections' 

= 1.0.3 = 
* Fixed compatibility issues with Wordpress 3.5
* Fixed javascript bug
* Fixed true false field error
* Fixed and bug in georeference field.
* Added field type: Vimeo API field which enables users to add vimeo video with a single video id.
* Added field type: radio-buttons field.

= 2.0. =
* Wordpress 3.8 compatible (not tested yet on downwards compatibility)
* Fixed compatibility issues with Wordpress 3.8
* Fixed google maps, javascript loads without a specific api key
* Added local javascript libraries instead of using my own.
* Fixed numerous bugs 
* Bug drag and drop function in the user interface edit screen
* missing collections.css 
* added renewed custom vimeo field. communicates trought the Vimeo Api 
 

== Upgrade Notice ==
= 1.0 =
* This is the first release so no notices.

= 1.0.1 = 
* Some issues fixed for php 5.2 compatibility

= 1.0.2 = 
* Fixed path errors for metadatafields
* Number of fatal errors fixes
* Bug fixed in save_metabox() name metabox saved properly now
* Added functionality to order fields in the table overview
* Tags and Category support for Collections
* Empty container 'class' caused the drop field in 'Side' to be to short
* Changes all old names 'collections' to 'metacollections' 

= 1.0.3 = 
* Fixed compatibility issues with Wordpress 3.5
* Fixed javascript bug
* Fixed true false field error
* Fixed and bug in georeference field.
* Added field type: Vimeo API field which enables users to add vimeo video with a single video id.
* Added field type: radio-buttons field.
 
= 2.0. =
* Wordpress 3.8 compatible (not tested yet on downwards compatibility)

