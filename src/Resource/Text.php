<?php
/**
 * @package     J2Store
 * @copyright   Copyright (c)2018 Ramesh Elamathi / Cartrabbit
 * @author      Alagesan <support@j2store.org>
 * @license     GNU GPL v3 or later
 */

namespace Cartrabbit\Fields\Resource;
class Text extends Field
{
    var $type = 'text';
    var $class = 'inputbox';

    function display($field, $value, $name, $translate, $options = '', $test = false, $allFields = null, $allValues = null)
    {
        $html = '';
        if (!empty($field)) {
            $size = '';
            if (isset($field->field_options)) {
                if (isset($field->field_options['size'])) {
                    $size .= empty($field->field_options['size']) ? '' : 'size="' . intval($field->field_options['size']) . '"';
                }
                if (isset($field->field_options['maxlength'])) {
                    $size .= empty($field->field_options['maxlength']) ? '' : ' maxlength="' . intval($field->field_options['maxlength']) . '"';
                }
                if (isset($field->field_options['readonly'])) {
                    $size .= empty($field->field_options['readonly']) ? '' : ' readonly="readonly"';
                }
            }
            $js = '';
            if ($translate && isset($field->field_name)) {
                $value = addslashes($this->translate($field->field_name));
            }
            $html .= '<input class="' . $this->class . '" id="' . $this->prefix . $field->field_namekey . $this->suffix . '" ' . $size . ' ' . $js . ' ' . $options . ' type="' . $this->type . '" name="' . $name . '" value="' . $value . '" />';
        }
        return $html;
    }

    function show(&$field, $value)
    {
        if (isset($field->field_table) && $field->field_table == 'address') return $value;
        return $this->translate($value);
    }


}