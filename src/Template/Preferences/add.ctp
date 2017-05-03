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
<?php use Cake\Routing\Router; ?>
<h1>Preferences</h1>
<p style="color: crimson;">Minimum of 1 preference has to be filled. Maximum of 3 preferences are allowed. Please select the checkbox to submit the other preferences.</p>
<?php echo $this->Form->create($preferences, ['name' => 'preferences_form']); ?>
<table>
    <tr>
        <th width="5%">S. No.</th>
        <th width="5%">Select</th>
        <th width="10%">CUCET TEST PAPER CODE</th>
        <th width="30%">Programme Name</th>
        <th width="8%">Marks A</th>
        <th width="8%">Marks B</th>
        <th width="8%">Total Marks</th>
    </tr>
    <?php $count = 0; $default  = (count($preferences) === 0) ? true : false;
            for ($count=0; $count<3; $count++) { //debug($preferences); ?>
    <tr>
        <td><?php echo $this->Form->control("$count.id", ['type' => 'hidden']); 
                  echo $this->Form->control("$count.candidate_id", ['type' => 'hidden']);
                  echo $count+1; ?></td>
        <td><?php $options = array(array('value' => $count, 'text' => '' ));
                  $checked = ($count == 0) ? true : (!empty($preferences[$count]['selected'])) ? true : false;
                  $disabled = ($count == 0) ? true : false;
                  echo $this->Form->input( "$count.selected", array(
                      'label' => false,
                      'type'=>'checkbox',
                      'id' => "{$count}_selectbox_id",
                      'value' => $count,
                      'checked'=>$checked,
                      'disabled' => $disabled,
                      'hiddenField' => false,
                      'templates' => [
                            'inputContainer' => '{{content}}'
                       ]));
                  /*echo $this->Form->checkbox(
                    "$count.selected",
                    $options, [
                    'hiddenField' => false, 'id' => "{$count}_checkbox",  'checked' => $checked , 'readonly' => $readonly]
                  );*/ ?></td>
        <td><?php   $optionsarr = []; $uniqueOptions = []; $poptionsarr = []; //debug($this->request->session()->read('papercodemapping'));
                    $optionsarr['0'] = 'Select';
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
                        echo $this->Form->input(
                            "$count.testpaper_id",
                            [  'disabled' => 'disabled',
                               'label' => false,
                               'options' => $optionsarr,
                               'type' => 'select',
                               //'maxlength' => 10,
                               'id' => "{$count}_test_paper_code",
                               'empty' => '0',
                               'class' => 'form-control'
                            ]
                        );
                      //echo $this->Form->control("$count.testpaper_id", ;
                  }
                  else {
                        echo $this->Form->input(
                            "$count.testpaper_id",
                            [  
                               'options' => $optionsarr,
                               'type' => 'select',
                               //'maxlength' => 10,
                               'id' => "{$count}_test_paper_code",
                               'class' => 'form-control',
                               'disabled' => 'disabled',
                               'label' => false,
                            ]
                        );
                      //echo $this->Form->control("$count.testpaper_id", ['disabled' => 'disabled', 'label' => false, 'options' => $optionsarr, 'type' => 'select' , 'maxlength' => 10 , 'id' => "{$count}_test_paper_code"]); 
                  } ?></td>
        <td><?php echo $this->Form->control("$count.programme_id", ['label' => false, 'options' => $poptionsarr, 'type' => 'select' , 'maxlength' => 10, 'id' => "{$count}_programmes"]); ?></td>
        <td><?php echo $this->Form->control("$count.marks_A", ['label' => false, 'maxlength' => 10]) ?></td>
        <td><?php echo $this->Form->control("$count.marks_B", ['label' => false, 'maxlength' => 10]) ?></td>
        <td><?php echo $this->Form->control("$count.marks_total", ['label' => false, 'maxlength' => 10]) ?></td>
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
                    var optionsStr = "<option value='0'>Select</option>";
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
                        var selectbox = document.getElementById('1_programmes');
                        var i;
                        for(i = selectbox.options.length - 1 ; i >= 0 ; i--)
                        {
                            selectbox.remove(i);
                        }
                        opt.value = 0;
                        opt.innerHTML = 'Select';
                        selectbox.appendChild(opt);
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
                                    opt = document.createElement('option');
                                    opt.value = value;
                                    opt.innerHTML = text;
                                    selectbox.appendChild(opt);
                                    continue;
                                }
                            }
                        }
            });
        }
        elem.disabled  = false;
        var elem = document.getElementById('1_test_paper_code');
        if(elem.addEventListener) {
            $('#1_test_paper_code').change(function() {
                var selectedValue = $(this).val();
                var optionsStr = "<option value='0'>Select</option>";
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
            $('input[name="1[selected]"]').change(function() {
                if(this.checked) {
                    $('select[name="1[testpaper_id]"]').attr('disabled', false);
                    $('select[name="1[programme_id]"]').attr('disabled', false);
                    $('input[name="1[marks_A]"]').attr('disabled', false);
                    $('input[name="1[marks_B]"]').attr('disabled', false);
                    $('input[name="1[marks_total]"]').attr('disabled', false);
                }
                else {
                    $('select[name="1[testpaper_id]"]').attr('disabled', true);
                    $('select[name="1[programme_id]"]').attr('disabled', true);
                    $('input[name="1[marks_A]"]').attr('disabled', true);
                    $('input[name="1[marks_B]"]').attr('disabled', true);
                    $('input[name="1[marks_total]"]').attr('disabled', true);
                }
            });
        }
        else if (elem.attachEvent) { // IE DOM
            elem.attachEvent("onchange", function() {
                        var selectedValue = elem.options[elem.selectedIndex].value;
                        var optionsStr = "<option value='0'>Select</option>";
                        var selectbox = document.getElementById('1_programmes');
                        var i;
                        for(i = selectbox.options.length - 1 ; i >= 0 ; i--)
                        {
                            selectbox.remove(i);
                        }
                        opt.value = 0;
                        opt.innerHTML = 'Select';
                        selectbox.appendChild(opt);
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
                                    opt = document.createElement('option');
                                    opt.value = value;
                                    opt.innerHTML = text;
                                    selectbox.appendChild(opt);
                                    continue;
                                }
                            }
                        }
            });
            document.getElementsByName("1[selected]")[0].onchange(function() {
                if(this.checked) {
                    document.getElementsByName("1[testpaper_id]")[0].setAttribute("disabled", false);
                    document.getElementsByName("1[programme_id]")[0].setAttribute("disabled", false);
                    document.getElementsByName("1[marks_A]")[0].setAttribute("disabled", false);
                    document.getElementsByName("1[marks_B]")[0].setAttribute("disabled", false);
                    document.getElementsByName("1[marks_total]")[0].setAttribute("disabled", false);
                }
                else {
                    document.getElementsByName("1[testpaper_id]")[0].setAttribute("disabled", true);
                    document.getElementsByName("1[programme_id]")[0].setAttribute("disabled", true);
                    document.getElementsByName("1[marks_A]")[0].setAttribute("disabled", true);
                    document.getElementsByName("1[marks_B]")[0].setAttribute("disabled", true);
                    document.getElementsByName("1[marks_total]")[0].setAttribute("disabled", true);
                }
            });
        }
        elem.disabled  = false;
        var elem = document.getElementById('2_test_paper_code');
        if(elem.addEventListener) {
            $('#2_test_paper_code').change(function() {
                    var selectedValue = $(this).val();
                    var optionsStr = "<option value='0'>Select</option>";
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
            $('input[name="2[selected]"]').change(function() {
                if(this.checked) {
                    $('select[name="2[testpaper_id]"]').attr('disabled', false);
                    $('select[name="2[programme_id]"]').attr('disabled', false);
                    $('input[name="2[marks_A]"]').attr('disabled', false);
                    $('input[name="2[marks_B]"]').attr('disabled', false);
                    $('input[name="2[marks_total]"]').attr('disabled', false);
                }
                else {
                    $('select[name="2[testpaper_id]"]').attr('disabled', true);
                    $('select[name="2[programme_id]"]').attr('disabled', true);
                    $('input[name="2[marks_A]"]').attr('disabled', true);
                    $('input[name="2[marks_B]"]').attr('disabled', true);
                    $('input[name="2[marks_total]"]').attr('disabled', true);
                }
            });
        }
        else if (elem.attachEvent) { // IE DOM
            elem.attachEvent("onchange", function() {
                var selectedValue = elem.options[elem.selectedIndex].value;
                var optionsStr = "<option value='0'>Select</option>";
                var selectbox = document.getElementById('1_programmes');
                var i;
                for(i = selectbox.options.length - 1 ; i >= 0 ; i--)
                {
                    selectbox.remove(i);
                }
                var opt = document.createElement('option');
                opt.value = 0;
                opt.innerHTML = 'Select';
                selectbox.appendChild(opt);
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
                            opt = document.createElement('option');
                            opt.value = value;
                            opt.innerHTML = text;
                            selectbox.appendChild(opt);
                            continue;
                        }
                    }
                }
            });
            document.getElementsByName("2[selected]")[0].onchange(function() {
                if(this.checked) {
                    document.getElementsByName("2[testpaper_id]")[0].setAttribute("disabled", false);
                    document.getElementsByName("2[programme_id]")[0].setAttribute("disabled", false);
                    document.getElementsByName("2[marks_A]")[0].setAttribute("disabled", false);
                    document.getElementsByName("2[marks_B]")[0].setAttribute("disabled", false);
                    document.getElementsByName("2[marks_total]")[0].setAttribute("disabled", false);
                }
                else {
                    document.getElementsByName("2[testpaper_id]")[0].setAttribute("disabled", true);
                    document.getElementsByName("2[programme_id]")[0].setAttribute("disabled", true);
                    document.getElementsByName("2[marks_A]")[0].setAttribute("disabled", true);
                    document.getElementsByName("2[marks_B]")[0].setAttribute("disabled", true);
                    document.getElementsByName("2[marks_total]")[0].setAttribute("disabled", true);
                }
            });
        }
        elem.disabled  = false;
        if(elem.addEventListener) {
            $('#0_test_paper_code').change();
            $('#1_test_paper_code').change();
            $('#2_test_paper_code').change(); 
            $('#0_test_paper_code').attr('disabled', false);
            $('#1_test_paper_code').attr('disabled', false);
            $('#2_test_paper_code').attr('disabled', false);
            $('input[name="0[selected]"]').change();
            $('input[name="1[selected]"]').change();
            $('input[name="2[selected]"]').change();
        }
        else if (elem.attachEvent) {
            document.getElementById('0_test_paper_code').onchange();
            document.getElementById('1_test_paper_code').onchange();
            document.getElementById('2_test_paper_code').onchange();
            document.getElementById('0_test_paper_code').setAttribute("disabled", false);
            document.getElementById('1_test_paper_code').setAttribute("disabled", false);
            document.getElementById('2_test_paper_code').setAttribute("disabled", false);
            document.getElementByName('0[selected]')[0].onchange();
            document.getElementByName('1[selected]')[0].onchange();
            document.getElementByName('2[selected]')[0].onchange();
        }
    }
</script>