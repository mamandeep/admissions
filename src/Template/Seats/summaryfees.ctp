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
<table>
    <tr>
        <td><label>Total Number of Seats: </label> <?= $totalseats ?></td>
        <td><label>Seats Filled: </label> <?= $seatsfilled ?></td>
        <td><label>Seats Vacant: </label> <?= $seatsvacant ?></td>
    </tr> 
</table>
<table>
<tr>
<th rowspan="2">S.No.</th>
<th rowspan="2">Name of the Programme</th>
<th rowspan="2">Total Seats</th>
<th colspan="4">Seats Filled</th>
<th rowspan="2">Total Filled</th>
<th colspan="4">Vancant Seats</th>
<th rowspan="2">Total Vacant</th>
</tr>
<tr>
<th>Open</th>
<th>SC</th>
<th>ST</th>
<th>OBC</th>
<th>Open</th>
<th>SC</th>
<th>ST</th>
<th>OBC</th>
</tr>
<?php $count = 1; foreach($summary as $programme) { ?>
<tr>
    <td><?= $count++ ?></td>
    <td><?= $programme['p_name'] ?></td>
    <td><?= $programme['Total_seats'] ?></td>
    <td><?= $programme['Open_filled'] ?></td>
    <td><?= $programme['SC_filled'] ?></td>
    <td><?= $programme['ST_filled'] ?></td>
    <td><?= $programme['OBC_filled'] ?></td>
    <td><?= ($programme['Open_filled'] + $programme['SC_filled'] + $programme['ST_filled'] + $programme['OBC_filled'] ) ?></td>
    <td><?= $programme['Open_vacant'] ?></td>
    <td><?= $programme['SC_vacant'] ?></td>
    <td><?= $programme['ST_vacant'] ?></td>
    <td><?= $programme['OBC_vacant'] ?></td>
    <td><?= ($programme['Open_vacant'] + $programme['SC_vacant'] + $programme['ST_vacant'] + $programme['OBC_vacant'] ) ?></td>
</tr>
<?php } ?>
</table>