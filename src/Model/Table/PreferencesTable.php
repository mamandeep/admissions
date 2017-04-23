<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

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
        $this->belongsTo('Testpapers');
        
        $this->addBehavior('Timestamp');
    }
    
    
    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('marks_A')
            ->notEmpty('marks_A', 'Please fill this field')
            ->add('marks_A', [
                'minValue' => [
                    'rule' => ['comparison', '>=', 0],
                    'message' => 'Marks should be atleast 0.',
                ],
                'maxValue' => [
                    'rule' => ['comparison', '<=', 100],
                    'message' => 'Maximum marks should be atleast 100.',
                ]
            ])
            ->requirePresence('marks_B')
            ->notEmpty('marks_B', 'Please fill this field')
            ->add('marks_B', [
                'minValue' => [
                    'rule' => ['comparison', '>=', 0],
                    'message' => 'Marks should be atleast 0.',
                ],
                'maxValue' => [
                    'rule' => ['comparison', '<=', 100],
                    'message' => 'Maximum marks should be atleast 100.',
                ]
            ])
            ->requirePresence('marks_total')
            ->notEmpty('marks_total', 'Please fill this field')
            ->add('marks_total', [
                'minValue' => [
                    'rule' => ['comparison', '>=', 0],
                    'message' => 'Marks should be atleast 0.',
                ],
                'maxValue' => [
                    'rule' => ['comparison', '<=', 100],
                    'message' => 'Maximum marks should be atleast 100.',
                ]
            ]);
        return $validator;
    }
    
    public function isOwnedBy($preferenceId, $userId)
    {
        return $this->exists(['id' => $preferenceId, 'user_id' => $userId]);
    }
}