<style>
    .labelsp {
        margin-right: 20px;
    }
</style>
<div class="form-container">
<h1>Application Form</h1>
<?php
    echo $this->Form->create($candidate);
    echo $this->Form->control("id", ['type' => 'hidden']); 
    echo $this->Form->control("user_id", ['type' => 'hidden']); ?>
<table>
    <tr>
        <td colspan="2"><?php echo $this->Form->control('name', ['label' => 'Name of the Candidate (full name as mentioned on your certificates)', 'maxlength'=>'100']); ?></td>
    </tr>
    <tr>
        <td colspan="2"><?php echo $this->Form->control('f_name', ['label' => 'Father\'s Name', 'maxlength'=>'100']); ?></td>
    </tr>
    <tr>
        <td><?php echo $this->Form->control('dob', ['label' => 'Date of Birth (DD/MM/YYYY)', 'placeholder' => 'DD/MM/YYYY', 'maxlength'=>'20']); ?></td>
        <td><?php echo $this->Form->control('cucet_roll_no', ['label' => 'CUCET Roll No. (e.g. 8 digit)', 'maxlength'=>'100']); ?></td>
    </tr>
    <tr>
        <td><?php echo $this->Form->control("category_id", ['label' => 'Category', 'empty' => ['select' => 'Select'],  'options' => $categories, 'type' => 'select' , 'id' => "id_category_id", 'maxlength'=>'100']); ?></td>
        <td><?php echo $this->Form->control('pwd', ['label' => 'Person with Disability', 'empty' => ['select' => 'Select'],  'options' => ['yes' => 'Yes', 'no' => 'No'], 'type' => 'select' , 'id' => "id_pwd", 'maxlength'=>'100']); ?></td>
    </tr>
    <tr>
        <td><?php echo $this->Form->control('ward_of_def', ['label' => 'Ward of Defense Personnel', 'empty' => ['select' => 'Select'],  'options' => ['yes' => 'Yes', 'no' => 'No'], 'type' => 'select' , 'id' => "id_wardofdef", 'maxlength'=>'100']); ?></td>
        <td><?php echo $this->Form->control('kashmiri_mig', ['label' => 'Kashmiri Migrant', 'empty' => ['select' => 'Select'],  'options' => ['yes' => 'Yes', 'no' => 'No'], 'type' => 'select' , 'id' => "id_wardofdef", 'maxlength'=>'100']); ?></td>
    </tr>
    <tr>
        <td><?php echo $this->Form->control("state_id", ['label' => 'State of Domicile', 'empty' => ['select' => 'Select'],  'options' => $states, 'type' => 'select' , 'id' => "id_state_id", 'maxlength'=>'100']); ?></td>
        <td><?php echo $this->Form->control('aadhar_no', ['label' => 'Aadhar No. (xxxx xxxx xxxx)', 'maxlength'=>'100']); ?></td>
    </tr>
</table>
<label>Details of Qualifying Examination: </label>
<table>
    <tr>
        <td><?php echo $this->Form->control('qualif_degree', ['label' => 'Degree (e.g. B.Sc./B.A/)', 'maxlength'=>'100', 'placeholder' => 'B.Sc./B.A/']); ?></td>
        <td><?php echo $this->Form->control('qualif_maj_subjects', ['label' => 'Major Subjects', 'maxlength'=>'100']); ?></td>
        <td><?php echo $this->Form->control('qualif_result_declared', ['label' => ['text' => 'Result Declared', 'class' => 'labelsp'], 'empty' => ['select' => 'Select'],  'options' => ['yes' => 'Yes', 'no' => 'No'], 'type' => 'select' , 'id' => "id_result_dec"]); ?></td>
        <td><?php echo $this->Form->control('qualif_marks_obtained', ['label' => 'Marks Obtained', 'maxlength'=>'100']); ?></td>
        <td><?php echo $this->Form->control('qualif_total_marks', ['label' => 'Total Marks', 'maxlength'=>'100']); ?></td>
    </tr>
</table>
<table>
    <tr>
        <td colspan="4"><?php echo $this->Form->control('valid_gate_gpat', ['label' => 'For M.Tech./M.Pharm. Candidates Only. Do you have valid GATE/GPAT:', 'empty' => ['select' => 'Select'],  'options' => ['yes' => 'Yes', 'no' => 'No'], 'type' => 'select' , 'id' => "id_validggp", 'maxlength'=>'100']); ?></td>
    </tr>
    <tr>
        <td><?php echo $this->Form->control('ggp_exam', ['label' => 'Examination', 'maxlength'=>'100', 'empty' => ['select' => 'Select'],  'options' => ['GATE' => 'GATE', 'GPAT' => 'GPAT'], 'type' => 'select' , 'id' => "id_ggpexam" ]); ?></td>
        <td><?php echo $this->Form->control('ggp_roll_no', ['label' => 'Roll Number', 'maxlength'=>'100']); ?></td>
        <td><?php echo $this->Form->control('ggp_year_passing', ['label' => 'Year of Passing (YYYY)', 'maxlength'=>'100']); ?></td>
        <td><?php echo $this->Form->control('ggp_marksobtained_rank', ['label' => 'Marks Obtained/Rank', 'maxlength'=>'100']); ?></td>
    </tr>
</table>
<label>Declaration: </label>
<ol>
    <li>I declare that the above information is true and correct to the best of my knowledge. If found incorrect at any time, my candidature can be cancelled.</li>
    <li>I undertake that i will abide by the rules and regulations of Central University of Punjab, Bathinda.</li>
    <li>I understand that my admission is subject to fulfilling the eligibility criteria, which I have read carefully, and I will be fully responsible for any choices made by me during the counselling and admission process. My candidature can be cancelled any time, in case i am found not eligible at any moment.</li>
</ol>

<?php echo $this->Form->button(__('Save Form')); 
      echo $this->Form->end();  ?>
</div>

<script>
    $(document).ready(function(){
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