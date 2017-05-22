<?php

namespace App\Model\Table;

use Cake\Validation\Validator;
use Cake\ORM\Table;

class CancelseatsTable extends Table
{

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }
    
    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('ac_owner_name')
            ->notEmpty('ac_owner_name', 'Please fill this field')
            ->add('ac_owner_name', [
                    'maxLength' => [
                        'rule' => ['maxLength', 100],
                        'message' => 'Account holder name cannot be too long.'
                    ]
            ])
            ->requirePresence('ac_number')
            ->notEmpty('ac_number', 'Please fill this field')
            ->add('ac_number', [
                    'maxLength' => [
                        'rule' => ['maxLength', 100],
                        'message' => 'Account number cannot be too long.'
                    ],
                    'pattern' => [
                        'rule' => function ($value, $context) {
                            $match = preg_match('/^[0-9]+$/', $value);
                            return (( $match === 1) ? true : false);    
                        },
                        'message' => 'Account number should contain digits only..'
                    ]
            ])
            ->requirePresence('bank_name')
            ->notEmpty('bank_name', 'Please fill this field')
            ->add('bank_name', [
                'maxLength' => [
                    'rule' => ['maxLength', 255],
                    'message' => 'Bank name cannot be too long.'
                ]
            ])
            ->requirePresence('ifs_code')
            ->notEmpty('ifs_code', 'Please fill this field')
            ->add('ifs_code', [
                'maxLength' => [
                    'rule' => ['maxLength', 50],
                    'message' => 'IFSC cannot be too long.'
                ]
            ])
            ->requirePresence('branch_address')
            ->notEmpty('branch_address', 'Please fill this field')
            ->add('branch_address', [
                'maxLength' => [
                    'rule' => ['maxLength', 255],
                    'message' => 'Branch address cannot be too long.'
                ]
            ])
            ->requirePresence('address')
            ->notEmpty('address', 'Please fill this field')
            ->add('address', [
                'maxLength' => [
                    'rule' => ['maxLength', 255],
                    'message' => 'Address cannot be too long.'
                ]
            ]);
        return $validator;
    }
}