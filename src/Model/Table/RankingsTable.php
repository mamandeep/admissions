<?php

namespace App\Model\Table;

use Cake\ORM\Table;



class RankingsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('rankings');
        $this->setPrimaryKey('id');
        
        $this->belongsTo('Programmes');
        $this->belongsTo('Categories', [
            'foreignKey' => 'final_category_id'
        ]);
        $this->hasOne('Candidates');
        
        $this->addBehavior('Timestamp');
    }
    
    public function isOwnedBy($seatId, $userId)
    {
        return $this->exists(['id' => $seatId, 'user_id' => $userId]);
    }
}
