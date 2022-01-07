Simple Image Gallery
====================

Simple Image Gallery (by JoomlaWorks) is truly the simplest and most effective way to add image galleries into your Joomla content and a classic must-have extension for any Joomla based website.

### WHY SIMPLE IMAGE GALLERY?
Adding image galleries inside your Joomla articles, K2 items, Virtuemart/HikaShop products or any other component that supports "content" plugins is dead-simple. The plugin can turn any folder of images located inside your Joomla website into a grid-style image gallery with lightbox/modal previews. And all that using a simple plugin tag like `{gallery}myphotos{/gallery}` or `{gallery}some/folder/myphotos{/gallery}`.

So for example, if we have a folder of images called "my_trip_to_Paris" and located in images/my_trip_to_Paris, then we can create our gallery by simply entering the tag `{gallery}my_trip_to_Paris{/gallery}` into some Joomla article.

The galleries created are presented in a grid of thumbnails. When your site visitors click on a thumbnail, they see the original (source) image in a lightbox/modal popup. The thumbnails are generated and cached using PHP for better results.

The plugin is ideal for any type of website: from personal ones (where you'd post a photo gallery from your last summer vacation), to e-shops (for product presentation) to large news portals. With Simple Image Gallery, you can have as many galleries as you want inside your content.

### FEATURES
So let's briefly see what are the main advantages of using Simple Image Gallery:
- You don't need to have an additional gallery component to display a few images and thus you don't need to tell your visitors "to see photos of XYZ click here".
- You can place one or more image galleries anywhere within your content giving you total layout freedom.
- The gallery layout is fluid by default which means it'll fit both responsive and adaptive website layouts.
- You can set a "root folder" in the plugin's parameters if you have all your gallery folders under a single long path in your Joomla site (e.g. images/content/galleries). This way you won't have to type the full path to each gallery folder inside the `{gallery}...{/gallery}` plugin tags but just the gallery folder name. The default "root folder" in the plugin's parameters is "images" because that's the default folder for uploading media files in Joomla as well.
- You can use MVC overrides to change how the thumbnail grid looks in your site. Simply copy the folder `/plugin/jw_sig/tmpl/Classic` into `/templates/YOUR_TEMPLATE/html/jw_sig/` so that you can access its files in `/templates/YOUR_TEMPLATE/html/jw_sig/Classic/`. You can then modify both the HTML and CSS code included there to match your template's design and colors.
- Uses the core Joomla updater.
- Uses Fancybox 3 for the lightbox/modal previews.
- Allows printing the image gallery grid when using the print preview feature available in most Joomla components (including the default article system and K2).
- Supports JPEG, PNG, GIF and WEBP as source images.

### DEMO
You can see a demo of the plugin here: [https://demo.joomlaworks.net/simple-image-gallery](https://demo.joomlaworks.net/simple-image-gallery)

### LEARN MORE
Visit the Simple Image Gallery product page at: [https://www.joomlaworks.net/simple-image-gallery](https://www.joomlaworks.net/simple-image-gallery)

### COMPATIBILITY & LICENSE
Simple Image Gallery is PHP 5, PHP 7 & PHP 8 compatible and supports Joomla versions 1.5, 2.5, 3.x and 4.x.

Joomla 1.5 must have the "Mootools Upgrade" system plugin enabled to avoid JavaScript conflicts between Mootools and newer jQuery releases used by the plugin.

Simple Image Gallery is a Joomla plugin developed by JoomlaWorks, released under the GNU General Public License.
