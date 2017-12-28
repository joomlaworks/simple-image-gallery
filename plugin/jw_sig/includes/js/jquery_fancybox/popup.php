<?php
/**
 * @version      3.5.0
 * @package      Simple Image Gallery (plugin)
 * @author       JoomlaWorks - http://www.joomlaworks.net
 * @copyright    Copyright (c) 2006 - 2017 JoomlaWorks Ltd. All rights reserved.
 * @license      GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$relName = 'fancybox-button';
$extraClass = 'fancybox-button';

$stylesheets = array(
    'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css'
);
$stylesheetDeclarations = array();
$scripts = array(
    'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js'
);

if(!defined('PE_FANCYBOX_LOADED')){
    define('PE_FANCYBOX_LOADED', true);
    $scriptDeclarations = array("
        (function($) {
            $(document).ready(function() {
                $('a.fancybox-button').fancybox({
                    buttons : [
                        'slideShow',
                        'fullScreen',
                        //'thumbs',
                        'share',
                        'download',
                        //'zoom',
                        'close'
                    ],
                    beforeShow : function(instance, current) {
                        if (current.type === 'image') {
                            var title = current.opts.\$orig.attr('title');
                            current.opts.caption = (title.length ? '<b class=\"fancyboxCounter\">".JText::_('JW_PLG_SIG_FB_IMAGE')." ' + (current.index + 1) + ' ".JText::_('JW_PLG_SIG_FB_OF')." ' + instance.group.length + '</b>' + ' | ' + title : '');
                        }
                    }
                });
            });
        })(jQuery);
    ");
} else {
    $scriptDeclarations = array();
}
