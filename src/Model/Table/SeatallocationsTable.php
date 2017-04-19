<?php

namespace App\Model\Table;

use Cake\ORM\Table;



class SeatallocationsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('seatallocations');
        $this->setPrimaryKey('id');
        
        $this->belongsTo('Seats');
        $this->belongsTo('Candidates');
        $this->belongsTo('Cocs');
        $this->belongsTo('Users');
        
        $this->addBehavior('Timestamp');
    }
}
