<?php

/**
 * moziloCMS Plugin: anythingSlider
 *
 * Does something awesome!
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_MoziloPlugins
 * @author   HPdesigner <mail@devmount.de>
 * @license  GPL v3
 * @version  GIT: v1.0.2013-11-01
 * @link     https://github.com/devmount/anythingSlider
 * @link     http://devmount.de/Develop/Mozilo%20Plugins/anythingSlider.html
 * @see      What good is it for a man to gain the whole world, yet forfeit his soul?
 *           – The Bible
 *
 * Plugin created by DEVMOUNT
 * www.devmount.de
 *
 */

// only allow moziloCMS environment
if (!defined('IS_CMS')) {
    die();
}

/**
 * anythingSlider Class
 *
 * @category PHP
 * @package  PHP_MoziloPlugins
 * @author   HPdesigner <mail@devmount.de>
 * @license  GPL v3
 * @link     https://github.com/devmount/anythingSlider
 */
class anythingSlider extends Plugin
{
    // language
    private $_admin_lang;
    private $_cms_lang;

    // plugin information
    const PLUGIN_AUTHOR  = 'HPdesigner';
    const PLUGIN_DOCU
        = 'http://devmount.de/Develop/Mozilo%20Plugins/anythingSlider.html';
    const PLUGIN_TITLE   = 'anythingSlider';
    const PLUGIN_VERSION = 'v1.0.2013-11-01';
    const MOZILO_VERSION = '2.0';
    private $_plugin_tags = array(
        'tag1' => '{anythingSlider|id|config|content}',
    );

    const LOGO_URL = 'http://media.devmount.de/logo_pluginconf.png';

    /**
     * set configuration elements, their default values and their configuration
     * parameters
     *
     * @var array $_confdefault
     *      text     => default, type, maxlength, size, regex
     *      textarea => default, type, cols, rows, regex
     *      password => default, type, maxlength, size, regex, saveasmd5
     *      check    => default, type
     *      radio    => default, type, descriptions
     *      select   => default, type, descriptions, multiselect
     */
    private $_confdefault = array(
        'width' => array(
            '',
            'text',
            '',
            '5',
            "/^[0-9]{0,1000}$/",
        ),
        'height' => array(
            '',
            'text',
            '',
            '5',
            "/^[0-9]{0,1000}$/",
        ),
    );

