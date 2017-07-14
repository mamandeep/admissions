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
    echo $this->Form->end();  ?>
<label>Category: Open</label>
<table>
<tr>
<th>Merit No.</th>
<th>CUCET Roll Number</th>
<th>Name of the Candidate</th>
<th>Candidate Category</th>
<th>Part B Marks</th>
<th>Total Marks</th>
</tr>
<?php foreach($summaryOpen as $candidate) { ?>
<tr>
    <td><?= $candidate['merit'] ?></td>
    <td><?= $candidate['rollno'] ?></td>
    <td><?= $candidate['c_name'] ?></td>
    <td><?= $candidate['c_category'] ?></td>
    <td><?= $candidate['marks_B'] ?></td>
    <td><?= $candidate['total_marks'] ?></td>
</tr>
<?php } ?>
</table>
<label>Category: SC</label>
<table>
<tr>
<th>Merit No.</th>
<th>CUCET Roll Number</th>
<th>Name of the Candidate</th>
<th>Candidate Category</th>
<th>Part B Marks</th>
<th>Total Marks</th>
</tr>
<?php foreach($summarySC as $candidate) { ?>
<tr>
    <td><?= $candidate['merit'] ?></td>
    <td><?= $candidate['rollno'] ?></td>
    <td><?= $candidate['c_name'] ?></td>
    <td><?= $candidate['c_category'] ?></td>
    <td><?= $candidate['marks_B'] ?></td>
    <td><?= $candidate['total_marks'] ?></td>
</tr>
<?php } ?>
</table>
<label>Category: ST</label>
<table>
<tr>
<th>Merit No.</th>
<th>CUCET Roll Number</th>
<th>Name of the Candidate</th>
<th>Candidate Category</th>
<th>Part B Marks</th>
<th>Total Marks</th>
</tr>
<?php foreach($summaryST as $candidate) { ?>
<tr>
    <td><?= $candidate['merit'] ?></td>
    <td><?= $candidate['rollno'] ?></td>
    <td><?= $candidate['c_name'] ?></td>
    <td><?= $candidate['c_category'] ?></td>
    <td><?= $candidate['marks_B'] ?></td>
    <td><?= $candidate['total_marks'] ?></td>
</tr>
<?php } ?>
</table>
<label>Category: OBC</label>
<table>
<tr>
<th>Merit No.</th>
<th>CUCET Roll Number</th>
<th>Name of the Candidate</th>
<th>Candidate Category</th>
<th>Part B Marks</th>
<th>Total Marks</th>
</tr>
<?php foreach($summaryOBC as $candidate) { ?>
<tr>
    <td><?= $candidate['merit'] ?></td>
    <td><?= $candidate['rollno'] ?></td>
    <td><?= $candidate['c_name'] ?></td>
    <td><?= $candidate['c_category'] ?></td>
    <td><?= $candidate['marks_B'] ?></td>
    <td><?= $candidate['total_marks'] ?></td>
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