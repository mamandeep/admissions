<div class="users form">
<?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('Registration Details') ?></legend>
        <?= $this->Form->control('username', ['label' => 'CUCET Applicant Id (e.g. PGxxxxxxxx)']) ?>
        <?= $this->Form->control('name', ['label' => 'Name']) ?>
        <?= $this->Form->control('mobile', ['label' => 'Mobile No. (10 digit)']) ?>        
        <?= $this->Form->control('password', ['label' => 'Password']) ?>
        <ul>
            <li>The passoword must be atleast 8 characters long.</li>
            <li>The passoword must contain atleast 1 number.</li>
            <li>The passoword must contain atleast 1 alphabet.</li>
        </ul>
        <?= $this->Form->control('password_confirm', ['label' => 'Confirm Password', 'type' => 'password']) ?>
        <?= $this->Form->control('role', [ 
            'value' => 'student',
            'type' => 'hidden'
        ]) ?>
   </fieldset>
<?= $this->Form->button(__('Submit')); ?>
<?= $this->Form->end() ?>
</div>