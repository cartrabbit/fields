<?php
/**
 * @package     J2Store
 * @copyright   Copyright (c)2018 Ramesh Elamathi / Cartrabbit
 * @author      Alagesan <support@j2store.org>
 * @license     GNU GPL v3 or later
 */

namespace Cartrabbit\Fields\Resource;
class Email extends Text
{
    function check(&$field, &$value, $oldvalue = '')
    {
        $error = '';
        if (isset($field->field_required) && (!$field->field_required || is_array($value))) {
            return $error;
        }
        if (filter_var(trim($value), FILTER_VALIDATE_EMAIL) == false) {
            $error = $this->platform->translate('J2STORE_VALIDATION_ENTER_VALID_EMAIL');
        } else {
            return $error;
        }
        if ($this->report) {
            if (!$this->platform->isAdmin() || (isset($field->admin_display_error) && $field->admin_display_error)) {
                if (!empty($field->field_options['errormessage'])) {
                    $error = addslashes($this->translate($field->field_options['errormessage']));
                } else {
                    $error = $this->platform->translate_print('PLEASE_FILL_THE_FIELD', $this->translate($field->field_name));
                }
            }
        }
        $return = array();
        $return[$field->field_namekey] = $error;
        return $error;
    }
}