<?php
namespace App\Model\Table;

use Cake\ORM\Table;



class ProgrammesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('programmes');
        $this->setPrimaryKey('id');
        
        $this->belongsTo('Centres');
        
        $this->hasMany('Seats');
        
        $this->addBehavior('Timestamp');
    }
}
