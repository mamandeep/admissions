<div class="users form">
<?= $this->Flash->render() ?>
<?= $this->Form->create($users); ?>
    <fieldset>
        <legend><?= __('Please enter your username and password') ?></legend>
        <?= $this->Form->control('username') ?>
        <?= $this->Form->control('password') ?>
    </fieldset>
<?= $this->Form->button(__('Login')); ?>
<?= $this->Form->end() ?>
<br/><!--
<p><?= $this->Html->link('Register', ['controller' => 'registrations', 'action' => 'add']) ?></p>
<p><?= $this->Html->link('Forgot Password', ['controller' => 'registrations', 'action' => 'forgotpasswd']) ?></p>-->
</div>