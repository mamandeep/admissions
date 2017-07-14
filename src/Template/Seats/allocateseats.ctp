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
<div style="font-size: 16px; font-weight: bold; text-align: center;">Welcome to Admin Panel for CUPB Admissions - 2017</div>
<div style="text-align: center;">Programme wise Merit List for Admissions - 2017 as on <?php date_default_timezone_set('Asia/Kolkata'); echo date('d-m-Y H:i'); ?></div>
<?php
    //debug($programme);
    echo $this->Form->create($programme);
    echo $this->Form->control("user_id", ['type' => 'hidden']); 
    echo $this->Form->control("id", ['label' => 'Select Programme: ',  'options' => $programmes, 'empty' => ['select' => 'Select'], 'type' => 'select' , 'id' => "programme_id", 'maxlength'=>'100']); 
    echo $this->Form->end();
 
    echo $this->Form->create('Seatallocation', [
        'url' => ['controller' => 'seats', 'action' => 'allocateseats']
    ]);
    
?>
<label>Category: Open</label> (Total Seats: <?= $totalOpenSeats ?>; Filled: <?=  $seatsOpenFilled ?>; Vacant: <?= $seatsOpenVacant ?>)
<table>
<tr>
<th>S. No.</th>
<th>Merit No.</th>
<th>Total Marks</th>
<th>CUCET Roll Number</th>
<th>Name of the Candidate</th>
<th>Candidate Category</th>
<th>Allocate Seat</th>
</tr>
<?php $gcount = 0; $count = 0; foreach($summaryOpen as $candidate) { ?>
<tr>
    <td><?= $gcount+1; echo $this->Form->hidden('id', [ 'value' => $candidate['programme_id']]); ?></td>
    <td><?= $candidate['merit'] ?></td>
    <td><?= $candidate['total_marks'] ?></td>
    <td><?= $candidate['rollno'] ?></td>
    <td><?= $candidate['c_name'] ?></td>
    <td><?= $candidate['c_category'] ?></td>
    <td><?php echo $this->Form->hidden('Seatallocation.'.$count.'.candidate_id', [ 'value' => $candidate['candidate_id']]); ?>
        <?php echo $this->Form->hidden('Seatallocation.'.$count.'.programme_id', [ 'value' => $candidate['programme_id']]); ?>
        <?php echo $this->Form->hidden('Seatallocation.'.$count.'.seat_category_id', [ 'value' => '1']); ?>
        <?php //echo $this->Form->hidden('Seatallocation.'.$count.'.seat_id', [ 'value' => $candidate['seat_id']]); ?><?php $this->Form->hidden('Seatallocation.'.$count.'.id', [ 'value' => '']);
                $options = array(array('text' => '' ));
                echo $this->Form->checkbox(
                    'Seatallocation.' . $count.'.idcheck',
                    $options, [
                    'hiddenField' => false ]
                ); ?></td>
</tr>
<?php $gcount++; $count++; } ?>
</table>
<label>Category: SC</label> (Total Seats: <?= $totalSCSeats ?>; Filled: <?=  $seatsSCFilled ?>; Vacant: <?= $seatsSCVacant ?>)
<table>
<tr>
<th>S. No.</th>
<th>Merit No.</th>
<th>Total Marks</th>
<th>CUCET Roll Number</th>
<th>Name of the Candidate</th>
<th>Candidate Category</th>
<th>Preferred Category</th>
<th>Allocate Seat</th>
</tr>
<?php $gcount = 0; foreach($summarySC as $candidate) { ?>
<tr>
    <td><?= $gcount+1; echo $this->Form->hidden('id', [ 'value' => $candidate['programme_id']]); ?></td>
    <td><?= $candidate['merit'] ?></td>
    <td><?= $candidate['total_marks'] ?></td>
    <td><?= $candidate['rollno'] ?></td>
    <td><?= $candidate['c_name'] ?></td>
    <td><?= $candidate['c_category'] ?></td>
    <td><?= $candidate['c_category_pref'] ?><?php echo $this->Form->hidden('Seatallocation.'.$count.'.candidate_id', [ 'value' => $candidate['candidate_id']]); ?>
        <?php echo $this->Form->hidden('Seatallocation.'.$count.'.programme_id', [ 'value' => $candidate['programme_id']]); ?>
        <?php echo $this->Form->hidden('Seatallocation.'.$count.'.seat_category_id', [ 'value' => '3']); ?>
        <?php //echo $this->Form->hidden('Seatallocation.'.$count.'.seat_id', [ 'value' => $candidate['seat_id']]); ?></td>
    <td><?php $this->Form->hidden('Seatallocation.'.$count.'.id', [ 'value' => '']);
                $options = array(array('text' => '' ));
                echo $this->Form->checkbox(
                    'Seatallocation.' . $count.'.idcheck',
                    $options, [
                    'hiddenField' => false ]
                ); ?></td>
</tr>
<?php $gcount++; $count++; } ?>
</table>
<label>Category: ST</label>(Total Seats: <?= $totalSTSeats ?>; Filled: <?=  $seatsSTFilled ?>; Vacant: <?= $seatsSTVacant ?>)
<table>
<tr>
<th>S. No.</th>
<th>Merit No.</th>
<th>Total Marks</th>
<th>CUCET Roll Number</th>
<th>Name of the Candidate</th>
<th>Candidate Category</th>
<th>Preferred Category</th>
<th>Allocate Seat</th>
</tr>
<?php $gcount = 0; foreach($summaryST as $candidate) { ?>
<tr>
    <td><?= $gcount+1; echo $this->Form->hidden('id', [ 'value' => $candidate['programme_id']]); ?></td>
    <td><?= $candidate['merit'] ?></td>
    <td><?= $candidate['total_marks'] ?></td>
    <td><?= $candidate['rollno'] ?></td>
    <td><?= $candidate['c_name'] ?></td>
    <td><?= $candidate['c_category'] ?></td>
    <td><?= $candidate['c_category_pref'] ?><?php echo $this->Form->hidden('Seatallocation.'.$count.'.candidate_id', [ 'value' => $candidate['candidate_id']]); ?>
        <?php echo $this->Form->hidden('Seatallocation.'.$count.'.programme_id', [ 'value' => $candidate['programme_id']]); ?>
        <?php echo $this->Form->hidden('Seatallocation.'.$count.'.seat_category_id', [ 'value' => '4']); ?>
        <?php //echo $this->Form->hidden('Seatallocation.'.$count.'.seat_id', [ 'value' => $candidate['seat_id']]); ?></td>
    <td><?php $this->Form->hidden('Seatallocation.'.$count.'.id', [ 'value' => '']);
                $options = array(array('text' => '' ));
                echo $this->Form->checkbox(
                    'Seatallocation.' . $count.'.idcheck',
                    $options, [
                    'hiddenField' => false ]
                ); ?></td>
</tr>
<?php $gcount++; $count++; } ?>
</table>
<label>Category: OBC</label>(Total Seats: <?= $totalOBCSeats ?>; Filled: <?=  $seatsOBCFilled ?>; Vacant: <?= $seatsOBCVacant ?>)
<table>
<tr>
<th>S. No.</th>
<th>Merit No.</th>
<th>Total Marks</th>
<th>CUCET Roll Number</th>
<th>Name of the Candidate</th>
<th>Candidate Category</th>
<th>Preferred Category</th>
<th>Allocate Seat</th>
</tr>
<?php $gcount = 0; foreach($summaryOBC as $candidate) { ?>
<tr>
    <td><?= $gcount+1; echo $this->Form->hidden('id', [ 'value' => $candidate['programme_id']]); ?></td>
    <td><?= $candidate['merit'] ?></td>
    <td><?= $candidate['total_marks'] ?></td>
    <td><?= $candidate['rollno'] ?></td>
    <td><?= $candidate['c_name'] ?></td>
    <td><?= $candidate['c_category'] ?></td>
    <td><?= $candidate['c_category_pref'] ?><?php echo $this->Form->hidden('Seatallocation.'.$count.'.candidate_id', [ 'value' => $candidate['candidate_id']]); ?>
        <?php echo $this->Form->hidden('Seatallocation.'.$count.'.programme_id', [ 'value' => $candidate['programme_id']]); ?>
        <?php echo $this->Form->hidden('Seatallocation.'.$count.'.seat_category_id', [ 'value' => '5']); ?>
        <?php //echo $this->Form->hidden('Seatallocation.'.$count.'.seat_id', [ 'value' => $candidate['seat_id']]); ?></td>
    <td><?php $this->Form->hidden('Seatallocation.'.$count.'.id', [ 'value' => '']);
                $options = array(array('text' => '' ));
                echo $this->Form->checkbox(
                    'Seatallocation.' . $count.'.idcheck',
                    $options, [
                    'hiddenField' => false ]
                ); ?></td>
</tr>
<?php $gcount++; $count++; } ?>
</table>
<?php echo $this->Form->button(__('Select Seats and click here for Seat Allocation')); ?>
<?php echo $this->Form->end(); ?>
<script>
    $(document).ready(function() {
            $('#programme_id').on('change', function() {
                 $(this).closest('form').trigger('submit');
            });

            //$('#programme_id').change();
            //var 
        });
</script>