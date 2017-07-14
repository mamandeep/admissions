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
<a href="javascript: window.print();" > PRINT </a>
<?php  $count2 = 1; $count3 = 0;
        for( $count3 = 0; $count3 < count($seatallocations); $count3++) { ?>
<?php if($count3 === 0 || $seatallocations[$count3-1]['p_name'] !== $seatallocations[$count3]['p_name']) { ?>
<div align="center" style="font-size: 16px; font-weight: bold; padding-top: 20px; padding-bottom: 3px"><?= $seatallocations[$count3]['p_name'] ?></div>
<table width="100%">
    <tr>
        <th width="10%">Sr. No.</th>
	<th width="20%">CUCET Applicant ID</th>
        <th width="30%">Name</th>
        <th width="10%">Total Marks</th>
	<th width="15%">Candidate Category</th>
	<th width="15%">Seat Category</th>
    </tr>
<?php } ?>     
    <tr>
        <td>
            <?= $count2++ ?>
        </td>
        <td>
            <?= $seatallocations[$count3]['username'] ?>
        </td>
        <td>
            <?= $seatallocations[$count3]['c_name'] ?>
        </td>
	<td>
            <?= $seatallocations[$count3]['marks_total'] ?>
        </td>
        <td>
            <?= $seatallocations[$count3]['c_category'] ?>
        </td>
        <td>
            <?= $seatallocations[$count3]['s_category'] ?>
        </td>
    <tr>
<?php if($count3 === (count($seatallocations) - 1) || $seatallocations[$count3]['p_name'] !== $seatallocations[$count3+1]['p_name']) { $count2 = 1; ?>
</table>
<?php } } ?>
