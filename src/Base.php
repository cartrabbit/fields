<?php
/**
 * @package     J2Store
 * @copyright   Copyright (c)2018 Ramesh Elamathi / Cartrabbit
 * @author      Alagesan <support@j2store.org>
 * @license     GNU GPL v3 or later
 */

namespace Cartrabbit\Fields;

use J2Store\Helper\J2Store;
use J2Store\Models\Countries;
use J2Store\Models\Customfields;
use J2Store\Models\Zones;

class Base
{
    var $prefix = '';
    var $suffix = '';

    function getFormatedDisplay($field, $value, $name, $translate = false, $options = '', $test = false, $allFields = null, $allValues = null)
    {
        $label = $this->getFieldLabel($field);
        $input = $this->display($field, $value, $name, $translate, $options, $test, $allFields, $allValues);
        $html = $label . $input;
        return $html;
    }

    function getFieldLabel($field)
    {
        $html = '';
        if (isset($field->field_type)) {
            $className = '\\Cartrabbit\\Fields\\Resource\\' . ucfirst($field->field_type);
            $class = new $className;
            $html .= $class->getFieldName($field);
        }
        return $html;
    }

    function display($field, $value, $name, $translate = false, $options = '', $test = false, $allFields = null, $allValues = null)
    {
        $html = '';
        if (isset($field->field_type)) {
            $className = '\\Cartrabbit\\Fields\\Resource\\' . ucfirst($field->field_type);
            $class = new $className;
            if (is_string($value)) {
                $value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
            }
            $data = null;
            $fields = array();
            $fields[$field->field_namekey] = $field;
            $this->prepareFields($fields, $data, 'address');
            $field = $fields[$field->field_namekey];
            $class->setPrefix($this->prefix);
            $class->setSuffix($this->suffix);
            $html .= $class->display($field, $value, $name, $translate, $options, $test, $allFields, $allValues);
        }
        return $html;
    }

    function show($field, $value)
    {
        $html = '';
        if (isset($field->field_type)) {
            $className = '\\Cartrabbit\\Fields\\Resource\\' . ucfirst($field->field_type);
            $class = new $className;
            if (is_string($value)) {
                $value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
            }
            $html .= $class->show($field, $value);
        }
        return $html;
    }

    function validate($formData, $area, $type = 'address', &$json)
    {
        if (is_array($formData)) {
            $formData = (object)$formData;
        }
        $fields = $this->getFields($area, $formData, $type);
        foreach ($fields as $field) {
            if (isset($field->field_type) && !empty($field->field_type)) {
                if (isset($data->admin_display_error) && $formData->admin_display_error) {
                    $field->admin_display_error = 1;
                }
                $className = '\\Cartrabbit\\Fields\\Resource\\' . ucfirst($field->field_type);
                $class = new $className;
                $namekey = $field->field_namekey;
                if (isset($formData->$namekey)) {
                    $val = $formData->$namekey;
                } else {
                    $val = '';
                }
                $error = $class->check($field, $val);
                if (!empty($error)) {
                    $json['error'][$namekey] = $error;
                }
            }
        }
    }


    function getFields($area, &$data, $type = 'address', $url = 'checkout&task=state', $notcoreonly = false)
    {
        $fields = $this->getData($area, $type, $notcoreonly);
        $this->prepareFields($fields, $data, $type, $url, $area);
        return $fields;
    }

    function getField($field_id, $type = 'address')
    {
        $field = array();
        if (is_numeric($field_id)) {
            $element = Customfields::find($field_id);
            //$element = F0FModel::getTmpInstance('CustomFields' ,'J2StoreModel')->getItem($fieldid);
        } else {
            $model = new Customfields();
            $model->where('field_table', $type);
            $element = $model->where('field_namekey', $field_id)->where('enabled', 1)->first();
        }
        if (!empty($element)) {
            $fields = array();
            $fields[$element->field_namekey] = $element;
            $data = null;
            $this->prepareFields($fields, $data, $fields[$element->field_namekey]->field_type, '', true);
            $field = $fields[$element->field_namekey];
        }
        return $field;
    }

