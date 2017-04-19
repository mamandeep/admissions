<?php

namespace App\Model\Table;

use Cake\ORM\Table;



class LockseatsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('lockseats');
        $this->setPrimaryKey('id');
        
        $this->belongsTo('Programmes');
        $this->belongsTo('Categories');
        $this->hasOne('Candidates');
        $this->hasMany('Preferences');
        $this->hasOne('Seats');
        
        $this->addBehavior('Timestamp');
    }
    
    public function isOwnedBy($lockedSeatId, $userId)
    {
        return $this->exists(['id' => $lockedSeatId, 'user_id' => $userId]);
    }
}
