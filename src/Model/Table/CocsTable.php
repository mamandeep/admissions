<?php

namespace App\Model\Table;

use Cake\ORM\Table;



class CocsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('cocs');
        $this->setPrimaryKey('id');
        
        $this->belongsTo('Centres');
        $this->belongsTo('Users');
        
        $this->addBehavior('Timestamp');
    }
    
    public function isOwnedBy($seatId, $userId)
    {
        return $this->exists(['id' => $seatId, 'user_id' => $userId]);
    }
}
