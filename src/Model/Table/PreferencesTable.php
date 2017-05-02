<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

use Cake\ORM\Table;
use Cake\Network\Session;



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
        $session = new Session();
        $sessionData = $session->read('papercodemapping');
        $validator
            ->requirePresence('marks_A')
            ->notEmpty('marks_A', 'Please fill this field')
            ->add('marks_A', [
                'minValue' => [
                    'rule' => ['comparison', '>=', 0],
                    'message' => 'Marks should be atleast 0.',
                ],
                'maxValue' => [
                    'rule' => ['comparison', '<=', 25],
                    'message' => 'Maximum marks should not be more than 25.',
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
                    'rule' => ['comparison', '<=', 75],
                    'message' => 'Maximum marks should not be more than 75.',
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
                    'message' => 'Maximum marks should not be more than 100.',
                ],
                'sum' => [
                    'rule' => function ($value, $context){
                        $marks_A = $context['data']['marks_A'];
                        $marks_B = $context['data']['marks_B'];
                        return ($value == intval($marks_A) + intval($marks_B)) ? true : false;
                    },
                    'message' => 'Sum of Marks A and Marks B does not match.'
                ]
            ])
            ->requirePresence('programme_id')
            ->notEmpty('programme_id', 'Please fill this field')
            ->add('programme_id', [
                'checkmap' => [
                    'rule' => function ($value, $context) use ($sessionData) {
                        //debug($value); debug($context); debug($sessionData);
                        $testPaperId = $context['data']['testpaper_id'];
                        $matched = false;
                        foreach ($sessionData as $papaercodeProgMap) {
                            if($testPaperId == $papaercodeProgMap['TestpapersProgrammes__testpaper_id'] && $value == $papaercodeProgMap['TestpapersProgrammes__programme_id']) {
                                $matched = true;
                            }
                        }
                        //debug($matched);
                        return $matched;
                    },
                    'message' => 'Selected Programme and Test Paper code do not match.'
                ]
            ])
            ->requirePresence('testpaper_id')
            ->notEmpty('testpaper_id', 'Please fill this field');
        return $validator;
    }
    
    public function isOwnedBy($preferenceId, $userId)
    {
        return $this->exists(['id' => $preferenceId, 'user_id' => $userId]);
    }
}