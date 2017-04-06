<?php
namespace App\Model\Table;

use Cake\ORM\Table;


class CategoriesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('categories');
        $this->setPrimaryKey('id');
        
        $this->hasMany('Seats');
        
        $this->addBehavior('Timestamp');
        
        
    }
}
