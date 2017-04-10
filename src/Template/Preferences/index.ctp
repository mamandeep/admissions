<h1>List of Preferences</h1>
<p><?= $this->Html->link('Add Preference', ['action' => 'add']) ?></p>
<table>
    <tr>
        <th>Id</th>
        <th>Candidate</th>
        <th>Exam Id</th>
        <th>Programme</th>
        <th>Marks A</th>
        <th>Marks B</th>
        <th>Total</th>
        <th>Created</th>
        <th>Verified</th>
    </tr>

<!-- Here's where we loop through our $articles query object, printing out article info -->

    <?php foreach ($preferences as $preference): ?>
    <tr>
        <td><?= $preference->id ?></td>
        <td>
            <?= isset($preference->candidate->name) ?  $preference->candidate->name : "" ; ?>
        </td>
        <td>
            <?= $preference->exam_id ?>
        </td>
        <td>
            <?= isset($preference->programme->name) ?  $preference->programme->name : "" ; ?>
        </td>
        <td>
            <?= $preference->marks_A ?>
        </td>
        <td>
            <?= $preference->marks_B ?>
        </td>
        <td>
            <?= $preference->marks_total ?>
        </td>
        <td>
            <?php $dt = new DateTime($preference->created->format('Y-m-d H:i:s'), new DateTimeZone('UTC'));
                  $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
                  echo $dt->format('l, d-M-y H:i:s T');
            ?>
        </td>
        <td>
            <?= $preference->verified ?>
        </td>
        <td>
            <?= $this->Form->postLink(
                'Delete',
                ['action' => 'delete', $preference->id],
                ['confirm' => 'Are you sure?'])
            ?>
            <?= $this->Html->link('Edit', ['action' => 'edit', $preference->id]) ?>
        </td>
    </tr>
    <?php endforeach; ?>

</table>