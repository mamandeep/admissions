<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Please Wait .. </h4>
      </div>
      <div class="modal-body">
        <p>Fetching Programmes for the selected paper code.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<?php use Cake\Routing\Router; ?>
<h1>Preferences</h1>
<p>Only 3 Preferences are allowed</p>
<?php echo $this->Form->create($preferences); ?>
<table>
    <tr>
        <th width="5%">S. No.</th>
        <th width="10%">CUCET TEST PAPER CODE</th>
        <th width="30%">Programme Name</th>
        <th width="8%">Marks A</th>
        <th width="8%">Marks B</th>
        <th width="8%">Total Marks</th>
    </tr>
    <?php $count = 0; $default  = (count($preferences) === 0) ? true : false;
            for ($count=0; $count<3; $count++) { //debug($i); ?>
    <tr>
        <td><?php echo $this->Form->control("$count.id", ['type' => 'hidden']); 
                  echo $this->Form->control("$count.candidate_id", ['type' => 'hidden']);
                  echo $count+1; ?></td>
        <td><?php   $optionsarr = []; $uniqueOptions = []; $poptionsarr = []; //debug($this->request->session()->read('papercodemapping'));
                    foreach ($this->request->session()->read('papercodemapping') as $map) {
                        if(!in_array($map['TestpapersProgrammes__testpaper_id'], $uniqueOptions)) {
                            $optionsarr[$map['TestpapersProgrammes__testpaper_id']] =  $map['Testpapers__code'];
                        }
                        $uniqueOptions[] = $map['TestpapersProgrammes__testpaper_id'];
                        if(count($optionsarr) > 0 && array_keys($optionsarr)[0] == $map['TestpapersProgrammes__testpaper_id']) {
                            $poptionsarr[$map['TestpapersProgrammes__programme_id']] = $map['Programmes__name'];
                        }
                  }
                  if($default == true) {
                      echo $this->Form->control("$count.testpaper_id", ['label' => false, 'options' => $optionsarr, 'type' => 'select' , 'id' => "{$count}_test_paper_code", array_keys($optionsarr)[0]]);
                  }
                  else {
                      echo $this->Form->control("$count.testpaper_id", ['label' => false, 'options' => $optionsarr, 'type' => 'select' , 'id' => "{$count}_test_paper_code"]); 
                  } ?></td>
        <td><?php echo $this->Form->control("$count.programme_id", ['label' => false, 'options' => $poptionsarr, 'type' => 'select' ,'id' => "{$count}_programmes"]); ?></td>
        <td><?php echo $this->Form->control("$count.marks_A", ['label' => false]) ?></td>
        <td><?php echo $this->Form->control("$count.marks_B", ['label' => false]) ?></td>
        <td><?php echo $this->Form->control("$count.marks_total", ['label' => false]) ?></td>
    </tr>
    <?php } ?>
</table>
<?php
    echo $this->Form->button(__('Save Preferences'));
    echo $this->Form->end();
?>
<script>
    
    $(function() {
	$('#0_test_paper_code').change(function() {
		var selectedValue = $(this).val();
                var targeturl = "<?php echo Router::url(array('controller'=>'preferences','action'=>'viewresult'));?>" //'_ext' => 'xml'
                $("#myModal").modal('show');
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
                                $("#myModal").modal('hide');
			},
			error: function(e) {
                                $("#myModal").modal('hide');
				alert("An error occurred: " + e.responseText.message);
				console.log(e);
			}
		});
	});
        $('#1_test_paper_code').change(function() {
		var selectedValue = $(this).val();
                var targeturl = "<?php echo Router::url(array('controller'=>'preferences','action'=>'viewresult'));?>" //'_ext' => 'xml'
                $("#myModal").modal('show');
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
                                $("#myModal").modal('hide');
			},
			error: function(e) {
                                $("#myModal").modal('hide');
				alert("An error occurred: " + e.responseText.message);
				console.log(e);
			}
		});
	});
        $('#2_test_paper_code').change(function() {
		var selectedValue = $(this).val();
		//var targeturl = $(this).attr('rel') + '?id=' + selectedValue;
                var targeturl = "<?php echo Router::url(array('controller'=>'preferences','action'=>'viewresult'));?>" //'_ext' => 'xml'
                $("#myModal").modal('show');
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
                                $("#myModal").modal('hide');
			},
			error: function(e) {
                                $("#myModal").modal('hide');
				alert("An error occurred: " + e.responseText.message);
				console.log(e);
			}
		});
	});
});

$(function() {
        $('#0_test_paper_code').change();
        $('#1_test_paper_code').change();
        $('#2_test_paper_code').change();
    });
</script>