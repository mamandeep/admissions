<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class FeesTable extends Table
{

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }
}