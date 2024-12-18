<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Spatie\HtmlElement\HtmlElement;

function smarty_function_form_input($params, &$template)
{
    $enabled = $params['enabled'] ?? false;
    
    $tuple = $template->getTemplateVars()['tuple'] ?? null;

    $tuple = method_exists($tuple, 'toArray') ? $tuple->toArray() : $tuple;
    
    $field = $params['field'] ?? '';

    sscanf($field, implode('|', array_fill(0, 8, '%[^|]')),
        $attr_name, 
        $attr_label,
        $attr_placeholder,
        $attr_type,
        $attr_step,
        $attr_id,
        $attr_required,
        $attr_readonly
    );

    if ($params['debug'] ?? false)
    {
        # dmp($attr_type);
    }

    $attrs = array_filter(get_defined_vars(), function($key)
    {
        return fnmatch('attr_*', $key);
    }, 
    ARRAY_FILTER_USE_KEY);

    foreach ($attrs as $attr => $attr_value)
    {
        ${ $attr } = $params[str_replace('attr_', '', $attr)] ?? $attr_value;
    }

    if (isset($params['type']))
    {
        sscanf($params['type'], implode('|', array_fill(0, 2, '%[^|]')),
            $attr_type, 
            $__attr_step
        );

        $attr_step = $attr_step ?? $__attr_step;
    }

    if ($params['debug'] ?? false)
    {
        # dmp($attr_type);
    }

    $attr_label = $attr_label ?? strFieldToLabel($attr_name);

    $attr_placeholder = $attr_placeholder ?? $attr_label ?? strFieldToLabel($attr_name);

    $attr_id = $attr_id ?? uuid();

    $attr_readonly = boolval($attr_readonly) || !$enabled;

    $attr_required = isset($attr_required) ? $attr_required : true;    

    $attr_required = $enabled && !$attr_readonly ? boolval($attr_required) : null;

    $attr_value = $params['value'] ?? $tuple[$attr_name] ?? null;

    $attr_disabled = ($params['disabled'] ?? false) ? 'disabled' :  null;

    #

    $attributes = array(
        'id'            => $attr_id,
        'name'          => $enabled && !$attr_readonly ? $attr_name : null,
        'placeholder'   => $attr_placeholder,
        'required'      => $attr_required ? "" : null,
        'type'          => $enabled && $attr_type ? $attr_type : 'text',
        'readonly'      => $attr_readonly ? "" : null,
        'step'          => $enabled ? $attr_step : null,
        'value'         => $attr_value,
        'disabled'      => $attr_disabled,
    );

    if ($params['debug'] ?? false)
    {
        # dmp($attributes);
    }

    $attributes = array_filter($attributes, function ($value)
    {
        return !is_null($value);
    });

    $attributes = array_merge($attributes, $params['attr'] ?? []);

    #

    $label_required_span = $attr_required ? '<span class="required"> *</span>' : '';

    $label_attr = ['for' => $attr_id];
    $label_attr = array_merge($label_attr, $params['label_attr'] ?? []);

    $label = $attr_label
    ? HtmlElement::render('label.form-label', $label_attr, $attr_label.$label_required_span)
    : null;

    if ($label) $form_group_childs[] = $label;

    #

    $input_class[] = $params['input_class'] ?? 'form-control';
    $input_class[] = $params['input_class_ex'] ?? '';
    $input_class   = array_filter($input_class);

    $input = HtmlElement::render('input.'.join('.', $input_class), $attributes, '');

    $form_group_childs[] = $input;

    #

    $form_group_class[] = $params['form_group_class'] ?? 'form-group';
    $form_group_class[] = $params['form_group_class_ex'] ?? '';
    $form_group_class   = array_filter($form_group_class);

    $form_group = (isset($params['form_group']) ? $params['form_group'] : true)
    ? HtmlElement::render('div.'.join('.', $form_group_class), find($params, 'form_group_attr', []), join('', $form_group_childs))
    : null;

    return $form_group ?? $input;
}