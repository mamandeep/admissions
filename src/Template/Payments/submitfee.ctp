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
<div style="font-size: 16px; font-weight: bold;">Fee submission for Allocated Programme</div>
<div>Based on the choices locked by the candidates and individual merit lists prepared on the basis of these choices, your status is as following:</div>
<?php if(empty($seatAlloted['programme'])) { ?>
<div>Sorry, your name is not in the list of selected candidates. Please retry in the next round, if seats are vacant.</div>
<?php } ?>
<?php //debug($seatAlloted); 
      if(isset($feePaid) && $feePaid === false && !empty($seatAlloted['programme'])) { ?>
<div>You have been allocated following seat: </div>
<table>
<tr>
<th>Programme Name</th>
<th>Seat Category</th>
</tr>
<tr>
    <td><?= $seatAlloted['programme'] ?></td>
    <td><?= $seatAlloted['category'] ?></td>
</tr>
</table>
<div>Last date to submit the fee is:  . Please click below to submit the fee for the Programme. Failure to submit the fee by due date and time will lead to automatic cancellation and seat will be declared vacant.</div>
<br/>
<?php
    //debug($programme);
    echo $this->Form->create();
    echo $this->Form->control("tokenid", [ 'type' => 'hidden' , 'value' => $token]);
    echo $this->Form->button(__('Submit Fee'));
    echo $this->Form->end();  
    } ?>
<?php if(isset($feePaid) && $feePaid === true) { ?>
    <table>
        <tr>
            <td colspan="2">You have successfully submitted the fee for the allocated Programme.</td>
        </tr>
        <tr>
            <td>Programme Name: <?php echo $seatAlloted['programme']; ?></td>
            <td>Category: <?php echo $seatAlloted['category']; ?></td>
        </tr>
    </table>
<?php } ?>
<script>
    $(document).ready(function() {
            $('#programme_id').on('change', function() {
                 $(this).closest('form').trigger('submit');
            });

            //$('#programme_id').change();
        });
</script>