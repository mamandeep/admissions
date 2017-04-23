<?php

namespace App\Model\Table;

use Cake\ORM\Table;



class TetpapersProgrammesTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('testpapers_programmes');
        $this->setPrimaryKey('id');
        
        $this->belongsTo('Testpapers', [
            'foreignKey' => 'testpaper_id'
        ]);
        $this->belongsTo('Programmes');
        
        $this->addBehavior('Timestamp');
    }
    
    public function isOwnedBy($seatId, $userId)
    {
        return $this->exists(['id' => $seatId, 'user_id' => $userId]);
    }
}
