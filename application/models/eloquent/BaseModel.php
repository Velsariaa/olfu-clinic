<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;
use \Illuminate\Database\Eloquent\Model as Eloquent;
use Spatie\HtmlElement\HtmlElement;

abstract class BaseModel extends Eloquent 
{
    public static function getTableName($alias = NULL)
    {   
        $alias = $alias ? (' as '.$alias) : '';

        $tablename = with(new static)->getTable();

        return $tablename.$alias;
    }
    public static function as($alias=null)
    {
        return self::getTableName($alias);
    }
    public static function pas($alias=null) # with prefix
    {
        return self::getPrefix().self::getTableName($alias);
    }
    public static function fieldPrefix($alias=null)
    {
        return self::getPrefix().($alias ?? '');        
    }
    public static function getPrefix()
    {
        return DB::connection(with(new static)->getConnectionName())->getTablePrefix();
    }
    public static function getTableNameWithPrefix($alias = NULL)
    {
        $alias = $alias ? (' as '.$alias) : '';

        return self::getPrefix().self::getTableName().$alias;
    }
    public function scopeIsActive($query, $alias='', $is_raw=false) 
    {
        /* do not use if table has no column "active" */

        $alias .= strlen($alias) ? '.' : '';

    	$is_raw ? $query->where(DB::raw($alias.'active'), 1) : $query->where($alias.'active', 1);
    }
    public function scopeIsInactive($query, $alias='') 
    {
        /* do not use if table has no column "active" */

        $alias .= strlen($alias) ? '.' : '';

        $query->where($alias.'active', 0);
    }
    public function getDateCreatedAttribute()
    {
        return $this->created_at ? $this->created_at->format('M. d, Y') : null;
    }
    public function getDateUpdatedAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('M. d, Y') : null;
    }
    public static function keyName()
    {
        return with(new static)->getKeyName();
    }
    public static function is_unique($field, $id=null)
    {
        $id = $id ?? post('id');

        return 'is_unique2['.join('.', [self::getTableNameWithPrefix(), self::keyName(), $id ?? '0', $field]).']';
    }
    public static function is_exists($field=null)
    {
        return 'is_exists['.join('.', [self::getTableNameWithPrefix(), $field ?? self::keyName()]).']';
    }
    public static function is_all_exists($field=null)
    {
        return 'is_all_exists['.join('.', [self::getTableNameWithPrefix(), $field ?? self::keyName()]).']';
    }
    public static function select2($text_field, $value_field=null, $include_nothing_selected=false, $callback=null, $custom_value=null, $nt='Nothing Selected')
    {
        $order_by = $custom_value ? DB::raw($custom_value) : $text_field;

        $text_field = $custom_value ? DB::raw($custom_value.' as text') : $text_field.' as text';

        $table = with(new static)::select(($value_field ?? 'id').' as value', $text_field);

        if ($callback) $callback($table);

        $table = $table
        ->distinct()
        ->orderBy($order_by)
        ->get()
        ->each
        ->setAppends([])
        ->toArray();

        if ($include_nothing_selected || !$table)
            array_unshift($table, ['value' => 0, 'text' => $nt]);

        return $table;
    }
}