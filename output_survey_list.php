<?php
  	session_start();
  	include('mysql_connect_inc.php');
    include('survey/header_php.php');
    
  	$Course_ID = $_GET['Course_ID'];   
    $DataTable="";
    $DataTable[0][0]="人員編號";
    $DataTable[0][1]="學年度";
    $DataTable[0][2]="課程名稱";
    $DataTable[0][3]="授課教師";
    $DataTable[0][4]="學生確認";
    $DataTable[0][5]="老師確認";
    $DataTable[0][6]="學期分數";
    $DataTable[0][7]="登入時間";
    //$DataTable[0][1]="總答題成功數";

    $sql = "SELECT * FROM `course_list` WHERE `Course_ID` = '$Course_ID'";
    $result = mysqli_query($con,$sql);
    $row=mysqli_fetch_assoc($result);
    $Year = $row['Year'];
    $Course_CName = $row['Course_CName'];
    $Teacher_CName = $row['Teacher_CName'];


    $sql = "SELECT * FROM `student_list` WHERE `Course_ID` = '$Course_ID' and `Hide`='0'";
    $result = mysqli_query($con,$sql);
    for($i=1; $i<=mysqli_num_rows($result); $i++){
      $row=mysqli_fetch_assoc($result);
      $DataTable[$i][0]=$row['StudentID'];
      $DataTable[$i][1]=$Year;
      $DataTable[$i][2]=$Course_CName;
      $DataTable[$i][3]=$Teacher_CName;
      $DataTable[$i][4]=$row['Student_check'];
      $DataTable[$i][5]=$row['Teacher_check'];
      $DataTable[$i][6]=$row['Grade'];
      $DataTable[$i][7]=$row['Time'];
      
    }
    include("xlsxwriter.class.php");
    $writer = new XLSXWriter();
    $format = array('font'=>'Arial','font-size'=>10,'halign'=>'center');
    foreach($DataTable as $row_data)
      $writer->writeSheetRow("list",$row_data,$format);
    $ResultName="attendence_survey_list_coures_".$Course_ID."_".date("Y_m_d").".xlsx";
    $writer->writeToFile("survey/xlsx/".$ResultName);
    $sql = "UPDATE `course_list` SET `survey_excel_name`='$ResultName' WHERE `Course_ID` = '$Course_ID'";
    mysqli_query($con,$sql);
    //echo "show_attendence_list.php";
    echo "<meta http-equiv=REFRESH CONTENT=0.01;url=show_survey_list.php>";
?>