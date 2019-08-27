<?php
/**
 * @package     J2Store
 * @copyright   Copyright (c)2018 Ramesh Elamathi / Cartrabbit
 * @author      Alagesan <support@j2store.org>
 * @license     GNU GPL v3 or later
 */

namespace Cartrabbit\Fields\Resource;
class CustomText extends Field
{
    function display($field, $value, $map, $inside, $options = '', $test = false, $allFields = null, $allValues = null)
    {
        return $this->translate($field->field_options['customtext']);
    }
}