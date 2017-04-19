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
<h1>Allocated Seats Details</h1>
<table>
    <tr>
        <th>Sr. No.</th>
        <th>Centre ID</th>
        <th>Seat ID</th>
        <th>Candidate ID</th>
    </tr>
     <?php  $count = 1;
        foreach ($seatallocations as $seat): ?>
    <tr>
        <td>
            <?= $count++; ?>
        </td>
        <td>
            <?= $seat['centre_id'] ?>
        </td>
        <td><?php //debug($seat); ?>
            <?= $seat['seat_id'] ?>
        </td>
        <td>
            <?= $seat['candidate_id'] ?>
        </td>
    <tr>
    <?php endforeach; ?>
<table>