    /**
     * creates plugin content
     *
     * @param string $value Parameter divided by '|'
     *
     * @return string HTML output
     */
    function getContent($value)
    {
        global $CMS_CONF;
        global $syntax;

        $this->_cms_lang = new Language(
            $this->PLUGIN_SELF_DIR
            . 'lang/cms_language_'
            . $CMS_CONF->get('cmslanguage')
            . '.txt'
        );

        // set slider path for core files
        $path = $this->PLUGIN_SELF_URL . 'core/';

        // get params
        $values = explode('|', $value);

        // id for current anythingslider
        $param_id = rawurlencode(trim($values[0]));
        $param_id = str_replace(' ','',rawurldecode($param_id));
        // slider configuration
        $param_config = trim(str_replace('-html_br~', ' ', $values[1]));
        // slider content = rest array = single slides
        $param_content = array_slice($values, 2);

        // get theme
        $theme = '';
        $all_configs = explode(',',$param_config);
        foreach ($all_configs as $single_config) {
            $sconfig = explode(':',$single_config);
            if (trim($sconfig[0]) == 'theme') {
                $theme = str_replace('"','',trim($sconfig[1]));
            }
        }

        // get conf and set default
        $conf = array();
        foreach ($this->_confdefault as $elem => $default) {
            $conf[$elem] = ($this->settings->get($elem) == '')
                ? $default[0]
                : $this->settings->get($elem);
        }

        // build styles
        $set_styles = ($conf['width'] != '' or $conf['height'] != '');
        $width_style = ($conf['width'] != '')
            ? 'width: ' . $conf['width'] . 'px;'
            : '';
        $height_style = ($conf['height'] != '')
            ? 'height: ' . $conf['height'] . 'px;'
            : '';

        // jQuery (required)
        $syntax->insert_jquery_in_head('jquery');

        // Optional plugins
        $syntax->insert_in_head(
            '<script
                type="text/javascript"
                src="'. $path . 'js/jquery.easing.1.2.js"
            ></script>'
        );
        $syntax->insert_in_head(
            '<script
                type="text/javascript"
                src="'. $path . 'js/swfobject.js"
            ></script>'
        );
        // Anything Slider
        $syntax->insert_in_head(
            '<link
                rel="stylesheet"
                href="'. $path . 'css/anythingslider.css"
                type="text/css"
                media="screen"
            />'
        );
        $syntax->insert_in_head(
            '<script
                type="text/javascript"
                src="'. $path . 'js/jquery.anythingslider.min.js"
            ></script>'
        );
        // used stylesheet
        $syntax->insert_in_head(
            '<link
                rel="stylesheet"
                href="'. $path . 'css/theme-' . $theme . '.css"
                type="text/css"
                media="screen"
            />'
        );
        // AnythingSlider optional extensions
        $syntax->insert_in_head(
            '<script
                type="text/javascript"
                src="'. $path . 'js/jquery.anythingslider.fx.min.js"
            ></script>'
        );
        $syntax->insert_in_head(
            '<script
                type="text/javascript"
                src="'. $path . 'js/jquery.anythingslider.video.min.js"
            ></script>'
        );

        // Initializing Slider with configuration
        if ($param_config != '') {
            $param_config = '{' . $param_config . '}';
        }
        $syntax->insert_in_head('
            <script type="text/javascript">
                $(function(){
                    $("#' . $param_id . '").anythingSlider(' . $param_config . ');
                });
            </script>
        ');

        if ($set_styles) $syntax->insert_in_head('
            <style type="text/css">
                #' . $param_id . ' {' .
                    $width_style .
                    $height_style .
                '}
            </style>
        ');

        // initialize return content, begin plugin content
        $content = '<!-- BEGIN ' . self::PLUGIN_TITLE . ' plugin content --> ';

        $content .= '<div id="' . $param_id . '">';
        foreach ($param_content as $item) {
            $content .= '<div>' . trim($item) . '</div>';
        }
        $content .= '</div>';

        // end plugin content
        $content .= '<!-- END ' . self::PLUGIN_TITLE . ' plugin content --> ';

        return $content;
    }


    function getConfig() {
        $config = array();

        // fixed width of slider
        $config['width']  = array(
            'type' => 'text',
            'description' => $this->admin_lang->getLanguageValue('config_width'),
            'maxlength' => '100',
            'size' => '5',
            'regex' => "/^[0-9]{0,1000}$/",
            'regex_error' => $this->admin_lang->getLanguageValue('config_width_error')
        );

        // fixed height of slider
        $config['height']  = array(
            'type' => 'text',
            'description' => $this->admin_lang->getLanguageValue('config_height'),
            'maxlength' => '100',
            'size' => '5',
            'regex' => "/^[0-9]{0,1000}$/",
            'regex_error' => $this->admin_lang->getLanguageValue('config_height_error')
        );

        return $config;
    }


    function getInfo() {
        global $ADMIN_CONF;

        $this->admin_lang = new Language(PLUGIN_DIR_REL."anythingSlider/lang/admin_language_".$ADMIN_CONF->get("language").".txt");

        $info = array(
            // Plugin-Name + Version
            '<b>anythingSlider</b> v1.0.2013-11-01',
            // moziloCMS-Version
            '2.0',
            // Kurzbeschreibung nur <span> und <br /> sind erlaubt
            $this->admin_lang->getLanguageValue('description'),
            // Name des Autors
            'HPdesigner',
            // Docu-URL
            'http://www.devmount.de/Develop/Mozilo%20Plugins/anythingSlider.html',
            // Platzhalter für die Selectbox in der Editieransicht
            // - ist das Array leer, erscheint das Plugin nicht in der Selectbox
            array(
                '{anythingSlider|id|config|content}' => $this->admin_lang->getLanguageValue('placeholder')
            )
        );
        return $info;
    }
}

?>