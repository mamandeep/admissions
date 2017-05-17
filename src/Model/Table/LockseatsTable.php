<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;



class LockseatsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('lockseats');
        $this->setPrimaryKey('id');
        
        $this->belongsTo('Programmes');
        $this->belongsTo('Categories');
        $this->hasOne('Candidates');
        $this->hasMany('Preferences');
        $this->hasOne('Seats');
        
        $this->addBehavior('Timestamp');
    }
    
    /*public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('eligible_for_open')
            ->notEmpty('eligible_for_open', 'Please fill this field')
            ->add('eligible_for_open', [
                'validFormat' => [
                    'rule' => array('custom', '/^yes|no$/i'),
                    'message' => 'Please enter a value. E.g Yes/No.',
                ],
                
            ])
            ->requirePresence('category_pref')
            ->notEmpty('category_pref', 'Please fill this field')
            ->add('category_pref', [
                'validFormat' => [
                    'rule' => array('custom', '/^[1-5]{1}$/i'),
                    'message' => 'Please enter a valid Category. E.g Open/SC/ST/OBC.',
                ],
                'sum' => [
                    'rule' => function ($value, $context){
                        $eligible = $context['data']['eligible_for_open'];
                        return (intval($value) === 1 && strcmp($eligible, "no") === 0) ? false : true;
                    },
                    'message' => 'The Eligibility for Open Category and Category Preference selected are not allowed.'
                ]
            ]);
                    
        return $validator;
    }*/
    
    public function isOwnedBy($lockedSeatId, $userId)
    {
        return $this->exists(['id' => $lockedSeatId, 'user_id' => $userId]);
    }
}
