<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Spatie\HtmlElement\HtmlElement;

function smarty_function_form_toggle($params, &$template)
{
    $enabled = $params['enabled'] ?? false;
    
    $tuple = $template->getTemplateVars()['tuple'] ?? null;

    $tuple = method_exists($tuple, 'toArray') ? $tuple->toArray() : $tuple;
    
    $field = $params['field'] ?? '';

    sscanf($field, implode('|', array_fill(0, 7, '%[^|]')),
        $attr_name, 
        $attr_label,
        $attr_checked,
        $attr_value,
        $attr_id,
        $attr_required,
        $attr_readonly
    );

    $attrs = array_filter(get_defined_vars(), function($key)
    {
        return fnmatch('attr_*', $key);
    }, 
    ARRAY_FILTER_USE_KEY);

    foreach ($attrs as $attr => $attr_value)
    {
        ${ $attr } = $params[str_replace('attr_', '', $attr)] ?? $attr_value;
    }

    $attr_label = $attr_label ?? strFieldToLabel($attr_name);

    $attr_id = $attr_id ?? uuid();

    $attr_readonly = boolval($attr_readonly) || !$enabled;

    $attr_required = isset($attr_required) ? $attr_required : false;    

    $attr_required = $enabled && !$attr_readonly ? boolval($attr_required) : null;

    $attr_value = isset($attr_value) ? $attr_value : 1;

    $attr_checked = boolval($params['checked'] ?? ($tuple ? $tuple[$attr_name] : true) ?? null);

    #

    $attributes = array(
        'id'            => $attr_id,
        'name'          => $enabled && !$attr_readonly ? $attr_name : null,
        'required'      => $attr_required ? "" : null,
        'readonly'      => $attr_readonly ? "" : null,
        'value'         => $attr_value,
        'checked'       => $attr_checked ? "" : null,
        'type'          => 'checkbox'
    );

    $attributes = array_filter($attributes, function ($value)
    {
        return !is_null($value);
    });

    $attributes = array_merge($attributes, $params['attr'] ?? []);

    #

    $input_class[] = $params['input_class'] ?? 'custom-control-input';
    $input_class[] = $params['input_class_ex'] ?? '';
    $input_class   = array_filter($input_class);

    $input = HtmlElement::render('input.'.join('.', $input_class), $attributes, '');

    $form_group_childs[] = $input;

    #

    $label_required_span = $attr_required ? '<span class="required"> *</span>' : '';

    $label_attr = ['for' => $attr_id];
    $label_attr = array_merge($label_attr, $params['label_attr'] ?? []);

    $label = $attr_label
    ? HtmlElement::render('label.custom-control-label', $label_attr, $attr_label.$label_required_span)
    : null;

    if ($label) $form_group_childs[] = $label;

    #

    $form_group_custom_control = HtmlElement::render('div.custom-control custom-control-right custom-switch custom-control-inline', [], join('', $form_group_childs));

    #

    $form_group_class[] = $params['form_group_class'] ?? 'form-group';
    $form_group_class[] = $params['form_group_class_ex'] ?? '';
    $form_group_class   = array_filter($form_group_class);

    $form_group = (isset($params['form_group']) ? $params['form_group'] : true)
    ? HtmlElement::render('div.'.join('.', $form_group_class), find($params, 'form_group_attr', []), $form_group_custom_control)
    : null;

    return $form_group ?? $input;
}