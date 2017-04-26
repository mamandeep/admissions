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
                      echo $this->Form->control("$count.testpaper_id", ['disabled' => 'disabled', 'label' => false, 'options' => $optionsarr, 'type' => 'select' , 'id' => "{$count}_test_paper_code", array_keys($optionsarr)[0]]);
                  }
                  else {
                      echo $this->Form->control("$count.testpaper_id", ['disabled' => 'disabled', 'label' => false, 'options' => $optionsarr, 'type' => 'select' , 'id' => "{$count}_test_paper_code"]); 
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
    window.onload = function(e){ 
        var data = [];
        var temp = [];
        var count = 0;
        <?php foreach($this->request->session()->read('papercodemapping') as $key => $value) { 
                echo "temp = [];";
                foreach($value as $key2 => $value2) {
                    echo "temp.push({'{$key2}': '{$value2}'});";
                }
                echo "data[{$key}]=temp;";
            } ?>

        //console.log(data);
        var elem = document.getElementById('0_test_paper_code');
        if(elem.addEventListener) {
            $('#0_test_paper_code').change(function() {
                    var selectedValue = $(this).val();
                    var optionsStr = "";
                    //var programmeElem = $('#0_programmes');
                    for(var i=0; i < data.length; i++) {
                        for(var j=0; j< data[i].length; j++) {
                            if(data[i][j].TestpapersProgrammes__testpaper_id && selectedValue == data[i][j].TestpapersProgrammes__testpaper_id) {
                                var text, value;
                                for(var k=0; k<data[i].length; k++) {
                                   
                                    if(data[i][k].TestpapersProgrammes__programme_id) {
                                        value = data[i][k].TestpapersProgrammes__programme_id;
                                    }
                                    if(data[i][k].Programmes__name) {
                                        text = data[i][k].Programmes__name;
                                    }
                                }
                                optionsStr += '<option value="' + value + '">' +  text + '</option>';
                                continue;
                            }
                        }
                    }
                    $('#0_programmes').find('option').remove().end().append(optionsStr);
            });
        }
        else if (elem.attachEvent) { // IE DOM
            elem.attachEvent("onchange", function() {
                        var selectedValue = elem.options[elem.selectedIndex].value;
                        var optionsStr = "";
                        for(var i=0; i < data.length; i++) {
                            for(var j=0; j< data[i].length; j++) {
                                if(data[i][j].TestpapersProgrammes__testpaper_id && selectedValue == data[i][j].TestpapersProgrammes__testpaper_id) {
                                    var text, value;
                                    for(var k=0; k<data[i].length; k++) {

                                        if(data[i][k].TestpapersProgrammes__programme_id) {
                                            value = data[i][k].TestpapersProgrammes__programme_id;
                                        }
                                        if(data[i][k].Programmes__name) {
                                            text = data[i][k].Programmes__name;
                                        }
                                    }
                                    optionsStr += '<option value="' + value + '">' +  text + '</option>';
                                    continue;
                                }
                            }
                        }
                        $('#0_programmes').find('option').remove().end().append(optionsStr);
            });
        }
        elem.disabled  = false;
        var elem = document.getElementById('1_test_paper_code');
        if(elem.addEventListener) {
            $('#1_test_paper_code').change(function() {
                var selectedValue = $(this).val();
                var optionsStr = "";
                for(var i=0; i < data.length; i++) {
                            for(var j=0; j< data[i].length; j++) {
                                if(data[i][j].TestpapersProgrammes__testpaper_id && selectedValue == data[i][j].TestpapersProgrammes__testpaper_id) {
                                    var text, value;
                                    for(var k=0; k<data[i].length; k++) {

                                        if(data[i][k].TestpapersProgrammes__programme_id) {
                                            value = data[i][k].TestpapersProgrammes__programme_id;
                                        }
                                        if(data[i][k].Programmes__name) {
                                            text = data[i][k].Programmes__name;
                                        }
                                    }
                                    optionsStr += '<option value="' + value + '">' +  text + '</option>';
                                    continue;
                                }
                            }
                        }
                $('#1_programmes').find('option').remove().end().append(optionsStr);
            });
        }
        else if (elem.attachEvent) { // IE DOM
            elem.attachEvent("onchange", function() {
                        var selectedValue = elem.options[elem.selectedIndex].value;
                        var optionsStr = "";
                        for(var i=0; i < data.length; i++) {
                            for(var j=0; j< data[i].length; j++) {
                                if(data[i][j].TestpapersProgrammes__testpaper_id && selectedValue == data[i][j].TestpapersProgrammes__testpaper_id) {
                                    var text, value;
                                    for(var k=0; k<data[i].length; k++) {

                                        if(data[i][k].TestpapersProgrammes__programme_id) {
                                            value = data[i][k].TestpapersProgrammes__programme_id;
                                        }
                                        if(data[i][k].Programmes__name) {
                                            text = data[i][k].Programmes__name;
                                        }
                                    }
                                    optionsStr += '<option value="' + value + '">' +  text + '</option>';
                                    continue;
                                }
                            }
                        }
                        $('#1_programmes').find('option').remove().end().append(optionsStr);
            });
        }
        elem.disabled  = false;
        var elem = document.getElementById('2_test_paper_code');
        if(elem.addEventListener) {
            $('#2_test_paper_code').change(function() {
                    var selectedValue = $(this).val();
                    var optionsStr = "";
                    for(var i=0; i < data.length; i++) {
                            for(var j=0; j< data[i].length; j++) {
                                if(data[i][j].TestpapersProgrammes__testpaper_id && selectedValue == data[i][j].TestpapersProgrammes__testpaper_id) {
                                    var text, value;
                                    for(var k=0; k<data[i].length; k++) {

                                        if(data[i][k].TestpapersProgrammes__programme_id) {
                                            value = data[i][k].TestpapersProgrammes__programme_id;
                                        }
                                        if(data[i][k].Programmes__name) {
                                            text = data[i][k].Programmes__name;
                                        }
                                    }
                                    optionsStr += '<option value="' + value + '">' +  text + '</option>';
                                    continue;
                                }
                            }
                        }
                    $('#2_programmes').find('option').remove().end().append(optionsStr);
            });
        }
        else if (elem.attachEvent) { // IE DOM
            elem.attachEvent("onchange", function() {
                        var selectedValue = elem.options[elem.selectedIndex].value;
                        var optionsStr = "";
                        for(var i=0; i < data.length; i++) {
                            for(var j=0; j< data[i].length; j++) {
                                if(data[i][j].TestpapersProgrammes__testpaper_id && selectedValue == data[i][j].TestpapersProgrammes__testpaper_id) {
                                    var text, value;
                                    for(var k=0; k<data[i].length; k++) {

                                        if(data[i][k].TestpapersProgrammes__programme_id) {
                                            value = data[i][k].TestpapersProgrammes__programme_id;
                                        }
                                        if(data[i][k].Programmes__name) {
                                            text = data[i][k].Programmes__name;
                                        }
                                    }
                                    optionsStr += '<option value="' + value + '">' +  text + '</option>';
                                    continue;
                                }
                            }
                        }
                        $('#2_programmes').find('option').remove().end().append(optionsStr);
            });
        }
        elem.disabled  = false;
        if(elem.addEventListener) {
            $('#0_test_paper_code').change();
            $('#1_test_paper_code').change();
            $('#2_test_paper_code').change();
        }
        else if (elem.attachEvent) {
            $('#0_test_paper_code').onchange();
            $('#1_test_paper_code').onchange();
            $('#2_test_paper_code').onchange();
        }
    }
</script>