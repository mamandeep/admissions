<style>
table {
    border-collapse: collapse;
}

table, th, td {
    border: 1px solid black;
}

table {
    width: 100%;
}

th {
    height: 50px;
    text-align: center;
}
td {
    height: 50px;
    vertical-align: bottom;
}
</style>
<h1>List of Seats</h1>
<?php 
     echo $this->Form->create('Seatallocation', [
    'url' => ['controller' => 'seats', 'action' => 'allocateseats']
]); 
?>
<table>
    <tr>
        <th>Select Candidates</th>
        <th>Programme Name</th>
        <th>Seat Category</th>
        <th>Rank</th>
        <th>Total Marks</th>
        <th>Seat No.</th>
        <th>Created</th>
    </tr>

<!-- Here's where we loop through our $articles query object, printing out article info -->

    <?php $count = 0; 
        foreach ($lockedSeats as $seat): ?>
    <tr>
        <td><?php //debug($seat);
                $this->Form->hidden('Seatallocation.'.$count.'.id', [ 'value' => '']);
                $options = array(array('text' => '' ));
                echo $this->Form->checkbox(
                    $count.'.idcheck',
                    $options, [
                    'hiddenField' => false ]
                ); ?></td>
        <td> <?php echo $this->Form->hidden('Seatallocation.'.$count.'.seat_id', [ 'value' => $seat['Seats__id']]); ?>
            <?= $seat['Programmes__name'] ?>
        </td>
        <td> 
             <?php echo $this->Form->hidden('Seatallocation.'.$count.'.candidate_id', [ 'value' => $seat['Lockseats__candidate_id']]); ?>
             <?= $seat['Categories__type'] ?>
        </td>
        <td>
            <?php echo $this->Form->hidden('Seatallocation.'.$count.'.centre_id', [ 'value' => $seat['Programmes__centre_id']]); ?>
            <?= $seat['Lockseats__rank_id'] ?>
        </td>
        <td>
            <?= $seat['Preferences__marks_total'] ?>
        </td>
        <td>
            <?= $seat['Seats__seat_no'] ?>
        </td>
        <td>
            <?php $dt = new DateTime(isset($seat['Lockseats__created']) ? $seat['Lockseats__created'] : "", new DateTimeZone('UTC'));
                  $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
                  echo $dt->format('l, d-M-y H:i:s T');
            ?>
        </td>
        <?php $count++ ?>
    </tr>
    <?php endforeach; ?>
</table>
<?php echo $this->Form->button(__('Allocate Seats')); ?>
<?php echo $this->Form->end(); ?>