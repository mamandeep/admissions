<?php

namespace App\Model\Table;

use Cake\Validation\Validator;
use Cake\ORM\Table;

class UploadfilesTable extends Table
{

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }
}