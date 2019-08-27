<?php
/**
 * @package     J2Store
 * @copyright   Copyright (c)2018 Ramesh Elamathi / Cartrabbit
 * @author      Alagesan <support@j2store.org>
 * @license     GNU GPL v3 or later
 */

namespace Cartrabbit\Fields\Resource;
class DropDown extends Field
{
    var $type = '';

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
        $string = '';
        if (!empty($field->field_value) && !is_array($field->field_value)) {
            $field->field_value = $this->explodeValues($field->field_value);
        }
        if (empty($field->field_value) || !count($field->field_value)) {
            return '<input type="hidden" name="' . $map . '" value="" />';
        }
        if ($this->type == "multiple") {
            $string .= '<input type="hidden" name="' . $map . '" value="" />';
            $map .= '[]';
            $arg = 'multiple="multiple"';
            if (!empty($field->field_options['size'])) $arg .= ' size="' . intval($field->field_options['size']) . '"';
        } else {
            $arg = 'size="1"';
            if (is_string($value) && empty($value) && !empty($field->field_value)) {
                $found = false;
                $first = false;
                foreach ($field->field_value as $oneValue => $title) {
                    if ($first === false) {
                        $first = $oneValue;
                    }
                    if ($oneValue == $value) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $value = $first;
                }
            }
        }
        $string .= '<select id="' . $this->prefix . $field->field_namekey . $this->suffix . '" name="' . $map . '" ' . $arg . $options . '>';
        if (empty($field->field_value))
            return $string . '</select>';
        //$admin = $this->platform->isAdmin();
        foreach ($field->field_value as $oneValue => $title) {
            //$selected = ((int)$title->disabled && !$admin) ? 'disabled="disabled" ' : '';
            $selected = ((is_numeric($value) && is_numeric($oneValue) AND $oneValue == $value) || (is_string($value) && $oneValue === $value) || is_array($value) && in_array($oneValue, $value)) ? 'selected="selected" ' : '';
            $id = $this->prefix . $field->field_namekey . $this->suffix . '_' . $oneValue;
            $string .= '<option value="' . $oneValue . '" id="' . $id . '" ' . $selected . '>' . $this->translate($title) . '</option>';
        }
        $string .= '</select>';
        return $string;
    }
}