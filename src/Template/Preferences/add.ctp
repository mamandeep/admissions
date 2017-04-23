<div id="loadingDiv" class="overlay">
    <div id="loading-img"></div>
</div>
<?php use Cake\Routing\Router; ?>
<h1>Preferences</h1>
<p>Only 3 Preferences are allowed</p>
<?php echo $this->Form->create($preferences); ?>
<table>
    <tr>
        <th>S. No.</th>
        <th>Candidate Name</th>
        <th>Exam ID</th>
        <th>Programme ID</th>
        <th>Marks A</th>
        <th>Marks B</th>
        <th>Total Marks</th>
    </tr>
    <?php $count = 0; 
            foreach ($preferences as $i): //debug($i); ?>
    <tr>
        <td><?php echo $this->Form->control("$count.id", ['type' => 'hidden']); echo $count+1; ?></td>
        <td><?php echo $this->Form->control("$count.candidate.name") ?></td>
        <td><?php   $optionsarr = []; $uniqueOptions = []; $poptionsarr = [];
                    foreach ($this->request->session()->read('papercodemapping') as $map) {  
                        if(!in_array($map['TestpapersProgrammes__testpaper_id'], $uniqueOptions)) {
                            $optionsarr[$map['TestpapersProgrammes__testpaper_id']] =  $map['Testpapers__code'];
                        }
                        $uniqueOptions[] = $map['TestpapersProgrammes__testpaper_id'];
                        if(count($optionsarr) > 0 && array_keys($optionsarr)[0] == $map['TestpapersProgrammes__testpaper_id']) {
                            $poptionsarr[$map['TestpapersProgrammes__programme_id']] = $map['Programmes__name'];
                        }
                  }
                  echo $this->Form->control("$count.programme.test_paper_code", ['options' => $optionsarr, 'type' => 'select' , 'id' => "{$count}_test_paper_code", 'default' => array_keys($optionsarr)[0]]); ?></td>
        <td><?php echo $this->Form->control("$count.programme_id", ['options' => $poptionsarr, 'type' => 'select' ,'id' => "{$count}_programmes"]); ?></td>
        <td><?php echo $this->Form->control("$count.marks_A") ?></td>
        <td><?php echo $this->Form->control("$count.marks_B") ?></td>
        <td><?php echo $this->Form->control("$count.marks_total") ?></td>
    </tr>
    <?php $count++; endforeach; ?>
</table>
<?php
    echo $this->Form->button(__('Save Marks'));
    echo $this->Form->end();
?>
<script>
    $(function() {
	$('#0_test_paper_code').change(function() {
		var selectedValue = $(this).val();
                var targeturl = "<?php echo Router::url(array('controller'=>'preferences','action'=>'viewresult'));?>" //'_ext' => 'xml'
		$.ajax({
			type: 'GET',
			url: targeturl,
                        cache: false,
                        data: "id="+selectedValue,
			dataType: 'xml',
			success: function(response) {
                                //console.log(response);
                                var optionsStr = "";
                                $(response).find("data").each(function() {
                                    //console.log($(this).text());
                                    optionsStr = $(this).text();
                                });
				if (optionsStr) {
                                    $('#0_programmes').find('option').remove().end().append(optionsStr);
				}
			},
			error: function(e) {
				alert("An error occurred: " + e.responseText.message);
				console.log(e);
			}
		});
	});
        $('#1_test_paper_code').change(function() {
		var selectedValue = $(this).val();
                var targeturl = "<?php echo Router::url(array('controller'=>'preferences','action'=>'viewresult'));?>" //'_ext' => 'xml'
		$.ajax({
			type: 'GET',
			url: targeturl,
                        cache: false,
                        data: "id="+selectedValue,
			dataType: 'xml',
			success: function(response) {
                                //console.log(response);
                                var optionsStr = "";
                                $(response).find("data").each(function() {
                                    //console.log($(this).text());
                                    optionsStr = $(this).text();
                                });
				if (optionsStr) {
                                    $('#1_programmes').find('option').remove().end().append(optionsStr);
				}
			},
			error: function(e) {
				alert("An error occurred: " + e.responseText.message);
				console.log(e);
			}
		});
	});
        $('#2_test_paper_code').change(function() {
		var selectedValue = $(this).val();
		//var targeturl = $(this).attr('rel') + '?id=' + selectedValue;
                var targeturl = "<?php echo Router::url(array('controller'=>'preferences','action'=>'viewresult'));?>" //'_ext' => 'xml'
		$.ajax({
			type: 'GET',
			url: targeturl,
                        cache: false,
                        data: "id="+selectedValue,
			dataType: 'xml',
			success: function(response) {
                                //console.log(response);
                                var optionsStr = "";
                                $(response).find("data").each(function() {
                                    //console.log($(this).text());
                                    optionsStr = $(this).text();
                                });
				if (optionsStr) {
                                    $('#2_programmes').find('option').remove().end().append(optionsStr);
				}
			},
			error: function(e) {
				alert("An error occurred: " + e.responseText.message);
				console.log(e);
			}
		});
	});
});
</script>