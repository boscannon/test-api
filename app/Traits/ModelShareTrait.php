<?php

namespace App\Traits;
use Schema;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

trait ModelShareTrait
{
	/**
     * 一對多 比對 更新刪除
     */
    protected function scopeHasManySyncable($db, $parent, $relation, $validatedData, $merge_data = [])
    {
        $deleteIds = array_diff($parent->{$relation}()->pluck('id')->all(), array_column($validatedData, 'id'));

        foreach($validatedData as $value){
            if(isset($value['id'])){
                $data = $parent->{$relation}()->findOrFail($value['id']);
                $data->update($value);
            }else{
                $parent->{$relation}()->create($value);
            }
        }

        foreach($deleteIds as $id){
            $data = $parent->{$relation}()->findOrFail($id);
            $data->delete();
        }
    }    

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date;
    }    
}