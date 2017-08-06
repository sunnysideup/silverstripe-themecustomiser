<?php

/**
 * This is the modeladmin for the customer requests.
 */
class ThemeCustomiserAdmin extends ModelAdmin
{
    private static $url_segment = 'theme';

    private static $menu_title = 'Design';

    private static $managed_models = array('ThemeCustomisation');

    // private static $menu_priority = 300;

    private static $menu_icon = 'themecustomisation/images/treeicons/ThemeCustomiserAdmin-file.gif';

}
