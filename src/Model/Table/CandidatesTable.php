<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class CandidatesTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('candidates');
        $this->setPrimaryKey('id');
        
        
        $this->hasOne('Seats');
        
        $this->addBehavior('Timestamp');
    }
}
