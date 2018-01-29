/**
 * @version      3.6.0
 * @package      Simple Image Gallery (plugin)
 * @author       JoomlaWorks - http://www.joomlaworks.net
 * @copyright    Copyright (c) 2006 - 2018 JoomlaWorks Ltd. All rights reserved.
 * @license      GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

var SIGHelper = {
    ieBrowserDetect: function() {
        var IEVersions = [6, 7, 8, 9];
        for (var i = 0; i < IEVersions.length; i++) {
            if (navigator.userAgent.toLowerCase().indexOf('msie ' + IEVersions[i]) != -1) {
                document.getElementsByTagName("body")[0].className += ' sigFreeIsIE' + IEVersions[i];
            }
        }
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

SIGHelper.loader(SIGHelper.ieBrowserDetect);
