/**
 * @version      3.5.0
 * @package      Simple Image Gallery (plugin)
 * @author       JoomlaWorks - http://www.joomlaworks.net
 * @copyright    Copyright (c) 2006 - 2017 JoomlaWorks Ltd. All rights reserved.
 * @license      GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

var SIGHelper = {
    ieBrowserDetect: function() {
        if (!document.getElementsByTagName) return false;
        if (!document.getElementById) return false;

        var bodyClass = document.getElementsByTagName("body")[0].className;

        var isIE6 = navigator.userAgent.toLowerCase().indexOf('msie 6') != -1;
        var isIE7 = navigator.userAgent.toLowerCase().indexOf('msie 7') != -1;
        var isIE8 = navigator.userAgent.toLowerCase().indexOf('msie 8') != -1;

        if (isIE6) document.getElementsByTagName("body")[0].className = bodyClass + ' sigFreeIsIE6';
        if (isIE7) document.getElementsByTagName("body")[0].className = bodyClass + ' sigFreeIsIE7';
        if (isIE8) document.getElementsByTagName("body")[0].className = bodyClass + ' sigFreeIsIE8';
    },
    loader: function(func) {
        var oldonload = window.onload;
        if (typeof window.onload != 'function') {
            window.onload = func;
        } else {
            window.onload = function() {
                if (oldonload) {
                    oldonload();
                }
                func();
            }
        }
    }
};

// Initiate
SIGHelper.loader(SIGHelper.ieBrowserDetect);
