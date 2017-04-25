<h1>Application Form</h1>
<?php
    echo $this->Form->create($candidate);
    echo $this->Form->control("id", ['type' => 'hidden']);
    echo $this->Form->control("user_id", ['type' => 'hidden']);
    echo $this->Form->control('name', ['label' => 'Student\'s Name']);
    echo $this->Form->control('f_name', ['label' => 'Father\'s Name']);
    echo $this->Form->control('email', ['label' => 'Email']);
    echo $this->Form->control('mobile', ['label' => 'Mobile No.']);
    echo $this->Form->control('cucet_roll_no', ['label' => 'CUCET Roll No.']);
    echo $this->Form->control('aadhar_no', ['label' => 'Aadhar No. (xxxx xxxx xxxx)']);
    //echo $this->Form->control('cucet_id');
    echo $this->Form->control('dob', ['label' => 'Date of Birth (DD/MM/YYYY)']);
    echo $this->Form->control("category_id", ['label' => false, 'options' => $categories, 'type' => 'select' , 'id' => "id_category_id"]);
    echo $this->Form->button(__('Save Form'));
    echo $this->Form->end();
?>