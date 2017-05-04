<div class="users form">
<?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('Registration Details') ?></legend>
        <?= $this->Form->control('username', ['label' => 'CUCET Applicant Id (e.g. PGxxxxxxxx)']) ?>
        <?= $this->Form->control('first_name', ['label' => 'First Name']) ?>
        <?= $this->Form->control('mobile', ['label' => 'Mobile No. (10 digit)']) ?>
        <?= $this->Form->control('password', ['label' => 'Password']) ?>
        <?= $this->Form->control('password_confirm', ['label' => 'Confirm Password']) ?>
        <?= $this->Form->control('role', [ 
            'options' => ['student' => 'Student'],
            'readonly' => 'readonly',
            'type' => 'hidden'
        ]) ?>
   </fieldset>
<?= $this->Form->button(__('Submit')); ?>
<?= $this->Form->end() ?>
</div>