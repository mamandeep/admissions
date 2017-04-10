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
        $this->hasMany('Preferences');
        
        $this->addBehavior('Timestamp');
    }
    
    public function isOwnedBy($articleId, $userId)
{
    return $this->exists(['id' => $articleId, 'user_id' => $userId]);
}
}
