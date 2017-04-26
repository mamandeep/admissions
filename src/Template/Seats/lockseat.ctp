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
    text-align: center;
    vertical-align: bottom;
}
</style>
<h1>List of Available Courses as per Merit</h1>
<?php 
     echo $this->Form->create($rankings, [
        'url' => ['controller' => 'seats', 'action' => 'lockseat']
     ]); 
?>
<table>
    <tr>
        <th>Select</th>
        <th>Programme Name</th>
        <th>Seat Category</th>
        <th>Merit No.</th>
        <th>Created</th>
    </tr>

<!-- Here's where we loop through our $articles query object, printing out article info -->

    <?php  $i=0;foreach ($rankings as $seat): ?>
    <tr>
        <td><?php //debug($seat);
                $options = array(array('value' => $seat->id, 'text' => '' ));
                echo $this->Form->radio(
                    "selected_course",
                    $options, [
                    'hiddenField' => false ]
                ); ?></td>
        <td> 
            <?= $seat->programme->name ?>
        </td>
        <td> 
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
    <?php $i++; endforeach; ?>
</table>
<br/>
<div style="text-align: center"><?php echo $this->Form->button(__('Lock Seat')); ?></div>
<?php echo $this->Form->end(); ?>
<br/>
<div>
    <p>The currently locked seat is<p>
    <?php foreach ($rankings as $seat) {
        //debug($seat->id);debug($lockedSeatRankId);
        if(isset($lockedSeatRankId) && $seat->id === intval($lockedSeatRankId)) { ?>
            <ul>
                <li>Programme Name : <?php echo $seat->programme->name; ?></li>
                <li>Category : <?php echo $seat->category->type; ?></li>
                <li>Merit in Programme : <?php echo $seat->rank; ?></li>
            </ul>
        <?php  } 
    }
?>
    
</div>