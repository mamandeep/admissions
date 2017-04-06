<h1>List of Seats</h1>
<p><?= $this->Html->link('Add Seat', ['action' => 'add']) ?></p>
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

    <?php foreach ($seats as $seat): ?>
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
            <?= $seat->created->format(DATE_RFC850) ?>
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