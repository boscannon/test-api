<?php

namespace App\Traits;
use Schema;

use Illuminate\Database\Eloquent\Model;

use App\Models\Audit;
use DateTimeInterface;

trait ObserverTrait
{
    use ModelShareTrait;
    
    use \Altek\Eventually\Eventually;

    protected static function booted()
    {
        static::created(function ($model) {
            $event = __('create');
            $auditing = [];

            $old_values = [];
            $new_values = [];
            foreach(static::$audit['only'] as $value){
                if($model->{$value} != ''){
                    $new_values[$value] = $model->{$value};

                    $name = isset(static::$audit['table']) ? 
                        __('backend/views/'.(new static::$audit['table'])->getTable().'.'.$model->getTable().'.*.'.$value) :
                        __('backend/views/'.$model->getTable().'.'.$value);

                    $auditing[] = __('backend/audits.Created', [
                        'name' => $name,
                        'new' => static::translation($model, $value),
                    ]);
                } 
            }

            static::createAudit($model, $event, $auditing, $old_values, $new_values);
        });

        static::updated(function ($model) {
            $event = __('edit');
            $auditing = [];

            $old_values = [];
            $new_values = [];
            foreach(static::$audit['only'] as $value){
                if($model->getRawOriginal($value) != $model->{$value}){
                    $old_values[$value] = $model->getRawOriginal($value);
                    $new_values[$value] = $model->{$value};

                    $name = isset(static::$audit['table']) ? 
                        __('backend/views/'.(new static::$audit['table'])->getTable().'.'.$model->getTable().'.*.'.$value) :
                        __('backend/views/'.$model->getTable().'.'.$value);

                    $auditing[] = __('backend/audits.Updated', [
                        'name' => $name,
                        'new' => static::translation($model, $value),
                    ]);
                }
            }

            static::createAudit($model, $event, $auditing, $old_values, $new_values);
        });
        
        static::deleted(function ($model) {
            $event = __('delete');
            $auditing = [];

            $old_values = [];
            $new_values = [];
            foreach(static::$audit['only'] as $value){
                if($model->{$value} != ''){
                    $old_values[$value] = $model->{$value};

                    $name = isset(static::$audit['table']) ? 
                        __('backend/views/'.(new static::$audit['table'])->getTable().'.'.$model->getTable().'.*.'.$value) :
                        __('backend/views/'.$model->getTable().'.'.$value);

                    $auditing[] = __('backend/audits.Deleted', [
                        'name' => $name,
                        'old' => static::translation($model, $value),
                    ]);
                }
            }

            static::createAudit($model, $event, $auditing, $old_values, $new_values);
        });       
        
        static::synced(function ($model, $relation, $properties) {
            $event = __('edit');
            $auditing = [];

            $old_values = [];
            $new_values = [];
            foreach(static::$audit['many'] as $key => $value){
                $new = [];
                foreach($model->{$key} as $many_value){
                    $new_values[$value][] = $many_value->{$value};
                    if($relation === 'permissions'){ //權限
                        list($action, $menu) = explode(" ", $many_value->{$value});
                        $new[] = __("backend/menu.$menu").' '.__($action);
                    }else{
                        $new[] = $many_value->{$value};
                    }
                }   

                $name = isset(static::$audit['table']) ? 
                    __('backend/views/'.(new static::$audit['table'])->getTable().'.'.$model->getTable().'.*.'.$key) :
                    __('backend/views/'.$model->getTable().'.'.$key);

                $auditing[] = __('backend/audits.Updated', [
                    'name' => $name,
                    'new' => implode(",", $new),
                ]);  
            }

            static::createAudit($model, $event, $auditing, $old_values, $new_values);
        });
    }

    public static function translation ($model, $value){
        if(isset(static::$audit['translation'][$value]) && $translation = static::$audit['translation'][$value]){
            if(isset(static::$audit['translation'][$value]['format'])){
                $obj = [ 'search' => [],'replace' => [] ];
                collect($model->{$translation['relation']}?->getAttributes())->each(function($item, $key) use (&$obj){
                    $obj['search'][] = '{'.$key.'}';
                    $obj['replace'][] = $item;
                });
                $tmp = str_replace($obj['search'], $obj['replace'], static::$audit['translation'][$value]['format'], $count);
                
                if($count == 0) return '';
                return $tmp;
            }

            if ($model->{$translation['relation']}?->{$translation['name']} instanceof \Illuminate\Support\Collection) {
                return json_encode($model->{$translation['relation']}->{$translation['name']}, JSON_UNESCAPED_UNICODE);
            }

            return $model->{$translation['relation']}?->{$translation['name']};
        }else{
            if(isset(static::$audit['format'][$value])){
                $obj = [ 'search' => [],'replace' => [] ];
                collect($model->getAttributes())->each(function($item, $key) use (&$obj){
                    $obj['search'][] = '{'.$key.'}';
                    $obj['replace'][] = $item;
                });
                $tmp = str_replace($obj['search'], $obj['replace'], static::$audit['format'][$value], $count);

                if($count == 0) return '';
                return $tmp;
            }
            if ($model->{$value} instanceof \Illuminate\Support\Collection) {
                return json_encode($model->{$value}, JSON_UNESCAPED_UNICODE);
            }
            
            return $model->{$value};
        }
    }

    public static function createAudit ($model, $event, $auditing, $old_values = [], $new_values = []) {
        $data = [
            'member_id' => auth()->guard('web')->user()->id ?? null,
            'user_id' => auth()->guard('admin')->user()->id ?? null,
            'event' => $event,
            'table' => isset(static::$audit['table']) ? (new static::$audit['table'])->getTable() : $model->getTable(),
            'table_id' => isset(static::$audit['table_id']) ? $model->{static::$audit['table_id']} : $model->id,
            'old_values' => $old_values,
            'new_values' => $new_values,
            'auditing' => $auditing,
            'url' => request()->url(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ];

        if(count($auditing) > 0) {
            Audit::create($data);
        }
    }
}