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
<?php if(isset($lockseatOpen) && $lockseatOpen === true && count($rankings) > 0) { ?>
<label style="font-size: 16px; text-align: center;">Tentative Merit Position</label>
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
<label>> <span style="color: green;">On 19th June, 2017 between 6 A.M. to 3 P.M. each candidate will be required to lock any one seat out of the preferences already filled by her/him. </span> One candidate can <span style="text-decoration: underline;">Lock only One Programme</span> in each round and his/her name will not be considered in other programmes in merit lists of that round. In the next round, she/he can again lock same/different preference and so on.</label>
<div align="center"><?php echo $this->Html->link(
            'Go To Lock Seat',
            '/seats/lockseat',
            ['class' => 'button btn btn-success']
    ); ?>
</div>
</div>
<br/>
<?php } else if(count($rankings) > 0) { ?>
 <div>Lock Seat is closed at this time.</div>
<?php } else if (count($rankings) === 0) { ?>
<div>Position not available at this time. Please check again later.</div>
<?php } ?> 