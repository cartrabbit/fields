<?php
/**
 * @package     J2Store
 * @copyright   Copyright (c)2018 Ramesh Elamathi / Cartrabbit
 * @author      Alagesan <support@j2store.org>
 * @license     GNU GPL v3 or later
 */

namespace Cartrabbit\Fields\Resource;
class TextArea extends Field
{

    function display($field, $value, $name, $translate, $options = '', $test = false, $allFields = null, $allValues = null)
    {
        $js = '';
        $html = '';
        if ($translate && strlen($value) < 1) {
            $value = addslashes($this->translate($field->field_name));
            $this->excludeValue[$field->field_namekey] = $value;
            $js = 'onfocus="if(this.value == \'' . $value . '\') this.value = \'\';" onblur="if(this.value==\'\') this.value=\'' . $value . '\';"';
        }
        if (!empty($field->field_options['maxlength'])) {
            static $done = false;
            if (!$done) {
                $jsFunc = '
				<script type="text/javascript">
				function CartrabbitTextCounter(textarea, counterID, maxLen) {
					cnt = document.getElementById(counterID);
					if (textarea.value.length > maxLen){
						textarea.value = textarea.value.substring(0,maxLen);
					}
					cnt.innerHTML = maxLen - textarea.value.length;
				}
				</script>
				';
                $html .= $jsFunc;
                $html .= '<span class="j2store_remaining_characters">' . $this->platform->translate_print('J2STORE_X_CHARACTERS_REMAINING', $this->prefix . $field->field_namekey . $this->suffix . '_count', (int)$field->field_options['maxlength']) . '</span>';
            }
            $js .= ' onKeyUp="CartrabbitTextCounter(this,\'' . $this->prefix . $field->field_namekey . $this->suffix . '_count' . '\',' . (int)$field->field_options['maxlength'] . ');" onBlur="CartrabbitTextCounter(this,\'' . $this->prefix . @$field->field_namekey . $this->suffix . '_count' . '\',' . (int)$field->field_options['maxlength'] . ');" ';
        }
        $cols = empty($field->field_options['cols']) ? '' : 'cols="' . intval($field->field_options['cols']) . '"';
        $rows = empty($field->field_options['rows']) ? '' : 'rows="' . intval($field->field_options['rows']) . '"';
        $options .= empty($field->field_options['readonly']) ? '' : ' readonly="readonly"';
        return '<textarea class="inputbox" id="' . $this->prefix . @$field->field_namekey . $this->suffix . '" name="' . $name . '" ' . $cols . ' ' . $rows . ' ' . $js . ' ' . $options . '>' . $value . '</textarea>' . $html;
    }

    function show(&$field, $value)
    {
        return nl2br(parent::show($field, $value));
    }
}