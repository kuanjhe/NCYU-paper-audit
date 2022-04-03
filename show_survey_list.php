<?php
  session_start();
  include('mysql_connect_inc.php');
  include('survey/header_php.php');
  if(isset($_SESSION['Course_ID'])){
    $Survey_ID=$_SESSION['Survey_ID'];
    $sql = "SELECT `Course_CName` FROM `course_list` WHERE `Survey_ID`='$Survey_ID'";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_row($result);
    $site_name=$row[0];
  } else {
    echo "<meta http-equiv=REFRESH CONTENT=0;url=jump_course.php>";
  }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>學生參加<?php echo $site_name; ?>之調查清單</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <style type="text/css">
      .additional{
        text-align: center;
        color: red;
      }
    </style>
    <script>
    $(document).ready(function(){
      $("#CheckAll").click(function(){
        if($("#CheckAll").prop("checked")){//如果全選按鈕有被選擇的話（被選擇是true）
        $("input[name='optradio[]']").prop("checked",true);//把所有的核取方框的property都變成勾選
        }else{
        $("input[name='optradio[]']").prop("checked",false);//把所有的核取方框的property都取消勾選
       }
      })
      $("#CheckAll_2").click(function(){
        if($("#CheckAll_2").prop("checked")){//如果全選按鈕有被選擇的話（被選擇是true）
        $("input[name='optradio2[]']").prop("checked",true);//把所有的核取方框的property都變成勾選
        }else{
        $("input[name='optradio2[]']").prop("checked",false);//把所有的核取方框的property都取消勾選
       }
      })
    })
    </script> 
    <script type="text/javascript">
      selected_index=NULL;
      function radio_checked(ID){
        var radio_name = "optradio3" + ID;
        document.getElementById(radio_name).checked = true;
        selected_index=ID;
      }
      function radio_unchecked(){
        var radio_name = "optradio3" + selected_index;
        document.getElementById(radio_name).checked = false;
        selected_index=NULL;
      }
      function radioB_checked(ID){
        var radio_name = "optradioB_" + ID;
        document.getElementById(radio_name).checked = true;
        selected_index=ID;
      }
      function radioB_unchecked(){
        var radio_name = "optradioB_" + selected_index;
        document.getElementById(radio_name).checked = false;
        selected_index=NULL;
      }
    </script>
    <style>
      td.hidden {
        visibility: hidden;
      }
</style>
</head>
<body style="font-family: DFKai-sb;">
<?php
  include("header.php");
?>
  
<div class="container">
  <div class="row">
    <?php 
      include("left_side_bar.php");
    ?>
    <div class="col-sm-9">
