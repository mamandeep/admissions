<?php

namespace App\Model\Table;

use Cake\ORM\Table;



class TetpapersTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('testpapers');
        $this->setPrimaryKey('id');
        
        $this->hasMany('TestpapersProgrammes');
        
        $this->addBehavior('Timestamp');
    }
    
    public function isOwnedBy($seatId, $userId)
    {
        return $this->exists(['id' => $seatId, 'user_id' => $userId]);
    }
}
