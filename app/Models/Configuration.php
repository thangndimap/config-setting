<?php

namespace App\Models;

use Microservices\models\BaseModel;

class Configuration extends BaseModel
{
   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $casts = [
        'integer' => ['_id', 'created_by', 'is_required'],
        'unixtime' => ['created_time', 'updated_time']
    ];
    #####
    protected $table = 'configuration';
    #####
    protected $primaryKey = '_id';
    protected $idAutoIncrement = 1;
    #### GTRI MAC DINH
}
