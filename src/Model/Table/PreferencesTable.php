<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

use Cake\ORM\Table;
use Cake\Network\Session;
use Cake\Datasource\ConnectionManager;



class PreferencesTable extends Table
{
    private $verified = [];
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('preferences');
        $this->setPrimaryKey('id');
        
        //$this->hasOne('Cucets');
        $this->belongsTo('Programmes');
        $this->belongsTo('Candidates');
        $this->belongsTo('Testpapers');
        
        $this->addBehavior('Timestamp');
    }
    
    
    public function validationDefault(Validator $validator)
    {
        $session = new Session();
        $sessionData = $session->read('papercodemapping');
        $cucetdata = $session->read('cucetdata');
        $scorecardUploaded = $session->read('scorecardUploaded');
        $validator
            ->requirePresence('marks_A')
            ->notEmpty('marks_A', 'Please fill this field')
            ->add('marks_A', [
                'minValue' => [
                    'rule' => ['comparison', '>=', -100],
                    'message' => 'Marks should be atleast 0.',
                ],
                'maxValue' => [
                    'rule' => ['comparison', '<=', 100],
                    'message' => 'Maximum marks should not be more than 25.',
                ],
                'allowZero' => [
                    'rule' => function ($value, $context) {
			//debug(bccomp($value, "0", 2));debug(strcmp($context['data']['testpaper_id'], "6")); 
                        //Add a check here that if CUCET Score card has been uploaded.
                        if((bccomp($value, "0", 2) !== 0) && (strcmp($context['data']['testpaper_id'], "6") === 0  || strcmp($context['data']['testpaper_id'], "44") === 0)) {
                            return false;
                        }
			else {
			    return true;
			}
                    },
                    'message' => 'Please fill zero(0) in marks A and total marks in marks B if this field is not applicable.',
                ]
            ])
            ->requirePresence('marks_B')
            ->notEmpty('marks_B', 'Please fill this field')
            ->add('marks_B', [
                'minValue' => [
                    'rule' => ['comparison', '>=', -100],
                    'message' => 'Marks should be atleast 0.',
                ],
                'maxValue' => [
                    'rule' => ['comparison', '<=', 100],
                    'message' => 'Maximum marks should not be more than 75.',
                ],
                'allowZero' => [
                    'rule' => function ($value, $context) {
			//debug(bccomp($value, "0", 2));debug(strcmp($context['data']['testpaper_id'], "6")); 
                        //Add a check here that if CUCET Score card has been uploaded.
                        if(bccomp($value, "0", 2) === 0 && (strcmp($context['data']['testpaper_id'], "6") === 0  || strcmp($context['data']['testpaper_id'], "44") === 0)) {
                            return false;
                        }
                        else {
			    return true;
			}
                    },
                    'message' => 'Please fill zero(0) in marks A and total marks in marks B.',
                ]
            ])
            ->requirePresence('marks_total')
            ->notEmpty('marks_total', 'Please fill this field')
            ->add('marks_total', [
                'minValue' => [
                    'rule' => ['comparison', '>=', -200],
                    'message' => 'Marks should be atleast 0.',
                ],
                'maxValue' => [
                    'rule' => ['comparison', '<=', 200],
                    'message' => 'Maximum marks should not be more than 100.',
                ],
                'sum' => [
                    'rule' => function ($value, $context){
                        $marks_A = $context['data']['marks_A'];
                        $marks_B = $context['data']['marks_B'];
			$total = bcadd($marks_A, $marks_B, 2);
                        //debug($value); debug($marks_A); debug($marks_B);
                        //debug(intval($marks_A) + intval($marks_B));
                        if(strcmp($context['data']['testpaper_id'], "6") === 0  || strcmp($context['data']['testpaper_id'], "44") === 0) {
                            return true;
                        }
                        return (bccomp($value, $total, 2) === 0) ? true : false;
                    },
                    'message' => 'Sum of Marks A and Marks B does not match.'
                ],
                'validatecucet' => [
                    'rule' => function ($value, $context) use ($cucetdata, $scorecardUploaded, $session) {
                        //Add a check here that if CUCET Score card has been uploaded.
                        /*if(strcmp($context['data']['testpaper_id'], "6") === 0  || strcmp($context['data']['testpaper_id'], "44") === 0) {
                            $this->verified[$context['data']['id']] = 0;
                            return $scorecardUploaded;
                        }*/
                        $matched = false;
			//debug($cucetdata); debug($context); debug($scorecardUploaded); 
                        foreach($cucetdata as $cucet) {
                            if($cucet['testpaper_id'] === $context['data']['testpaper_id']) {
				//debug($context['data']['marks_A']);debug($cucet['cucet_marks_A']);
				//debug(bccomp($context['data']['marks_A'], $cucet['cucet_marks_A'], 2));
//debug(strcmp($context['data']['testpaper_id'], "6")); 
				if(strcmp($context['data']['testpaper_id'], "6") === 0  || strcmp($context['data']['testpaper_id'], "44") === 0) {
                            $this->verified[$context['data']['id']] = 0;
			    $matched = (bccomp($context['data']['marks_A'], "0", 2) === 0) ? ((bccomp($context['data']['marks_B'], $cucet['cucet_total_marks'], 2) === 0) ? true : false) : false;  //debug($matched);
                        }
			else {
                                $matched = (bccomp($context['data']['marks_A'], $cucet['cucet_marks_A'], 2) === 0) ? ((bccomp($context['data']['marks_B'], $cucet['cucet_marks_B'], 2) === 0) ? true : false) : false;
                                $this->verified[$context['data']['id']] = ($matched === true) ? 1 : 0;
}
                            }
                        }
			$return  = ($matched === false) ? $scorecardUploaded : true;
			($matched === false && $scorecardUploaded === false) ? ($session->write('isUploadRequired', true)) : ($session->write('isUploadRequired', false));
                        return $return;
                    },
                    'message' => 'The data entered does not match with CUCET (Test Paper Code, Marks A, Marks B). Please correct the data or upload CUCET Score Card by clicking below.'
                ]
            ])
            ->requirePresence('programme_id')
            ->notEmpty('programme_id', 'Please fill this field')
            ->add('programme_id', [
                'checkmap' => [
                    'rule' => function ($value, $context) use ($sessionData) {
                        //debug($value); debug($context); debug($sessionData);
                        $testPaperId = $context['data']['testpaper_id'];
                        if(intval($testPaperId) === 0 || intval($value) === 0) {
                            return false;
                        }
                        $matched = false;
                        foreach ($sessionData as $papaercodeProgMap) {
                            if($testPaperId == $papaercodeProgMap['TestpapersProgrammes__testpaper_id'] && $value == $papaercodeProgMap['TestpapersProgrammes__programme_id']) {
                                $matched = true;
                            }
                        }
                        return $matched;
                    },
                    'message' => 'CUCET Test paper code and Programme Name are mandatory.'
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
    
    public function beforeSave($event, $entity, $options) {
        //debug($this->verified); debug($entity); debug($options);
        $entity->verified = (!empty($this->verified[$entity->id])) ? $this->verified[$entity->id] : NULL;
        //return false;
    }
}
