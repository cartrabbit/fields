<?php
/**
 * @package     J2Store
 * @copyright   Copyright (c)2018 Ramesh Elamathi / Cartrabbit
 * @author      Alagesan <support@j2store.org>
 * @license     GNU GPL v3 or later
 */

namespace Cartrabbit\Fields\Resource;

use J2Store\Helper\J2Store;

class Field
{

    var $prefix = '';
    var $suffix = '';
    var $report = true;
    var $excludeValue = array();
    var $platform;
    var $parent;

    function __construct()
    {
        $this->platform = J2Store::platform();
    }

    function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }

    function setReport($report)
    {
        $this->report = $report;
    }

    function setParent($obj)
    {
        $this->parent = $obj;
    }

    function explodeValues($values)
    {
        $allValues = explode("\n", $values);
        $returnedValues = array();
        foreach ($allValues as $id => $oneVal) {
            $line = explode('::', trim($oneVal));
            $var = $line[0];
            $val = $line[1];
            if (count($line) == 2) {
                $disable = '0';
            } else {
                $disable = $line[2];
            }
            if (strlen($val) > 0) {
                $obj = new \stdClass();
                $obj->value = $val;
                $obj->disabled = $disable;
                $returnedValues[$var] = $obj;
            }
        }
        return $returnedValues;
    }

    function getFieldName($field)
    {
        $platform = J2Store::platform();
        $html = '';
        if (!empty($field->field_required)) {
            $html .= '<span class="j2store_field_required">*</span>';
        }
        if (isset($field->display_label) && strtolower($field->display_label) == 'yes') {
            return $html .= '<label for="' . $this->prefix . $field->field_namekey . $this->suffix . '">' . $this->translate($field->field_name) . '</label>';
        } elseif ($platform->isAdmin()) return $this->translate($field->field_name);
        return $html .= '<label for="' . $this->prefix . $field->field_namekey . $this->suffix . '">' . $this->translate($field->field_name) . '</label>';
    }

    function translate($name)
    {
        $val = preg_replace('#[^a-z0-9]#i', '_', strtoupper($name));
        $trans = $this->platform->translate($val);
        if ($val == $trans) {
            $trans = $name;
        }
        return $trans;
    }


    function check(&$field, &$value, $oldvalue = '')
    {
        $error = '';
        if (!isset($field->field_required) || !$field->field_required || is_array($value) || strlen($value) || strlen($oldvalue)) {
            return $error;
        }
        if ($this->report) {
            if (!$this->platform->isAdmin() || (isset($field->admin_display_error) && $field->admin_display_error)) {
                if (!empty($field->field_options['errormessage'])) {
                    $error = addslashes($this->translate($field->field_options['errormessage']));
                } else {
                    $error = $this->platform->translate_print('J2STORE_FIELD_REQUIRED', $this->translate($field->field_name));
                }
            }
        }
        return $error;
    }

    function display($field, $value, $name, $translate, $options = '', $test = false, $allFields = null, $allValues = null)
    {
        return $value;
    }

    function show(&$field, $value)
    {
        return $this->translate($value);
    }
}