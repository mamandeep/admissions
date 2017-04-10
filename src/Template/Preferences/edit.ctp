<h1>Edit Preferences</h1>
<?php
    echo $this->Form->create($preference);
    echo $this->Form->control('candidate_id');
    echo $this->Form->control('exam_id');
    echo $this->Form->input(
            'programme_id', 
            [
                'type' => 'select',
                'multiple' => false,
                'options' => $programmes, 
                'empty' => false
            ]
    );
    echo $this->Form->control('marks_A');
    echo $this->Form->control('marks_B');
    echo $this->Form->control('marks_total');
    echo $this->Form->button(__('Save Preference'));
    echo $this->Form->end();
?>