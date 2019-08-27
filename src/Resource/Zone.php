<?php
/**
 * @package     J2Store
 * @copyright   Copyright (c)2018 Ramesh Elamathi / Cartrabbit
 * @author      Alagesan <support@j2store.org>
 * @license     GNU GPL v3 or later
 */

namespace Cartrabbit\Fields\Resource;

use J2Store\Helper\J2Html;
use J2Store\Helper\J2Store;
use J2Store\Models\Countries;
use J2Store\Models\Customfields;
use J2Store\Models\Zones;

class Zone extends SingleDropDown
{

    function display($field, $value, $map, $inside, $options = '', $test = false, $allFields = null, $allValues = null)
    {
        $store = J2Store::config();
        $current_zone_code = ($store->get('zone_code') > 0) ? $store->get('zone_code') : '';
        $country_code = ($store->get('country_code') > 0) ? $store->get('country_code') : '';
        //if no default value was set in the fields, then use the country id set in the store profile.
        //echo $field->field_default;
        if (empty($field->field_default)) {
            $default_country = $country_code;
        }
        if (empty($value)) {
            $value = $field->field_default;
        }
        if ($field->field_options['zone_type'] == 'country') {
            if (isset($default_country)) {
                $field->field_default = $default_country;
            }
            if (empty($value)) {
                $value = $field->field_default;
            }
        } elseif ($field->field_options['zone_type'] == 'zone') {
            $stateId = str_replace(array('[', ']'), array('_', ''), $map);
            $dropdown = '';
            $country = null;
            //no country country, then load it based on the zone default.
            /*if(empty($country) && isset($field->field_default)) {
                $table = Zones::where('zone_code',$field->field_default)->first();
                if(!empty($table->country_code)) {
                    $country = $table->country_code;
                }
            }*/
            if (empty($country)) {
                foreach ($allFields as $f) {
                    if ($f->field_type == 'zone' && !empty($f->field_options['zone_type']) && $f->field_options['zone_type'] == 'country') {
                        $key = $f->field_namekey;
                        if (!empty($allValues->$key)) {
                            $country = $allValues->$key;
                        } else {
                            $country = $f->field_default;
                        }
                        break;
                    }
                }
            }
            //still no. Set it to store default.
            if (empty($country)) {
                $country = $store->get('country_code');
                if (empty($value)) {
                    $value = $store->get('zone_code');
                }
            }
            if (!empty($country)) {
                $zones = Zones::where('country_code', $country)->orderBy('zone_name')->where('enabled', 1)->get()->pluck('zone_name', 'zone_code');
                $values = array();
                $values['*'] = $this->platform->translate('J2STORE_ALL_ZONES');
                foreach ($zones as $key => $zone_value) {
                    $values[$key] = $zone_value;
                }
                $dropdown = J2Html::select()->clearState()
                    ->type('genericlist')
                    ->name($map)
                    ->value($value)
                    ->setOptions($values)
                    ->getHtml();
            }
            $html = '<span id="' . $stateId . '_container">' . $dropdown . '</span>' .
                '<input type="hidden" id="' . $stateId . '_default_value" name="' . $stateId . '_default_value" value="' . $value . '"/>';
            return $html;
        }
        return parent::display($field, $value, $map, $inside, $options, $test, $allFields, $allValues);
    }
}