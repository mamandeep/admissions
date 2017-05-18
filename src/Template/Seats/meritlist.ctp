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
<div style="text-align: center;">Summary of Admissions - 2017 as on <?php date_default_timezone_set('Asia/Kolkata'); echo date('d-m-Y H:i'); ?></div>
<?php
    //debug($programme);
    echo $this->Form->create($programme);
    echo $this->Form->control("user_id", ['type' => 'hidden']); 
    echo $this->Form->control("id", ['label' => 'Select Programme: ',  'options' => $programmes, 'type' => 'select' , 'id' => "programme_id", 'maxlength'=>'100']); 
    echo $this->Form->end();  ?>
<table>
    <tr>
        <td><label>Total Number of Seats: </label> <?= $totalseats ?></td>
        <td><label>Seats Filled: </label> <?= $seatsfilled ?></td>
        <td><label>Seats Vacant: </label> <?= $seatsvacant ?></td>
    </tr> 
</table>
<table>
<tr>
<th>S.No.</th>
<th>CUCET Roll Number</th>
<th>Name of the Candidate</th>
<th>Candidate Category</th>
<th>Seat Category</th>
<th>Fee Submitted</th>
</tr>
<?php $count = 1; foreach($summary as $programme) { ?>
<tr>
    <td><?= $count++ ?></td>
    <td><?= $programme['cucet_roll_no'] ?></td>
    <td><?= $programme['c_name'] ?></td>
    <td><?= $programme['c_category'] ?></td>
    <td><?= $programme['s_category'] ?></td>
    <td><?= $programme['fee'] ?></td>
</tr>
<?php } ?>
</table>

<script>
    $(document).ready(function() {
            $('#programme_id').on('change', function() {
                 $(this).closest('form').trigger('submit');
            });

            //$('#programme_id').change();
        });
</script>