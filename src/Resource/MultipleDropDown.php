<?php
/**
 * @package     J2Store
 * @copyright   Copyright (c)2018 Ramesh Elamathi / Cartrabbit
 * @author      Alagesan <support@j2store.org>
 * @license     GNU GPL v3 or later
 */

namespace Cartrabbit\Fields\Resource;
class MultipleDropDown extends DropDown
{
    var $type = 'multiple';

    function display($field, $value, $map, $inside, $options = '', $test = false, $allFields = null, $allValues = null)
    {
        $value = explode(',', $value);
        return parent::display($field, $value, $map, $inside, $options, $test, $allFields, $allValues);
    }

    function show(&$field, $value)
    {
        if (!is_array($value)) {
            $value = explode(',', $value);
        }
        if (!empty($field->field_value) && !is_array($field->field_value)) {
            $field->field_value = $this->explodeValues($field->field_value);
        }
        $results = array();
        foreach ($value as $val) {
            if (isset($field->field_value[$val])) $val = $field->field_value[$val]->value;
            $results[] = parent::show($field, $val);
        }
        return implode(', ', $results);
    }
}