<?= $this->Flash->render() ?>
<?= $this->Form->create($users); ?>
    <fieldset>
        <legend><strong><?= __('Please enter your username and password') ?><strong></legend>
         <table width="100%">
            <tr>
                <td width="30%" class="form-label">Username (e.g. PGXXXXXXXX)</td>
                <td><?php echo $this->Form->control('username', ['label' => false]) ?></td>
            </tr>
            <tr>
                <td class="form-label">Password</td>
                <td><?php echo $this->Form->control('password', ['label' => false]); ?></td>
            </tr>
            <tr>
                <td colspan="2" align="center"><?= $this->Form->button(__('Login')); ?></td>
            </tr>
        </table>
    </fieldset>
<?= $this->Form->end() ?>