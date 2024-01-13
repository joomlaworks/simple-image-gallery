<?php
/**
 * @version      4.2
 * @package      Simple Image Gallery (plugin)
 * @author       JoomlaWorks - https://www.joomlaworks.net
 * @copyright    Copyright (c) 2006 - 2022 JoomlaWorks Ltd. All rights reserved.
 * @license      GNU/GPL license: https://www.gnu.org/licenses/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Uri\Uri as JUri;
use Joomla\CMS\Filesystem\Folder as JFolder;
use Joomla\CMS\Filesystem\File as JFile;
use Joomla\CMS\Object\CMSObject as JObject;
 
class SimpleImageGalleryHelper
{
    public $srcimgfolder;
    public $thb_width;
    public $thb_height;
    public $smartResize;
    public $jpg_quality;
    public $cache_expire_time;
    public $gal_id;
    public $format;
	
	public function read_image($original_file)	
	{
		$original_extension = strtolower(pathinfo($original_file, PATHINFO_EXTENSION));
		$exif_data = exif_read_data($original_file);
		$exif_orientation = $exif_data['Orientation'];
		// load the image
		if($original_extension == "jpg" or $original_extension == "jpeg"){
			$original_image = imagecreatefromjpeg($original_file);
		}
		if($original_extension == "gif"){
			$original_image = imagecreatefromgif($original_file);
		}
		if($original_extension == "png"){
			$original_image = imagecreatefrompng($original_file);
		}
		 if($exif_orientation=='3'  or $exif_orientation=='6' or $exif_orientation=='8'){		   
			$new_angle[3] = 180;
			$new_angle[6] = -90;
			$new_angle[8] = 90;
			imagesetinterpolation($original_image, IMG_MITCHELL);
			$rotated_image = imagerotate($original_image, $new_angle[$exif_orientation], 0);
			imagedestroy($original_image); 
		}else {
			$rotated_image  = $original_image;
		}	 
		return $rotated_image;
	}

    public function renderGallery()
    {
        // Initialize
        $srcimgfolder = $this->srcimgfolder;
        $thb_width = $this->thb_width;
        $thb_height = $this->thb_height;
        $smartResize = $this->smartResize;
        $jpg_quality = $this->jpg_quality;
        $cache_expire_time = $this->cache_expire_time;
        $gal_id = $this->gal_id;
        $format = $this->format;

        // API
        jimport('joomla.filesystem.folder');

        // Path assignment
        $sitePath = JPATH_SITE.'/';
        if ($format == 'feed') {
            $siteUrl = JUri::root(true).'';
        } else {
            $siteUrl = JUri::root(true).'/';
        }

        // Internal parameters
        $prefix = "jw_sig_cache_";

        // Set the cache folder
        //$cacheFolderPath = JPATH_SITE.'/cache/jw_sig';
        $cacheFolderPath = $sitePath.$srcimgfolder . '/jw_sig';
        $cacheFolderUrl = $siteUrl.$srcimgfolder . '/jw_sig';
        if (file_exists($cacheFolderPath) && is_dir($cacheFolderPath)) {
            // all OK
        } else {
            mkdir($cacheFolderPath);
        }

        // Check if the source folder exists and read it
        $srcFolder = JFolder::files($sitePath.$srcimgfolder);

        // Proceed if the folder is OK or fail silently
        if (!$srcFolder) {
            return;
        }

        // Loop through the source folder for images
        $fileTypes = array('gif', 'jpg', 'jpeg', 'png', 'webp');

        // Create an array of file types
        $found = array();

        // Create an array for matching files
        foreach ($srcFolder as $srcImage) {
            $fileInfo = pathinfo($srcImage);
            if (array_key_exists('extension', $fileInfo) && in_array(strtolower($fileInfo['extension']), $fileTypes)) {
                $found[] = $srcImage;
            }
        }

        // Bail out if there are no images found
        if (count($found) == 0) {
            return;
        }

        // Sort array
        sort($found);

        // Initiate array to hold gallery
        $gallery = array();

        // Loop through the image file list
        foreach ($found as $key => $filename) {

            // Determine thumb image filename
            if (strtolower(substr($filename, -4, 4)) == 'jpeg' || strtolower(substr($filename, -4, 4)) == 'webp') {
                $thumbfilename = substr($filename, 0, -4).'jpg';
            } elseif (strtolower(substr($filename, -3, 3)) == 'gif' || strtolower(substr($filename, -3, 3)) == 'jpg' || strtolower(substr($filename, -3, 3)) == 'png') {
                $thumbfilename = substr($filename, 0, -3).'jpg';
            }

            // Object to hold each image elements
            $gallery[$key] = new JObject;

            // Assign source image and path to a variable
            $original = $sitePath.$srcimgfolder.'/'.$filename;

            // Check if thumb image exists already
            $thumbimage = $cacheFolderPath.'/'.$prefix.$gal_id.'_'.strtolower($this->cleanThumbName($thumbfilename));

            if (file_exists($thumbimage) && is_readable($thumbimage) && (filemtime($thumbimage) + $cache_expire_time) > time()) {
                // Do nothing
            } else {
                // Otherwise create the thumb image

                // Begin by getting the details of the original
                list($originalwidth, $originalheight, $type) = getimagesize($original);

                // Create an image resource for the original
                switch ($type) {
                    case 1:    
                    case 2:
                    case 3:
      			    //take into account orientation see https://www.php.net/manual/en/function.exif-read-data.php#121742
					$source = $this->read_image($original);

                       break;
                    case 18:
                        // WEBP
                        if (version_compare(PHP_VERSION, '7.1.0', 'ge')) {
                            $source = imagecreatefromwebp($original);
                        } else {
                            $source = null;
                        }
                        break;
                    default:
                        $source = null;
                }



                // Bail out if the image resource is not OK
                if (!$source) {
                    if (version_compare(JVERSION, '4', 'ge')) {
                        $app = JFactory::getApplication();
                        $app->enqueueMessage(JText::_('JW_PLG_SIG_ERROR_SRC_IMGS'), 'notice');
                    } else {
                        JError::raiseNotice('', JText::_('JW_PLG_SIG_ERROR_SRC_IMGS'));
                    }
                    return;
                }
			  $width  = imagesx($source);
			  $height = imagesy($source);
                // Calculate thumbnails
                $thumbnail = $this->thumbDimCalc($width, $height, ($thb_width * $width)/$originalwidth, ($thb_height * $height) /$originalheight, $smartResize);

                $thumb_width = $thumbnail['width'];
                $thumb_height = $thumbnail['height'];

                // Create an image resource for the thumbnail
                $thumb = imagecreatetruecolor($thumb_width, $thumb_height);

                // Create the resized copy
                imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);

                // Convert and save all thumbs to .jpg
                $success = imagejpeg($thumb, $thumbimage, $jpg_quality);

                // Bail out if there is a problem in the GD conversion
                if (!$success) {
                    return;
                }

                // Remove the image resources from memory
                imagedestroy($source);
                imagedestroy($thumb);
            }

            // Assemble the image elements
            $gallery[$key]->filename = $filename;
            $gallery[$key]->sourceImageFilePath = $siteUrl.$srcimgfolder.'/'.$this->replaceWhiteSpace($filename);
            //$gallery[$key]->thumbImageFilePath = $siteUrl.'cache/jw_sig/'.$prefix.$gal_id.'_'.strtolower($this->cleanThumbName($thumbfilename));
            $gallery[$key]->thumbImageFilePath = $cacheFolderUrl.'/' . $prefix.$gal_id.'_'.strtolower($this->cleanThumbName($thumbfilename));
            $gallery[$key]->width = $thb_width;
            $gallery[$key]->height = $thb_height;
        }

        return $gallery;
    }



    /* ------------------ Helper Functions ------------------ */

    // Calculate thumbnail dimensions
    private function thumbDimCalc($width, $height, $thb_width, $thb_height, $smartResize)
    {
        if ($smartResize) {
            // thumb ratio bigger that container ratio
            if ($width / $height > $thb_width / $thb_height) {
                // wide containers
                if ($thb_width >= $thb_height) {
                    // wide thumbs
                    if ($width > $height) {
                        $thumb_width = $thb_height * $width / $height;
                        $thumb_height = $thb_height;
                    }
                    // high thumbs
                    else {
                        $thumb_width = $thb_height * $width / $height;
                        $thumb_height = $thb_height;
                    }
                    // high containers
                } else {
                    // wide thumbs
                    if ($width > $height) {
                        $thumb_width = $thb_height * $width / $height;
                        $thumb_height = $thb_height;
                    }
                    // high thumbs
                    else {
                        $thumb_width = $thb_height * $width / $height;
                        $thumb_height = $thb_height;
                    }
                }
            } else {
                // wide containers
                if ($thb_width >= $thb_height) {
                    // wide thumbs
                    if ($width > $height) {
                        $thumb_width = $thb_width;
                        $thumb_height = $thb_width * $height / $width;
                    }
                    // high thumbs
                    else {
                        $thumb_width = $thb_width;
                        $thumb_height = $thb_width * $height / $width;
                    }
                    // high containers
                } else {
                    // wide thumbs
                    if ($width > $height) {
                        $thumb_width = $thb_height * $width / $height;
                        $thumb_height = $thb_height;
                    }
                    // high thumbs
                    else {
                        $thumb_width = $thb_width;
                        $thumb_height = $thb_width * $height / $width;
                    }
                }
            }
        } else {
            if ($width > $height) {
                $thumb_width = $thb_width;
                $thumb_height = $thb_width * $height / $width;
            } elseif ($width < $height) {
                $thumb_width = $thb_height * $width / $height;
                $thumb_height = $thb_height;
            } else {
                $thumb_width = $thb_width;
                $thumb_height = $thb_height;
            }
        }

        $thumbnail = array();
        $thumbnail['width'] = round($thumb_width);
        $thumbnail['height'] = round($thumb_height);

        return $thumbnail;
    }

    // Replace white space
    private function replaceWhiteSpace($text_to_parse)
    {
        $source_html = array(" ");
        $replacement_html = array("%20");
        return str_replace($source_html, $replacement_html, $text_to_parse);
    }

    // Cleanup thumbnail filenames
    private function cleanThumbName($text_to_parse)
    {
        $source_html = array(' ', ',');
        $replacement_html = array('_', '_');
        return str_replace($source_html, $replacement_html, $text_to_parse);
    }

    // Path overrides
    public function getTemplatePath($pluginName, $file, $tmpl)
    {
        $app = JFactory::getApplication();
        $template = $app->getTemplate();

        $p = new stdClass;

        if (file_exists(JPATH_SITE.'/templates/'.$template.'/html/'.$pluginName.'/'.$tmpl.'/'.$file)) {
            $p->file = JPATH_SITE.'/templates/'.$template.'/html/'.$pluginName.'/'.$tmpl.'/'.$file;
            $p->http = JUri::root(true)."/templates/".$template."/html/{$pluginName}/{$tmpl}/{$file}";
        } else {
            if (version_compare(JVERSION, '2.5.0', 'ge')) {
                // Joomla 2.5+
                $p->file = JPATH_SITE.'/plugins/content/'.$pluginName.'/'.$pluginName.'/tmpl/'.$tmpl.'/'.$file;
                $p->http = JUri::root(true)."/plugins/content/{$pluginName}/{$pluginName}/tmpl/{$tmpl}/{$file}";
            } else {
                // Joomla 1.5
                $p->file = JPATH_SITE.'/plugins/content/'.$pluginName.'/tmpl/'.$tmpl.'/'.$file;
                $p->http = JUri::root(true)."/plugins/content/{$pluginName}/tmpl/{$tmpl}/{$file}";
            }
        }
        return $p;
    }
}
