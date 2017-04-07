<?php

namespace App\Model\Table;

use Cake\ORM\Table;



class SeatsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('seats');
        $this->setPrimaryKey('id');
        
        $this->belongsTo('Programmes');
        $this->belongsTo('Categories');
        $this->hasOne('Candidates');
        
        $this->addBehavior('Timestamp');
    }
    
    public function isOwnedBy($seatId, $userId)
    {
        return $this->exists(['id' => $seatId, 'user_id' => $userId]);
    }
}
