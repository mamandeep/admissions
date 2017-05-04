<?php

namespace App\Model\Table;

use Cake\Validation\Validator;
use Cake\ORM\Table;

class CucetsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
    }
}