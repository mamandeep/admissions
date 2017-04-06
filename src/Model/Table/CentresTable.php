<?php
namespace App\Model\Table;

use Cake\ORM\Table;



class CentresTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('centres');
        $this->setPrimaryKey('id');
        
        $this->belongsTo('Schools');
        
        $this->hasMany('Programmes');
        
        $this->addBehavior('Timestamp');
    }
}
