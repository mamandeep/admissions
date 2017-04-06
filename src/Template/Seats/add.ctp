<h1>Add Seat</h1>
<?php
    //debug($programmes);debug($categories);
    echo $this->Form->create($seat);
    echo $this->Form->input('programme_id', array('label'=>'Programmes', 'type'=>'select', 'options'=>$programmes));
    echo $this->Form->input('category_id', array('label'=>'Categories', 'type'=>'select', 'options'=>$categories));
    echo $this->Form->control('seat_no');
    echo $this->Form->button(__('Save Seats'));
    echo $this->Form->end();
?>