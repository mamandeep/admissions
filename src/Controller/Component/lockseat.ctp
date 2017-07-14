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
<script> 
        var eligible_values = [];
         var category_pref_values = [];
</script>
<?php if(isset($lockseatOpen) && $lockseatOpen === true) { ?>
<label style="font-size: 16px; text-align: center;">Choice Lock-Selection of One Programme for Locking</label>
<div>Based on the information provided by you and all the other candidates in the counselling form, following is your merit position in the individual merit lists of the programmes already selected by you, this information is purely provisional and subject to change after verification of the candidate claims:</div>
<ul>
    <?php $displayChoice = false; 
          $category = 0;
          $category_name = '';
        foreach ($rankings as $seat): 
        echo "<li><strong>\"" . $seat->rank . "\" place</strong> in <strong>" .  $seat->programme->name . "</strong> - <strong>" . $seat->category->type . "</strong> Category </li>"; 
        if(!$displayChoice) {
            $displayChoice = (strcmp($seat->category->id, "3") === 0 || strcmp($seat->category->id, "4") === 0 || strcmp($seat->category->id, "5") === 0  ) ? true : false;
            $category_name = ($displayChoice === true) ? $seat->category->type : '';
            $category = ($displayChoice === true) ? $seat->candidate_category : 0;
        }
    endforeach; ?>
</ul>
<div>Please note that above information is for information only and to assist you in making an informed decision. Seats will be offered based on the merit list to be prepared out of the choices locked by the candidates in each round. Candidates of M-Tech CST may please note that above position is likely to change after verification of GATE scores.</div>
<label>> Each candidate can <span style="text-decoration: underline;">Lock only One Programme</span> in each round and his/her name will not be considered in other programmes in merit lists of that round. In the next round, she/he can again lock same/different preference and so on.</label>
<div>
    <p style="font-weight: bold; color: green;">The currently locked programme is:  
    <?php         $str = '';
        $seatLocked = false; 
        foreach ($rankings as $seat) {
        //debug($seat); debug($seat->id);debug($lockedSeat);
        //debug(isset($lockedSeat) && $seat->id === $lockedSeat->rank_id);
        if(isset($lockedSeat) && $seat->id === $lockedSeat->rank_id) { 
                $str .= " <strong>" . $seat->programme->name . "</strong>";
                //$str .= ";Category: <strong>" . $seat->category->type . "</strong>";
                //$str .= ";Merit in Programme: <strong>" . $seat->rank . "</strong>";
                $seatLocked = true;
        } 
    }
    if($seatLocked === false) {
        $str .= "<strong>Nil</strong>";
    }
    echo $str;
?>  </p>
</div>
<?php 
     echo $this->Form->create($lockedSeat, [
        'url' => ['controller' => 'seats', 'action' => 'lockseat'],
        'id' => 'lockseat_form'
     ]); 
?>
<table>
    <tr>
        <th><label>Lock Seat</label></th>
        <th><label>Programme Name</label></th>
        <?php if($displayChoice === true) { ?>
        <th><label>Eligible for Open Category*</label></th>
        <th><label>Category Preference*</label></th>
        <?php } ?>
    </tr>

<!-- Here's where we loop through our $articles query object, printing out article info -->

    <?php   $index=0;$i=0;foreach ($rankings as $seat): 
            if($displayChoice === true && strcmp($seat->category->id, "1") === 0) { 
                $openRankId = $seat->id;
                continue;
            }?>
    <tr>
        <td><?php //debug($seat); debug($seat->id); debug($lockedSeat['rank_id']);
                $options = array(array('value' => $seat->id, 'text' => '' ));
                echo $this->Form->radio(
                    "selected_course",
                    $options, [
                    'hiddenField' => false ]
                ); ?></td>
        <td> <input type="hidden" name="prog_name" value="<?= $seat->programme->name ?>"/>
            <?= $seat->programme->name ?>
        </td>
        <?php if($displayChoice === true) { ?>
        <td> 
            <?php echo $this->Form->control("eligible_for_open[]", ['label' => false,  'options' => [ 'yes' => 'Yes', 'no' => 'No'], 'type' => 'select' , 'id' => "${index}" . "_eligible_for_open", 'maxlength'=>'100']); ?>
            <script> eligible_values.push('<?php echo ($seat->id === $lockedSeat['rank_id']) ? $lockedSeat['eligible_for_open'] : 'yes'; ?>');</script>
        </td>
        <td>
            <?php echo $this->Form->control("category_pref[]", ['label' => false,  'options' => [ 1 => 'Open',  $category => $category_name ], 'type' => 'select' , 'id' => "${index}" . "_category_pref", 'maxlength'=>'100']); $index++;
                  echo $this->Form->input(($seat->id . "_assoc"), array('type' => 'hidden', 'value' => $openRankId)); ?>
            <script> category_pref_values.push('<?php echo ($seat->id === $lockedSeat['rank_id']) ? $lockedSeat['category_pref'] : '1'; ?>');</script>
        </td>
        <?php } ?>
    </tr>
    <?php $i++; endforeach; ?>
</table>
<br/>
<?php if($displayChoice === true) { ?>
<label>*Note: </label>
<div>Candidates belonging to SC/ST/OBC category who fulfill admission eligibility 
criteria without any relaxation in educational qualifications are eligible for consideration
in both Open Category and their respective category merit lists. So, in case they 
are otherwise eligible for seat allocation in both the categories, they have the 
option to chose in "Category Preference" column whether they want to be 
considered first for Open category or their respective category.</div>
<div>Candidates belonging to SC/ST/OBC category but becoming eligible after getting 
relaxation of marks in minimum eligibility criteria can not claim seat under 
Open Category. So they need to select 'No' under Eligible for Open Category column. 
In case candidate claimed seat under Open category, but does not fulfill eligibility 
criteria without relaxation, his/her seat will be automatically cancelled.</div>
<?php } ?>
<br/>
<div style="text-align: center"><?php echo $this->Form->button(__('Submit')); ?></div>
<?php echo $this->Form->end(); ?>
<br/>

<script>
$(document).ready(function(){
    
    if(eligible_values[0]) $("#0_eligible_for_open").val(eligible_values[0]);
    if(eligible_values[1]) $("#1_eligible_for_open").val(eligible_values[1]);
    if(eligible_values[2]) $("#2_eligible_for_open").val(eligible_values[2]);
    if(category_pref_values[0]) $("#0_category_pref").val(category_pref_values[0]);
    if(category_pref_values[1]) $("#1_category_pref").val(category_pref_values[1]);
    if(category_pref_values[2]) $("#2_category_pref").val(category_pref_values[2]);
    
    $("#lockseat_form").submit(function(e){
        var prog_name = '';
        var eligible_for_open = '';
        var category_pref = '';
        var eligibility = [];
        <?php foreach($rankings as $seat) { 
                $eligibility = addslashes($seat->programme->eligibility);
                echo "eligibility.push({'{$seat->programme->name}': '{$eligibility}'});";
            } ?>
        $('input[name=selected_course]:checked').closest('tr').find('input[name=prog_name]').each(function(){
            prog_name = $(this).val();
        });
        $('input[name=selected_course]:checked').closest('tr').find('select[name=eligible_for_open\\[\\]]').each(function(){
            eligible_for_open = $(this).val();
        });
        $('input[name=selected_course]:checked').closest('tr').find('select[name=category_pref\\[\\]] :selected').each(function(){
            category_pref = $(this).text();
        });
        eligibility_str = '';
        for(var i=0; i< eligibility.length ; i++) {
            if(eligibility[i][prog_name]) {
                eligibility_str = eligibility[i][prog_name];
            }
        }
        if (!confirm("Please ensure that you fulfill the following eligibility conditions for " + prog_name + " before locking: \n\n " + eligibility_str + "."))
        {
            e.preventDefault();
            return;
        }
    });
});
</script>
<?php } else { ?>
 <div>Lock Seat is closed at this time.</div>
<?php } ?>