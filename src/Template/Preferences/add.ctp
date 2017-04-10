<h1>Preference</h1>
<?php
    echo $this->Form->create($preference);
    echo $this->Form->control('candidate_id');
    echo $this->Form->control('exam_id');
    echo $this->Form->control('programme_id');
    echo $this->Form->control('marks_A');
    echo $this->Form->control('marks_B');
    echo $this->Form->control('marks_total');
    echo $this->Form->button(__('Add Preference'));
    echo $this->Form->end();
?>