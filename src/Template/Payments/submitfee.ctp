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
<?php if(isset($feePaid) && $feePaid === true) { ?>
    <table>
        <tr>
            <td colspan="2">You have successfully submitted the fee for the Programme.</td>
        </tr>
	<tr>
		<td><strong>Programme Name</strong></td>
		<td><strong>Seat Category</strong></td>
	</tr>
	<?php foreach($seatConfirmed as $fee) { ?>
        <tr>
            <td><?php echo $fee['programme']; ?></td>
            <td><?php echo $fee['category']; ?></td>
        </tr>
	<?php } ?>
    </table>
<?php } ?>
<br/>
<br/>
<br/>
<?php if(isset($submitFeeOpen) && $submitFeeOpen === true) { ?>
<div style="font-size: 16px; font-weight: bold;">Fee submission for Allocated Programme</div>
<div>Based on the choices locked by the candidates and individual merit lists prepared on the basis of these choices, your status is as following:</div>
<?php if(empty($seatAlloted['programme']) && count($seatConfirmed) === 0) { ?>
<div>Sorry, your name is not in the list of selected candidates. Please retry in the next round, if seats are vacant.</div>
<?php } else if(empty($seatAlloted['programme']) && count($seatConfirmed) > 0) { ?>
<div>You have already succesfully submited the fee for preferred Programmes.</div>
<?php } ?>
<?php //debug($seatAlloted); 
      if(!empty($seatAlloted['programme'])) { ?>
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
<div>Last date to submit the fee is: 28<sup>th</sup>June, 2017 4 P.M.. Please click below to submit the fee for the Programme. Failure to submit the fee by due date and time will lead to automatic cancellation and seat will be declared vacant.</div>
<ul>
<li>
<strong style="font:weight: bold; color: red">Please check that your eligibility is fulfilled before submission of fee.</strong>
</li>
</ul>
<br/>
<?php
    //debug($programme);
    echo $this->Form->create();
    echo $this->Form->control("tokenid", [ 'type' => 'hidden' , 'value' => $token]);
    echo $this->Form->button(__('Submit Fee'));
    echo $this->Form->end();  
    } ?>
<br/>

<script>
    $(document).ready(function() {
            $('#programme_id').on('change', function() {
                 $(this).closest('form').trigger('submit');
            });

            //$('#programme_id').change();
        });
</script>
<?php } else { ?>
<div>Submit Fee is closed at this time.</div>
<?php } ?>
