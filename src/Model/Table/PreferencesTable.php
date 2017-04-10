<?php

namespace App\Model\Table;

use Cake\ORM\Table;



class PreferencesTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('preferences');
        $this->setPrimaryKey('id');
        
        //$this->hasOne('Programmes');
        $this->belongsTo('Programmes');
        $this->belongsTo('Candidates');
        
        $this->addBehavior('Timestamp');
    }
    
    public function isOwnedBy($preferenceId, $userId)
    {
        return $this->exists(['id' => $preferenceId, 'user_id' => $userId]);
    }
}