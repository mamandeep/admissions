<h1>List of Seats</h1>
<p><?= $this->Html->link('Add Seat', ['action' => 'add']) ?></p>
<p><?= $this->Html->link('Go to Centre', ['action' => 'centre', $centreId]) ?></p>
<table>
    <tr>
        <th>Id</th>
        <th>Programme</th>
        <th>Category</th>
        <th>Seat No.</th>
        <th>Created</th>
        <th>Actions</th>
    </tr>

<!-- Here's where we loop through our $articles query object, printing out article info -->

    <?php foreach ($seats as $seat): //debug($seat);?>
    <tr>
        <td><?= $seat->id ?></td>
        <td>
            <?= $seat->programme->name ?>
        </td>
        <td>
            <?= $seat->category->type ?>
        </td>
        <td>
            <?= $seat->seat_no ?>
        </td>
        <td>
            <?php $dt = new DateTime($seat->created->format('Y-m-d H:i:s'), new DateTimeZone('UTC'));
                  $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
                  echo $dt->format('l, d-M-y H:i:s T');
            ?>
        </td>
        <td>
            <?= $this->Form->postLink(
                'Delete',
                ['action' => 'delete', $seat->id],
                ['confirm' => 'Are you sure?'])
            ?>
            <?= $this->Html->link('Edit', ['action' => 'edit', $seat->id]) ?>
        </td>
    </tr>
    <?php endforeach; ?>

</table>