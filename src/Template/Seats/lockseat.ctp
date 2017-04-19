<h1>List of Seats</h1>
<?php 
     echo $this->Form->create('Lockseat', [
    'url' => ['controller' => 'seats', 'action' => 'lockseat']
]); 
?>
<table>
    <tr>
        <th>Select (Only one Seat can be Locked)</th>
        <th>Programme Name</th>
        <th>Seat Category</th>
        <th>Rank</th>
        <th>Created</th>
    </tr>

<!-- Here's where we loop through our $articles query object, printing out article info -->

    <?php foreach ($seatOptions as $seat): ?>
    <tr>
        <td><?php //debug($seat);
                $options = array(array('value' => $seat->id, 'text' => '' ));
                echo $this->Form->radio(
                    'rank_id',
                    $options, [
                    'hiddenField' => false ]
                ); ?></td>
        <td> <?php echo $this->Form->hidden('programme_id', [ 'value' => $seat->programme_id]); ?>
            <?= $seat->programme->name ?>
        </td>
        <td> <?php echo $this->Form->hidden('final_category_id', [ 'value' => $seat->final_category_id]); ?>
             <?php echo $this->Form->hidden('candidate_id', [ 'value' => $seat->candidate_id]); ?>
            <?= $seat->category->type ?>
        </td>
        <td>
            <?= $seat->rank ?>
        </td>
        <td>
            <?php $dt = new DateTime($seat->created->format('Y-m-d H:i:s'), new DateTimeZone('UTC'));
                  $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
                  echo $dt->format('l, d-M-y H:i:s T');
            ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php echo $this->Form->button(__('Lock Seat')); ?>
<?php echo $this->Form->end(); ?>