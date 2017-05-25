<div class="users form">
<?php //debug($cucetregister); 
     echo $this->Form->create($cucetregister); ?>
    <fieldset>
        <legend><?= __('Registration Details') ?></legend>
        <?php echo $this->Form->control('username', ['label' => 'CUCET Applicant Id (e.g. PGXXXXXXXX)']) ?>
        <?php echo $this->Form->control('name', ['label' => 'Name']) ?>
        <?php echo $this->Form->control('email', ['label' => 'Email Id.']) ?>
        <?php echo $this->Form->control('mobile', ['label' => 'Mobile No. (10 digit)']) ?>        
        <?php echo $this->Form->control('password', ['label' => 'Password']) ?>
        <ul>
            <li>The passoword must be atleast 8 characters long.</li>
            <li>The passoword must contain atleast 1 number.</li>
            <li>The passoword must contain atleast 1 alphabet.</li>
        </ul>
        <?php echo $this->Form->control('password_confirm', ['label' => 'Confirm Password', 'type' => 'password']) ?>
        <?php echo $this->Form->control('role', [ 
            'value' => 'student',
            'type' => 'hidden'
        ]) ?>
   </fieldset>
<?php echo $this->Form->button(__('Submit')); ?>
<?php echo $this->Form->end() ?>
</div>