<?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (isset($_POST['optradio'])){
      $ID = $_POST['optradio'];
      for($i=0; $i<count($ID); $i++){
        $number_items = $_POST[$number_items];
        $sql = "UPDATE `student_list` SET `Teacher_check` = '1' WHERE `student_list`.`ID` ='{$ID[$i]}'";
        mysqli_query($con,$sql);
      }
    }

    if (isset($_POST['optradio2'])){
      $ID = $_POST['optradio2'];
      for($i=0; $i<count($ID); $i++){
        $sql = "DELETE FROM `student_list` WHERE `student_list`.`ID`='{$ID[$i]}'";
        mysqli_query($con,$sql);
      }
    }

    if (isset($_POST['optradio3'])){
      $ID = $_POST['optradio3'];
        for($j=0; $j<count($ID); $j++){
          $Grade = "Grade".$ID[$j];
          $Grade = $_POST[$Grade]; 
          $sql = "UPDATE `student_list` SET `Grade`='$Grade' WHERE `ID`='$ID[$j]'";
          mysqli_query($con,$sql);
        }
    }

    echo "<meta http-equiv=REFRESH CONTENT=0;url=\"{$_SERVER['PHP_SELF']}\">";
  } else {
      if (isset($_SESSION['Survey_ID'])){
        $Survey_ID = $_SESSION['Survey_ID'];    
        echo "      <div class=\"container\">\n";
        echo "        <h2>學生登記修讀清單</h2>\n";
        echo "        <p>以下為學生清單:</p>\n";
        $sql = "SELECT * FROM `course_list` WHERE `Survey_ID` = '$Survey_ID'";
        $row =mysqli_fetch_assoc(mysqli_query($con,$sql));
        echo "<div class=\"text-center\">";
        if (!is_null($row['survey_excel_name'])){
          echo "<a href=\"survey/xlsx/{$row['survey_excel_name']}\" class=\"btn btn-info\" role=\"button\">下載學生登記修讀清單Excel檔</a>";
          
        } else{
          echo "尚未產生學生登記修讀清單Excel檔";
        }
        echo "</div>\n";
        echo "        <form action=\"\" method=\"POST\">\n";
        echo "        <table class=\"table table-hover\">\n";
        echo "          <thead>\n";
        echo "            <tr>\n";
        echo "              <th style=\"text-align: center\"></th>\n";
        echo "              <th style=\"text-align: center\">學號</th>\n";
        echo "              <th style=\"text-align: center\">學生確認</th>\n";
        echo "              <th style=\"text-align: center\">老師確認<br><input type=\"checkbox\" id=\"CheckAll\"></th></th>\n";
        echo "              <th style=\"text-align: center\">分數</th>\n";
        echo "              <th style=\"text-align: center\">登入時間</th>\n";
        echo "              <th style=\"text-align: center\">刪除<br><input type=\"checkbox\" id=\"CheckAll_2\"></th>\n";
        echo "            </tr>\n";
        echo "          </thead>\n";
        echo "          <tbody>\n";
        $DataTable="";
        $DataTable[0][0]="人員編號";
        //$DataTable[0][1]="總答題成功數";
        $sql = "SELECT * FROM `student_list` WHERE `Survey_ID` = '$Survey_ID' ORDER BY `StudentID`";
        $result = mysqli_query($con,$sql);
        for($i=1; $i<=mysqli_num_rows($result); $i++){
          $row=mysqli_fetch_assoc($result);
          $DataTable[$i][0]=$row['StudentID'];
          echo "<tr>\n";
          echo "<td style=\"text-align: center\" class=\"hidden\"><div class=\"radio\"><label><input type=\"checkbox\" name=\"optradio3[]\" id=\"optradio3{$row['ID']}\" value=\"{$row['ID']}\"></label></div></td>\n";
          echo "<td style=\"text-align: center\">{$row['StudentID']}</td>\n";
          if ($row['Student_check']==1) {
          	echo "<td style=\"text-align: center\"> &#10004; </td>\n";
          }else{
          	echo "<td style=\"text-align: center\"> &#10006; </td>\n";
          }
          if ($row['Teacher_check']==1) {
          	echo "<td style=\"text-align: center\"> &#10004; </td>\n";
          }else{
          	echo "<td style=\"text-align: center\"><input type=\"checkbox\" name=\"optradio[]\" id=\"optradio{$row['ID']}\" value=\"{$row['ID']}\"></label></td>\n";
          }
          echo "<td style=\"text-align: center\" ><input type=\"text\" class=\"form-control\" name=\"Grade{$row['ID']}\" id=\"Grade{$row['ID']}\" value=\"{$row['Grade']}\"";
          echo " onclick=\"radio_checked('{$row['ID']}')\" onselect=\"radio_checked('{$row['ID']}')\" required>";
          echo "</td>\n";
          echo "<td style=\"text-align: center\">{$row['Time']}</td>\n";
          echo "<td style=\"text-align: center\"><input type=\"checkbox\" name=\"optradio2[]\" id=\"optradio2{$row['ID']}\" value=\"{$row['ID']}\"></label></td>\n";
          echo "</tr>\n";    
        }
        echo "</tbody>\n";
        echo "</table>\n";
        echo "<div class=\"text-center\">\n";
        echo "<input class=\"btn btn-info\" role=\"button\" type=\"submit\" value=\"送出修正\"></td>\n";
        echo "</div>\n";
        echo "</form>\n";
        echo "<br>";
        /*include("xlsxwriter.class.php");
        $writer = new XLSXWriter();
        $format = array('font'=>'Arial','font-size'=>10,'halign'=>'center');
        foreach($DataTable as $row_data)
          $writer->writeSheetRow("list",$row_data,$format);
        $ResultName="attendence_list_activity_".$activity_name."_".date("Y_m_d").".xlsx";
        $writer->writeToFile($ResultName);*/
        //$_SESSION['year'] = $year;
        //$_SESSION['activity_id'] = $activity_name;
      }
    }
?>
          
          
<?php  
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<div class=\"text-center\">";
    echo "<a href=\"output_survey_list.php?Course_ID=$Survey_ID\" class=\"btn btn-info\" role=\"button\">產生學生登記修讀清單Excel檔</a>";
    echo "</div>";
  } else if (isset($_SESSION['Course_ID'])){
    echo "<div class=\"text-center\">";
    echo "<a href=\"output_survey_list.php?Course_ID=$Survey_ID\" class=\"btn btn-info\" role=\"button\">產生學生登記修讀清單Excel檔</a>";
    echo "</div>";
  }
?>        
      </div>
    </div>
  </div>
</div>

</body>
</html>