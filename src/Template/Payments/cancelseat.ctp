<style>
    .labelsp {
        margin-right: 20px;
    }
</style>
<div class="form-container">
<h1>Cancellation of Seat and Refund of Fee</h1>
<?php
	$options = [];
	//debug($programmes_alloted);
        foreach($programmes_alloted as $p1) {
		$options[$p1['p_id']] = $p1['p_name'];
	}
    echo $this->Form->create($programme);
    echo $this->Form->control("pid", ['label' => 'Select Programme: ',  'options' => $options, 'empty' => ['select' => 'Select'], 'type' => 'select' , 'id' => "programme_id", 'maxlength'=>'100']); 
    echo $this->Form->end();

if(isset($userData) && count($userData) > 0) { 
    echo $this->Form->create($cancelseat);
    echo $this->Form->input('id', ['type' => 'hidden']);
    echo $this->Form->input('fee_id', ['type' => 'hidden', 'value' => $userData['fee_id']]);
    echo $this->Form->input('seat_id', ['type' => 'hidden', 'value' => $userData['seat_id']]);
    echo $this->Form->input('pid', ['type' => 'hidden', 'value' => $programme['id']]);
    ?>
<label>Applicant ID CUCET: <?php echo $userData['applicant_id'];?></label>
<table>
    <tr>
        <td>Name</td>
        <td><?php echo $userData['c_name']; ?></td>
    </tr>
    <tr>
        <td>Father's  Name</td>
        <td><?php echo $userData['f_name']; ?></td>
    </tr>
    <tr>
        <td>Address</td>
        <td><?php echo $this->Form->control('address', ['label' => false, 'maxlength' => '255']); ?></td>
    </tr>
    <tr>
        <td>Mobile Number</td>
        <td><?php echo $userData['mobile']; ?></td>
    </tr>
    <tr>
        <td>Email ID</td>
        <td><?php echo $userData['email']; ?></td>
    </tr>
    <tr>
        <td>Fee payment ID</td>
        <td><?php echo $userData['bank_payment_id']; ?></td>
    </tr>
    <tr>
        <td>Fee payment date</td>
        <td><?php echo $userData['fee_submit_date']; ?></td>
    </tr>
    <tr>
        <td>Seat Alloted (Course Name)</td>
        <td><?php echo $userData['p_name']; ?></td>
    </tr>
    <tr>
        <td>Seat Alloted (Category)</td>
        <td><?php echo $userData['category']; ?></td>
    </tr>
    <tr>
        <td>Amount of Fee Deposited</td>
        <td><?php echo $userData['amount']; ?></td>
    </tr>
</table>
<ul>
<li><strong style="font-size: 14px; color: red">Please note that once the Seat is cancelled you will not be considered for the same Programme.</strong></li>
</ul>
<br/>
<div><strong>It is requested that my seat may be cancelled and refund of the fee be sent in the following Account detail: </strong></div>

<table>
    <tr>
        <td>Account Holder Name</td>
        <td><?php echo $this->Form->control('ac_owner_name', ['label' => false, 'maxlength' => '100']); ?></td>
    </tr>
    <tr>
        <td>Bank Account Number</td>
        <td><?php echo $this->Form->control('ac_number', ['label' => false, 'maxlength' => '100']); ?></td>
    </tr>
    <tr>
        <td>Name of the Bank</td>
        <td><?php echo $this->Form->control('bank_name', ['label' => false, 'maxlength' => '255']); ?></td>
    </tr>
    <tr>
        <td>IFS Code (IFSC)</td>
        <td><?php echo $this->Form->control('ifs_code', ['label' => false, 'maxlength' => '50']); ?></td>
    </tr>
    <tr>
        <td>Branch Address</td>
        <td><?php echo $this->Form->control('branch_address', ['label' => false, 'maxlength' => '255']); ?></td>
    </tr>
</table>
<div>
<label style="text-decoration: underline;">Undertaking by the Candidate</label>
<br/>
<label style="font-style: italic;">
I hereby certify that, I opt to cancel my seat and request to refund the fee as per the UGC guidelines given in the University Prospectus.
</label>
</div>
<?php if(isset($seatCancelledAlready) && $seatCancelledAlready === false) {
	echo $this->Form->button(__('Submit Request'));
      } 
      echo $this->Form->end(); 
} ?>
</div>

<script>
    $(document).ready(function(){
        $('#programme_id').on('change', function() {
	     $(this).closest('form').trigger('submit');
	});

      var date_input=$('input[name="dob"]'); //our date input has the name "date"
      var container=$('.form-container form').length>0 ? $('.form-container form').parent() : "body";
      var options={
        format: 'dd/mm/yyyy',
        container: container,
        todayHighlight: true,
        autoclose: true,
      };
      date_input.datepicker(options);
    })
</script>
