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
<label style="font-size: 16px; text-align: center;">Choice Lock-Selection of One Programme for Locking</label>
<div>Based on the information provided by you and all the other candidates in the counselling form, following is your merit position in the individual merit lists of the programmes already selected by you:</div>
<ul>
    <?php $displayChoice = false; 
          $category = 0;
          $category_name = '';
        foreach ($rankings as $seat): 
        echo "<li>\"" . $seat->rank . "\" place in " .  $seat->programme->name . " - " . $seat->category->type . " Category </li>"; 
        if(!$displayChoice) {
            $displayChoice = (strcmp($seat->category->type, "SC") === 0 || strcmp($seat->category->type, "ST") === 0 || strcmp($seat->category->type, "OBC") === 0  ) ? true : false;
            $category_name = ($displayChoice === true) ? $seat->category->type : '';
            $category = ($displayChoice === true) ? $seat->candidate_category : 0;
        }
    endforeach; ?>
</ul>
<div>Please note that above information is for information only and to assist you in making an informed decision. Seats will be offered based on the merit list to be prepared out of the choices locked by the candidates in each round.</div>
<label>> Each candidate can <span style="text-decoration: underline;">Lock only One Programme</span> in each round and his/her name will not be considered in other programmes in merit lists of that round. In the next round, she/he can again lock same/different preference and so on.</label>
<?php 
     echo $this->Form->create($lockedSeat, [
        'url' => ['controller' => 'seats', 'action' => 'lockseat']
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

    <?php   $i=0;foreach ($rankings as $seat): 
            if($displayChoice === true && strcmp($seat->category->type, "Open") === 0) { 
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
        <td> 
            <?= $seat->programme->name ?>
        </td>
        <?php if($displayChoice === true) { ?>
        <td> 
            <?php echo $this->Form->control("eligible_for_open[]", ['selected' => ($seat->id === $lockedSeat['rank_id']) ? $lockedSeat['eligible_for_open'] : 'yes', 'label' => false,  'options' => [ 'yes' => 'Yes', 'no' => 'No'], 'type' => 'select' , 'id' => "id_eligible_for_open", 'maxlength'=>'100']); ?>
        </td>
        <td>
            <?php echo $this->Form->control("category_pref[]", ['label' => false,  'options' => [ 1 => 'Open',  $category => $category_name ], 'type' => 'select' , 'id' => "id_category_pref", 'maxlength'=>'100']); 
                  echo $this->Form->input(($seat->id . "_assoc"), array('type' => 'hidden', 'value' => $openRankId)); ?>
        </td>
        <?php } ?>
    </tr>
    <?php $i++; endforeach; ?>
</table>
<br/>
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
<br/>
<div style="text-align: center"><?php echo $this->Form->button(__('Submit')); ?></div>
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