    /*
	 * @area string display area - billing or shipping or payment
	 * @type string field table type example: address
	 * @notcoreonly boolen true for core fields
	 */
    function getData($area, $type, $notcoreonly = false)
    {
        static $data = array();
        $key = $area . "_" . $type . "_" . $notcoreonly;
        if (empty($data[$key])) {
            $custom_field_model = new Customfields();
            $custom_field_model = $custom_field_model->where('enabled', 1);
            if ($notcoreonly) {
                $custom_field_model = $custom_field_model->where('field_core', 0);
            }
            $custom_field_model = $custom_field_model->where('field_table', $type)->orderBy('ordering');
            $clauses = explode(';', trim($area, ';'));
            foreach ($clauses as $clause) {
                if (empty($clause))
                    continue;
                $v = '1';
                if (strpos($clause, '=') !== false) {
                    list($clause, $v) = explode('=', $clause, 2);
                    $v = (int)$v;
                }
                if (substr($clause, 0, 8) == 'display:') {
                    $cond = substr($clause, 8) . $v;
                    $custom_field_model = $custom_field_model->where('field_display', 'LIKE', '%' . $cond . '%');
                    //$this->where[] = 'a.field_display LIKE \'%;'.$cond.';%\'';
                } else {
                    // $custom_field_model = $custom_field_model->where($clause,$v);
                    //$this->where[] = 'a.' . $db->quoteName($clause) . $v;
                }
            }
            $data[$key] = $custom_field_model->get()->keyBy('field_namekey');
        }
        return $data[$key];
    }

    function prepareFields($fields, &$data, $type = 'user', $url = 'checkout&task=state', $test = false, $area = '')
    {
        if (!empty($fields)) {
            if ($type == 'address') {
                $id = 'id';
            } else {
                $id = $type . '_id';
            }
            if ($data == null || empty($data)) {
                $data = new \stdClass();
            }
            if (is_array($data)) {
                $data = (object)$data;
            }
            foreach ($fields as $namekey => &$field) {

                if (isset($field->field_options) && !empty($field->field_options) && is_string($field->field_options)) {
                    $field->field_options = unserialize($field->field_options);
                }
                if (isset($field->field_value) && !empty($field->field_value) && is_string($field->field_value)) {
                    $field->field_value = $this->explodeValues($field->field_value, $field);
                }
                if (!isset($data->$namekey) || empty($data->$namekey)) {
                    if (isset($field->field_default) && $field->field_default) {
                        $data->$namekey = $field->field_default;
                    }
                }
                if (isset($field->field_type) && isset($field->field_options) && $field->field_type == 'zone' && !empty($field->field_options['zone_type'])) {
                    $this->handleZone($field, $test, $data, $area);
                }
            }
        }
    }

    function explodeValues($values, $field)
    {
        $returnedValues = array();
        if (!empty($values)) {
            $allValues = explode("\n", $values);
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
        }
        return $returnedValues;
    }

    function handleZone(&$field, $test = false, $data, $area = '')
    {
        //foreach ($fields as $namekey => &$field) {
            if (isset($field->field_type) && isset($field->field_options) && $field->field_type == 'zone' && !empty($field->field_options['zone_type'])) {
                $namekey = $field->field_namekey;
                if ($field->field_options['zone_type'] == 'country') {
                    // country
                    if (isset($data->$namekey) && !empty($data->$namekey) && $data->$namekey == $field->field_namekey) {
                        $field->field_default = $data->$namekey;
                    }
                    $field->field_value = Countries::where('enabled', 1)->get()->pluck('country_name', 'country_isocode_3')->toArray();
                } elseif ($field->field_options['zone_type'] == 'zone') {
                    // zone
                    $allFields = $this->getData($area, 'address');
                    //$allFields = array();
                    //find country fields
                    $country_id = '';
                    foreach ($allFields as $one_field) {
                        if (isset($one_field->field_options) && !empty($one_field->field_options) && is_string($one_field->field_options)) {
                            $one_field->field_options = unserialize($one_field->field_options);
                        }
                        if ($one_field->field_type == 'zone' && $one_field->field_options['zone_type'] == 'country') {
                            if (isset($data->$namekey) && !empty($data->$namekey) && $data->$namekey == $field->field_namekey) {
                                $one_field->field_default = $data->$namekey;
                            }
                            $country_id = $one_field->field_default;
                            break;
                        }
                    }
                    // set related zone list
                    if ($country_id) {
                        $field->field_value = Zones::where('country_code', $country_id)->where('enabled', 1)->get()->pluck('zone_name', 'zone_code')->toArray();
                    }
                }
                //}
        }
    }


}