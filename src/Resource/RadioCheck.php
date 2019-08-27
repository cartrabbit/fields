<?php
/**
 * @package     J2Store
 * @copyright   Copyright (c)2018 Ramesh Elamathi / Cartrabbit
 * @author      Alagesan <support@j2store.org>
 * @license     GNU GPL v3 or later
 */

namespace Cartrabbit\Fields\Resource;
class RadioCheck extends Field
{

    var $radioType = 'checkbox';

    function show(&$field, $value)
    {
        if (!empty($field->field_value) && !is_array($field->field_value)) {
            $field->field_value = $this->explodeValues($field->field_value);
        }
        if (isset($field->field_value[$value])) $value = $field->field_value[$value]->value;
        return parent::show($field, $value);
    }

    function display($field, $value, $map, $inside, $options = '', $test = false, $allFields = null, $allValues = null)
    {
        $string = '<div id="' . $field->field_namekey . '">';
        if ($inside) $string = $this->translate($field->field_name) . ' ';
        if ($this->radioType == 'checkbox') {
            $string .= '<input type="hidden" name="' . $map . '" value=""/>';
            $map .= '[]';
        }
        if (empty($field->field_value)) return $string;
        foreach ($field->field_value as $oneValue => $title) {
            $checked = ((int)$title->disabled && !$this->platform->isAdmin()) ? 'disabled="disabled" ' : '';
            $checked .= ((is_string($value) && $oneValue == $value) || is_array($value) && in_array($oneValue, $value)) ? 'checked="checked" ' : '';
            $id = $this->prefix . $field->field_namekey . $this->suffix . '_' . $oneValue;
            $string .= '<input type="' . $this->radioType . '" name="' . $map . '" value="' . $oneValue . '" id="' . $id . '" ' . $checked . ' ' . $options . ' /><label for="' . $id . '">' . $this->translate($title->value) . '</label>';
        }
        $string .= '</div>';
        return $string;
    }
}