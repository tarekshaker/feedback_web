<!DOCTYPE html>
<html>
  <head>
    <title>IConnect | Survey</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo base_url(); ?>assets/mobile/style.css" rel="stylesheet">
    <link href="http://localhost/feedback/assets/new/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="<?php echo base_url(); ?>assets/mobile/jquery-1.9.1.min.js" type="text/javascript"></script>
    <style media="screen">
    html {
      overflow:auto;

    height: 100%;
    /*Image only BG fallback*/
    background: url('<?php echo base_url(); ?>assets/mobile/gs.png');
    /*background = gradient + image pattern combo*/
    /*background:
      linear-gradient(rgba(196, 102, 0, 0.2), rgba(155, 89, 182, 0.2)),
      url('assets/mobile/gs.png');*/
    }
    </style>
  </head>
  <body>
  <style media="screen">
    label > input{ /* HIDE RADIO */
      visibility: hidden; /* Makes input not-clickable */
      position: absolute; /* Remove input from document flow */
      padding-right: 40px;
    }
    label > input + img{ /* IMAGE STYLES */
      cursor:pointer;
      width: 40px;
      height: 40px;
      border:2px solid transparent;
    }
  </style>
  <script type="text/javascript">
  $( document ).ready(function() {
    $('input:radio').change(function(){
      var name = $(this).attr('name');
      $('input:radio[name="'+name+'"].excellent').next().attr('src','<?php echo base_url(); ?>assets/excellent.png');
      $('input:radio[name="'+name+'"].good').next().attr('src','<?php echo base_url(); ?>assets/good.png');
      $('input:radio[name="'+name+'"].poor').next().attr('src','<?php echo base_url(); ?>assets/sad.png');
      var img = $(this).next();
      if($(this).attr('class') == 'excellent'){
        img.fadeOut('fast', function () {
          img.attr('src', '<?php echo base_url(); ?>assets/selectedexcellent.png');
          img.fadeIn('fast');
        });
      }
      if($(this).attr('class') == 'good'){
        img.fadeOut('fast', function () {
          img.attr('src', '<?php echo base_url(); ?>assets/selectedgood.png');
          img.fadeIn('fast');
        });
      }
      if($(this).attr('class') == 'poor'){
        img.fadeOut('fast', function () {
          img.attr('src', '<?php echo base_url(); ?>assets/selectedsad.png');
          img.fadeIn('fast');
        });
      }
    });
  });
  </script>
    <!-- multistep form -->
    <?php $groups=array(); if(!isset($questions['result'])){$groups = array_column($questions['questions'], 'group_name');}?>
    <form id="msform">
    	<!-- progressbar -->
    	<ul id="progressbar">
        <?php
        if(!isset($questions['result'])){
          for($i =0;$i<sizeof($groups);$i++){
            if($i==0){
              echo "<li class='active'>{$groups[$i]}</li>";
            }else{
              echo "<li>{$groups[$i]}</li>";
            }
            $groups[$i] = str_replace(' ', '', $groups[$i]);
          }
        }
        if(!isset($nextBranches['result'])){
          echo "<li>Suggested Branches</li>";
        }
         ?>
    	</ul>
      <script type="text/javascript">
        var groups = <?=json_encode($groups);?>
      </script>
      <?php
      if(!isset($questions['result'])){
        for($i = 0; $i<sizeof($questions['questions']) ;$i++){
            $html= '<fieldset>';
            $html .= '<h2 class="fs-title">Happiness Review</h2>';
            if($questions['questions'][$i]['qroup_type'] == 2){
              $html .= '<div style="border: 1px solid #ccc; border-radius: 3px;font-family: montserrat;color: #2C3E50; font-size: 13px;margin-bottom:5px;">';
              $html .= '<h4>Select an Employee</h4>';
              $html .= "<select name='$groups[$i]emp' style='width: 100%;'>";
                $html .= "<option value='' disabled selected>Select employee</option>";
              for($e =0;$e <sizeof($employees['employees']); $e++){
//                if($e == 0){

//                }else{
                $html .= "<option value='{$employees['employees'][$e]['employee_id']}'>{$employees['employees'][$e]['employee_name']}</option>";
//                }
              }
              $html .= '</select>';
              $html .= '</div>';
            }
            for($j = 0; $j<sizeof($questions['questions'][$i]['ques']) ;$j++){
              $html .= '<div style="border: 1px solid #ccc; border-radius: 3px;font-family: montserrat;color: #2C3E50; font-size: 13px;margin-bottom:5px;">';
              $html .= "<h3 class='fs-subtitle'>{$questions['questions'][$i]['ques'][$j]['question_description']}</h3>";
              $html .= "<label style='padding-right: 40px;' for='".$groups[$i].$j.$i."'><input class='excellent' type='radio' name='".$groups[$i]."check[$j]' value='3' id='".$groups[$i].$j.$i."'/><img src='".base_url()."assets/excellent.png' /></label>";
              $html .= "<label style='padding-right: 40px;' for='".$groups[$i].$j.($i+1)."'><input class='good' type='radio' name='".$groups[$i]."check[$j]' value='2' id='".$groups[$i].$j.($i+1)."'/><img src='".base_url()."assets/good.png' /></label>";
              $html .= "<label for='".$groups[$i].$j.($i+2)."'><input class='poor' type='radio' name='".$groups[$i]."check[$j]' value='1' id='".$groups[$i].$j.($i+2)."'/><img src='".base_url()."assets/sad.png' /></label>";
              $html .= "<input type='hidden' name='".$groups[$i]."input[$j]' value='{$questions['questions'][$i]['ques'][$j]['question_id']}'/>";
              $html .= '</div>';
            }

            if($i==0 && !isset($nextBranches['result'])){
              $html .= '<textarea name="'.$groups[$i].'" placeholder="Comment"></textarea><input type="button" name="next" class="next action-button" value="Next" />';
            }else if ($i==0 && isset($nextBranches['result'])){
              $html .= '<textarea name="'.$groups[$i].'" placeholder="Comment"></textarea><input id="finish" type="button" name="finish" class="action-button" value="Finish" />';
            }
            else if($i==(sizeof($questions['questions'])-1)){
              $html .= '<textarea name="'.$groups[$i].'" placeholder="Comment"></textarea>
              <input type="button" name="previous" class="previous action-button" value="Previous" />';
              if(!isset($nextBranches['result'])){
                $html .= '<input type="button" name="next" class="next action-button" value="Next" />';
              }else{
                $html .= '<input id="finish" type="button" name="finish" class="action-button" value="Finish" />';
              }
            }
            else{
              $html .= '<textarea name="'.$groups[$i].'" placeholder="Comment"></textarea>
              <input type="button" name="previous" class="previous action-button" value="Previous" />
              <input type="button" name="next" class="next action-button" value="Next" />';
            }
            $html .= "<input type='hidden' name='$groups[$i]id' value='{$questions['questions'][$i]['group_id']}'/>";
            $html .= "<input type='hidden' name='$groups[$i]type' value='{$questions['questions'][$i]['qroup_type']}'/>";

            $html .= '</fieldset>';
            echo $html;
        }
      }
      if(!isset($nextBranches['result'])){
        $html= '<fieldset>';
        $html .= '<h2 class="fs-title">Happiness Review</h2>';
        $html .= "<select name='nextbranch' style='width: 100%;'>";
        $html .= '<option selected disabled>-Select Suggested Branch-</option>';
        for($i =0;$i <sizeof($nextBranches['branches']); $i++){
          $html .= "<option value='{$nextBranches['branches'][$i]['id']}'>{$nextBranches['branches'][$i]['name']}</option>";
        }
        $html .= '</select>';
        $html .= '<input type="button" name="previous" class="previous action-button" value="Previous" />
        <input type="button" id="finish" name="finish" class="action-button" value="Finish" />';
        $html .= '</fieldset>';
        echo $html;
      }
       ?>
    	<!-- fieldsets -->
    </form>
    <!-- jQuery easing plugin -->
    <script src="<?php echo base_url(); ?>assets/mobile/jquery.easing.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/mobile/main.js" type="text/javascript"></script>
    <form id="revfr" action="<?php echo base_url(); ?>mobile/savereview" method="POST">
      <input id="revD" type="hidden" name="reviewData" value="">
      <input id="nextB" type="hidden" name="nextB" value="">
    </form>
    <script type="text/javascript">
    $( document ).ready(function() {
      $('#finish').on('click',function(){
        var inputs, index;
        inputs = document.querySelectorAll("input[type=radio]:checked");
        if(inputs.length > 0){
          var previous = '';
          var questionData = {};
          questionData['questions'] =[];
          var row = {};
        }
        for (index = 0; index < inputs.length; ++index) {
          var name = inputs[index].name;
          var gname = name.replace(/check\[\d\]/g, "") ;
          var inputorder = name.replace(/(\w+)check/g, "");
          var inputid = gname+'input'+inputorder;
          if(previous != gname){
            if(index != 0){
              questionData['questions'].push(row);
            }
            row = {};
            row['group_id'] = $("input[name='"+gname+"id'").val();
            row['comment'] = $("textarea[name='"+gname+"'").val();
            row['group_type'] = $("input[name='"+gname+"type'").val();
            if(row['group_type'] == 2){
              row['employee_id'] = $("select[name='"+gname+"emp'").val();
            }
            row['ques'] =[];
            previous = gname;
          }else{
            var grpid = '';
            var grpcmnt = '';
            var grptyp = '';
          }
          var answerData ={};
          answerData['question_id'] = $("input[name='"+inputid+"'").val();
          answerData['result'] = inputs[index].value;
          row['ques'].push(answerData);
          if(index == (inputs.length-1)){
            questionData['questions'].push(row);
          }
        }
        document.getElementById("revD").value = JSON.stringify(questionData);
        document.getElementById("nextB").value = $("select[name='nextbranch'").val();
        //console.log(document.getElementById("revD").value);
        if((document.getElementById("revD").value != 'undefined') || (document.getElementById("nextB").value != '')){
          document.getElementById("revfr").submit();
        }
      });
    });
    </script>
  </body>
</html>
