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
 * @version  GIT: v1.1.2014-04-04
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
        = 'http://devmount.de/Develop/moziloCMS/Plugins/anythingSlider.html';
    const PLUGIN_TITLE   = 'anythingSlider';
    const PLUGIN_VERSION = 'v1.1.2014-04-04';
    const MOZILO_VERSION = '2.0';
    private $_plugin_tags = array(
        'tag1' => '{anythingSlider|<id>|<config>|<content>}',
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
        $param_id = str_replace(' ', '', rawurldecode($param_id));
        // slider configuration
        $param_config = trim(str_replace('-html_br~', ' ', $values[1]));
        // slider content = rest array = single slides
        $param_content = array_slice($values, 2);

        // get theme
        $theme = '';
        $all_configs = explode(',', $param_config);
        foreach ($all_configs as $single_config) {
            $sconfig = explode(':', $single_config);
            if (trim($sconfig[0]) == 'theme') {
                $theme = str_replace('"', '', trim($sconfig[1]));
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
        $syntax->insert_in_head(
            '<script type="text/javascript">
                $(function(){
                    $("#' . $param_id . '").anythingSlider(' . $param_config . ');
                });
            </script>'
        );

        if ($set_styles) {
            $syntax->insert_in_head(
                '<style type="text/css">
                    #' . $param_id . ' {' . $width_style . $height_style . '}
                </style>'
            );
        }

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


    /**
     * sets backend configuration elements and template
     *
     * @return Array configuration
     */
    function getConfig()
    {
        $config = array();

        // read configuration values
        foreach ($this->_confdefault as $key => $value) {
            // handle each form type
            switch ($value[1]) {
            case 'text':
                $config[$key] = $this->confText(
                    $this->_admin_lang->getLanguageValue('config_' . $key),
                    $value[2],
                    $value[3],
                    $value[4],
                    $this->_admin_lang->getLanguageValue(
                        'config_' . $key . '_error'
                    )
                );
                break;

            case 'textarea':
                $config[$key] = $this->confTextarea(
                    $this->_admin_lang->getLanguageValue('config_' . $key),
                    $value[2],
                    $value[3],
                    $value[4],
                    $this->_admin_lang->getLanguageValue(
                        'config_' . $key . '_error'
                    )
                );
                break;

            case 'password':
                $config[$key] = $this->confPassword(
                    $this->_admin_lang->getLanguageValue('config_' . $key),
                    $value[2],
                    $value[3],
                    $value[4],
                    $this->_admin_lang->getLanguageValue(
                        'config_' . $key . '_error'
                    ),
                    $value[5]
                );
                break;

            case 'check':
                $config[$key] = $this->confCheck(
                    $this->_admin_lang->getLanguageValue('config_' . $key)
                );
                break;

            case 'radio':
                $descriptions = array();
                foreach ($value[2] as $label) {
                    $descriptions[$label] = $this->_admin_lang->getLanguageValue(
                        'config_' . $key . '_' . $label
                    );
                }
                $config[$key] = $this->confRadio(
                    $this->_admin_lang->getLanguageValue('config_' . $key),
                    $descriptions
                );
                break;

            case 'select':
                $descriptions = array();
                foreach ($value[2] as $label) {
                    $descriptions[$label] = $this->_admin_lang->getLanguageValue(
                        'config_' . $key . '_' . $label
                    );
                }
                $config[$key] = $this->confSelect(
                    $this->_admin_lang->getLanguageValue('config_' . $key),
                    $descriptions,
                    $value[3]
                );
                break;

            default:
                break;
            }
        }

        // read admin.css
        $admin_css = '';
        $lines = file('../plugins/' . self::PLUGIN_TITLE. '/admin.css');
        foreach ($lines as $line_num => $line) {
            $admin_css .= trim($line);
        }

        // add template CSS
        $template = '<style>' . $admin_css . '</style>';

        // build Template
        $template .= '
            <div class="anythingslider-admin-header">
            <span>'
                . $this->_admin_lang->getLanguageValue(
                    'admin_header',
                    self::PLUGIN_TITLE
                )
            . '</span>
            <a href="' . self::PLUGIN_DOCU . '" target="_blank">
            <img style="float:right;" src="' . self::LOGO_URL . '" />
            </a>
            </div>
        </li>
        <li class="mo-in-ul-li ui-widget-content anythingslider-admin-li">
            <div class="anythingslider-admin-subheader">'
            . $this->_admin_lang->getLanguageValue('admin_size')
            . '</div>
            <div style="margin-bottom:5px;">
                <div class="anythingslider-single-conf">
                    {width_text}
                    {width_description}
                </div>
                <span class="anythingslider-admin-default">
                    <!--[' . $this->_confdefault['width'][0] . ']-->
                </span>
            </div>
            <div style="margin-bottom:5px;">
                <div class="anythingslider-single-conf">
                    {height_text}
                    {height_description}
                </div>
                <span class="anythingslider-admin-default">
                    <!--[' . $this->_confdefault['height'][0] . ']-->
                </span>
        ';

        $config['--template~~'] = $template;

        return $config;
    }

    /**
     * sets default backend configuration elements, if no plugin.conf.php is
     * created yet
     *
     * @return Array configuration
     */
    function getDefaultSettings()
    {
        $config = array('active' => 'true');
        foreach ($this->_confdefault as $elem => $default) {
            $config[$elem] = $default[0];
        }
        return $config;
    }

    /**
     * sets backend plugin information
     *
     * @return Array information
     */
    function getInfo()
    {
        global $ADMIN_CONF;

        $this->_admin_lang = new Language(
            $this->PLUGIN_SELF_DIR
            . 'lang/admin_language_'
            . $ADMIN_CONF->get('language')
            . '.txt'
        );

        // build plugin tags
        $tags = array();
        foreach ($this->_plugin_tags as $key => $tag) {
            $tags[$tag] = $this->_admin_lang->getLanguageValue('tag_' . $key);
        }

        $info = array(
            '<b>' . self::PLUGIN_TITLE . '</b> ' . self::PLUGIN_VERSION,
            self::MOZILO_VERSION,
            $this->_admin_lang->getLanguageValue(
                'description',
                htmlspecialchars($this->_plugin_tags['tag1'])
            ),
            self::PLUGIN_AUTHOR,
            self::PLUGIN_DOCU,
            $tags
        );

        return $info;
    }

    /**
     * creates configuration for text fields
     *
     * @param string $description Label
     * @param string $maxlength   Maximum number of characters
     * @param string $size        Size
     * @param string $regex       Regular expression for allowed input
     * @param string $regex_error Wrong input error message
     *
     * @return Array  Configuration
     */
    protected function confText(
        $description,
        $maxlength = '',
        $size = '',
        $regex = '',
        $regex_error = ''
    ) {
        // required properties
        $conftext = array(
            'type' => 'text',
            'description' => $description,
        );
        // optional properties
        if ($maxlength != '') {
            $conftext['maxlength'] = $maxlength;
        }
        if ($size != '') {
            $conftext['size'] = $size;
        }
        if ($regex != '') {
            $conftext['regex'] = $regex;
        }
        if ($regex_error != '') {
            $conftext['regex_error'] = $regex_error;
        }
        return $conftext;
    }

    /**
     * throws styled error message
     *
     * @param string $text Content of error message
     *
     * @return string HTML content
     */
    protected function throwError($text)
    {
        return '<div class="' . self::PLUGIN_TITLE . 'Error">'
            . '<div>' . $this->_cms_lang->getLanguageValue('error') . '</div>'
            . '<span>' . $text. '</span>'
            . '</div>';
    }
}

?>