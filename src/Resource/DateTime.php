<?php
/**
 * @package     J2Store
 * @copyright   Copyright (c)2018 Ramesh Elamathi / Cartrabbit
 * @author      Alagesan <support@j2store.org>
 * @license     GNU GPL v3 or later
 */

namespace Cartrabbit\Fields\Resource;

use J2Store\Helper\J2Html;

class DateTime extends Field
{
    function display($field, $value, $map, $inside, $options = '', $test = false, $allFields = null, $allValues = null)
    {
        $field_options = $field->field_options;
        if (empty($field_options['format'])) {
            $field_options['format'] = "YYYY-MM-DD HH:mm:ss";
        }
        $js_format = $field_options['format'];
        if (empty($field_options['php_format'])) $field_options['php_format'] = "Y-m-d H:i:s";
        $php_format = $field_options['php_format'];
        $html = J2Html::calendar($map, '', array('id' => $map, 'js_format' => $js_format, 'php_format' => $php_format));
        return $html;
    }
}