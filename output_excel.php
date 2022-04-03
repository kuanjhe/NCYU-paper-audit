<?php
  	session_start();
  	include('mysql_connect_inc.php');
    include('department_script.php');                                      
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>書面審查成績轉換</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="//d3js.org/d3.v3.js"></script>
    <script src="http://d3js.org/d3.v3.min.js"></script>
    <script src="radar-chart.js"></script>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <style type="text/css">
      .additional{
        text-align: center;
        color: red;
      }
    </style>
</head>
<body style="font-family: DFKai-sb;">
<?php
  include("header.php");
  $Course_Department_array=array('','教育學系暨研究所','輔導與諮商學系暨研究所','體育與健康休閒學系暨研究所','特殊教育學系暨研究所','幼兒教育學系暨研究所','教育行政與政策發展研究所','數理教育研究所','數位學習設計與管理學系暨研究所','教學專業國際碩士學位學程','中國文學系暨研究所','視覺藝術學系暨研究所','應用歷史學系暨研究所','外國語言學系暨研究所','音樂學系暨研究所','企業管理學系暨研究所','應用經濟學系暨研究所','生物事業管理學系暨研究所','資訊管理學系暨研究所','財務金融學系','行銷與觀光管理學系暨研究所','管院碩士在職專班','全英文授課觀光暨管理碩士學位學程','農藝學系暨研究所','園藝學系暨研究所','森林暨自然資源學系暨研究所','木質材料與設計學系暨研究所','動物科學系暨研究所','生物農業科技學系','景觀學系','農業科學博士學位學程','植物醫學系','農學院農業科技全英碩士學位學程','生物技術學程','蘭花生技學程','有機農業學程','農場管理進修學士學位學程','農學碩士在職專班','電子物理學系光電暨固態電子研究所','應用化學系暨研究所','應用數學系暨研究所','資訊工程學系暨研究所','生物機電工程學系暨研究所','土木與水資源工程學系暨研究所','電機工程學系暨研究所','機械與能源工程學系','食品科學系暨研究所','水生生物科學系暨研究所','生物資源學系暨研究所','生化科技學系暨研究所','微生物免疫與生物藥學系暨研究所','生命科學全英文碩士學位學程','獸醫學系暨研究所','公共政策研究所');
    $Course_College_array=array('','理工學院','師範學院','人文藝術學院','管理學院','農學院','生命科學院','獸醫學院');
?>
<div class="container">
  <div class="row">
    <div class="col-md">      
       <h2 style="text-align:center;">查詢各科系評分表</h2>
       <form action="" method="post">
          <div  class="form-group">
            <label for="Year"><h4>請選擇學年度:</h4></label>
              <select class="form-control" id="Year" name="Year" required>
<?php
    $sql = "SELECT `Year` FROM `grade` WHERE 1 GROUP BY `Year` ORDER BY `Year` ASC";
    $result = mysqli_query($con,$sql);
    for($i=1; $i<=mysqli_num_rows($result); $i++){
      $row = mysqli_fetch_assoc($result);
      if (isset($_SESSION['Year'])){
        if ($row['Year']==$_SESSION['Year']){
          echo "            <option value=\"".$row['Year']."\" selected>".$row['Year']."</option>\n";
        } else {
          echo "            <option value=\"".$row['Year']."\">".$row['Year']."</option>\n";
        } 
    } else{
        echo "            <option value=\"".$row['Year']."\">".$row['Year']."</option>\n";
      } 
    }
?>
              </select>
            </div>
            <div class="form-group">
              <label for="College"><h4>請選擇學院：</h4></label> 
              <select class="form-control action" id="College" name="College"  onchange="produce_department()" required>

<?php 
        foreach ($Course_College_array as $array_name) 
                echo "<option value=\"{$array_name}\">{$array_name}</option>\n";
?>
              </select>
            </div>
            <div class="form-group">
              <label for="Department"><h4>請選擇系所：</h4></label> 
              <select class="form-control action" id="Department" name="Department" require>

<?php 
        foreach ($Course_Department_array as $array_name) 
                echo "<option value=\"{$array_name}\">{$array_name}</option>\n";
?>
              </select>
            </div>
            <div class="form-group">
              <button type="submit" id="submit" class="btn-submit">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>

<?php 



  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $Year=$_POST['Year'];
    $Department=$_POST['Department'];

    $sql="SELECT * FROM `department` WHERE  `CName`='$Department'";
    $result=mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $code=$row['College_ID']."_".$row['ID'];

    include("xlsxwriter.class.php"); //include
    $sql="SELECT * FROM `list` WHERE  `Year`='$Year' and `Department`='$Department'";
    $result=mysqli_query($con,$sql);
    $num=mysqli_num_rows($result);


    if(isset($num)){
      $sql="SELECT `Teacher` FROM `grade` WHERE  `Year`='$Year' and `Department`='$Department' GROUP BY Teacher ORDER BY `Teacher` ASC";
      $result=mysqli_query($con,$sql);
      $num2=mysqli_num_rows($result);
      $num_Teacher=mysqli_num_rows($result);
      $writer = new XLSXWriter();
      for($i=1;$i<=$num2;$i++){
        $row = mysqli_fetch_assoc($result);
        $Teacher=$row['Teacher'];     //new writer

        $sheet_name = "sheet_".$i;   //sheetname
        $format1 = array('font'=>'Arial','font-size'=>20,'halign'=>'center');
        $format2 = array('font'=>'Arial','font-size'=>12,'halign'=>'center');
        $descript1=$Year."學年度大學個人申請入學招生".$Department."書審評分表(".$i.") 評分人員:".$Teacher;
        
        $header = array('string','string','string','string','string','string','string','string','string','string','string','string','string','string','string','string','string','string');  // header-made for six columns
        $writer->writeSheetHeader($sheet_name, $header, $col_options = ['widths'=>[10,20,30,40,50,60], 'suppress_row'=>true] );
        $des1 = array($descript1);
        
        //$des2 = array('','','高中(職)在校成績證明(30%)','','','','','讀書計畫(含申請動機)(50%)','','','','','英語能力檢定證明(20%)','','','','','總分');
        $des2 = array("","","高中(職)在校成績證明(30%)","優(80~89)","佳(70~79)","良(60~69)","可(~59)","讀書計畫(含申請動機)(50%)","優(80~89)","佳(70~79)","良(60~69)","可(~59)","英語能力檢定證明(20%)","優(80~89)","佳(70~79)","良(60~69)","可(~59)","總分");
        $des3 = array("編號","姓名","傑出(90~)","優(80~89)","佳(70~79)","良(60~69)","可(~59)","傑出(90~)","優(80~89)","佳(70~79)","良(60~69)","可(~59)","傑出(90~)","優(80~89)","佳(70~79)","良(60~69)","可(~59)","");

        $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 18);
        $writer->markMergedCell($sheet_name, $start_row = 1, $start_col = 2, $end_row = 1, $end_col = 6);
        $writer->markMergedCell($sheet_name, $start_row = 1, $start_col = 7, $end_row = 1, $end_col = 11);
        $writer->markMergedCell($sheet_name, $start_row = 1, $start_col = 12, $end_row = 1, $end_col = 16); 
        $writer->writeSheetRow($sheet_name, $des1,$format1);   //write data
        $writer->writeSheetRow($sheet_name, $des2,$format2);   //write data
        $writer->writeSheetRow($sheet_name, $des3,$format2);   //write data

        
        $sql2="SELECT * FROM `grade` WHERE  `Year`='$Year' and `Department`='$Department' and `Teacher`='$Teacher'";
        $result2=mysqli_query($con,$sql2);
        $num3=mysqli_num_rows($result2);
        $result2=mysqli_query($con,$sql2);
        for($e=0;$e<$num3+2;$e++){#配合excel結構
          for($r=0;$r<18;$r++){
            $DataTable[$e][$r]=' ';
          }
        }
        for($j=2;$j<$num3+2;$j++){#配合檔案結構
          
          $row2 = mysqli_fetch_assoc($result2);
          $DataTable[$j][0]=$j-1;
          $DataTable[$j][1]=$row2['Name'];

          $a=$row2['a_p']+1;
          $b=$row2['b_p']+1;
          $c=$row2['c_p']+1;#配合檔案結構
          

          $DataTable[$j][$a]=$row2['basic_knowledge_Grade'];
          $DataTable[$j][$b]=$row2['expression_ability_Grade'];
          $DataTable[$j][$c]=$row2['response_capability_Grade'];
          $DataTable[$j][17]=$row2['Average'];
          $rowarray[$i][$j]=array($DataTable[$j][0],$DataTable[$j][1],$DataTable[$j][2],$DataTable[$j][3],$DataTable[$j][4],$DataTable[$j][5],$DataTable[$j][6],$DataTable[$j][7],$DataTable[$j][8],$DataTable[$j][9],$DataTable[$j][10],$DataTable[$j][11],$DataTable[$j][12],$DataTable[$j][13],$DataTable[$j][14],$DataTable[$j][15],$DataTable[$j][16],$DataTable[$j][17]);
        }

        $data[$i]=array($rowarray[$i][2]);
        for($k=3;$k<$num3+2;$k++){
          array_push($data[$i],$rowarray[$i][$k] );
        }
        
        if($i==1)
          $writer->writeSheet($data[$i], $sheet_name, $header);//附件一
        if($i==2)
          $writer->writeSheet($data[$i], $sheet_name, $header);//附件一
        if($i==3)
          $writer->writeSheet($data[$i], $sheet_name, $header);//附件一
        if($i==4)
          $writer->writeSheet($data[$i], $sheet_name, $header);//附件一
        
      }
      ############################################################################################################
      #附件二
      
      $sql="SELECT `Teacher` FROM `grade` WHERE  `Year`='$Year' and `Department`='$Department' GROUP BY Teacher ORDER BY `Teacher` ASC";
      $result=mysqli_query($con,$sql);
      $num=mysqli_num_rows($result);
      for($i=1;$i<=3;$i++){
        for($j=1;$j<=5;$j++){
          for($k=1;$k<=4;$k++){
            $grade[$i][$k][$j]=0;
          }
        }
      }
        for($j=1;$j<=$num;$j++){
          $row = mysqli_fetch_assoc($result);
          $teacher[$j]=$row['Teacher'];
          $Teacher=$row['Teacher'];
          $sql2="SELECT * FROM `list` WHERE  `Year`='$Year' and `Department`='$Department' and `Teacher`='$Teacher'";
          $result2=mysqli_query($con,$sql2);
          $num2=mysqli_num_rows($result2);
          $row2 = mysqli_fetch_array($result2);
          for($k=1;$k<=5;$k++){#資料庫結構   
            $grade[1][$j][$k]=$row2[$k+3];
            $grade[2][$j][$k]=$row2[$k+8];
            $grade[3][$j][$k]=$row2[$k+13];   
          }
        }
      for($i=1;$i<=3;$i++){
        for($j=1;$j<=5;$j++){
          $grade_all[$i][$j]=0;
        }
      }

      
      for($i=1;$i<=3;$i++){
        for($j=1;$j<=5;$j++){
          $grade_all[$i][$j]=$grade[$i][1][$j]+$grade[$i][2][$j]+$grade[$i][3][$j]+$grade[$i][4][$j];
        }
      }

      $sheet_name = "result_sheet";
      $header = array('string','string','string','string','string','string','string','string','string','string','string','string','string','string');
      $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 13);#嘉義大學
      $writer->markMergedCell($sheet_name, $start_row = 1, $start_col = 0, $end_row = 2, $end_col = 1);#空白
      $writer->markMergedCell($sheet_name, $start_row = 1, $start_col = 2, $end_row = 1, $end_col = 5);#面向一
      $writer->markMergedCell($sheet_name, $start_row = 1, $start_col = 6, $end_row = 1, $end_col = 9);#面向二
      $writer->markMergedCell($sheet_name, $start_row = 1, $start_col = 10, $end_row = 1, $end_col = 13);#面向三
      $writer->markMergedCell($sheet_name, $start_row = 2, $start_col = 2, $end_row = 2, $end_col = 5);#30
      $writer->markMergedCell($sheet_name, $start_row = 2, $start_col = 6, $end_row = 2, $end_col = 9);#50
      $writer->markMergedCell($sheet_name, $start_row = 2, $start_col = 10, $end_row = 2, $end_col = 13);#20
      for($n=0;$n<5;$n++){
        $writer->markMergedCell($sheet_name, $start_row = $num_Teacher*$n+3, $start_col = 0, $end_row = $num_Teacher*$n+3+$num_Teacher-1, $end_col = 1);#90
        $writer->markMergedCell($sheet_name, $start_row = $num_Teacher*$n+3, $start_col = 4, $end_row = $num_Teacher*$n+3+$num_Teacher-1, $end_col = 4);#合計
        $writer->markMergedCell($sheet_name, $start_row = $num_Teacher*$n+3, $start_col = 5, $end_row = $num_Teacher*$n+3+$num_Teacher-1, $end_col = 5);#total
        $writer->markMergedCell($sheet_name, $start_row = $num_Teacher*$n+3, $start_col = 8, $end_row = $num_Teacher*$n+3+$num_Teacher-1, $end_col = 8);#合計
        $writer->markMergedCell($sheet_name, $start_row = $num_Teacher*$n+3, $start_col = 9, $end_row = $num_Teacher*$n+3+$num_Teacher-1, $end_col = 9);#total
        $writer->markMergedCell($sheet_name, $start_row = $num_Teacher*$n+3, $start_col = 12, $end_row = $num_Teacher*$n+3+$num_Teacher-1, $end_col = 12);#合計
        $writer->markMergedCell($sheet_name, $start_row = $num_Teacher*$n+3, $start_col = 13, $end_row = $num_Teacher*$n+3+$num_Teacher-1, $end_col = 13);#total
      }
      for($j=0;$j<3;$j++){
        for($n=1;$n<=$num_Teacher;$n++){
          $writer->markMergedCell($sheet_name, $start_row = $n+2, $start_col =4*$j+2, $end_row = $n+2, $end_col = 4*$j+3);#評委
          $writer->markMergedCell($sheet_name, $start_row = $n+$num_Teacher+2, $start_col = 4*$j+2, $end_row = $n+$num_Teacher+2, $end_col = 4*$j+3);#評委
          $writer->markMergedCell($sheet_name, $start_row = $n+2+2*$num_Teacher, $start_col = 4*$j+2, $end_row = $n+2+2*$num_Teacher, $end_col = 4*$j+3);#評委
          $writer->markMergedCell($sheet_name, $start_row = $n+2+3*$num_Teacher, $start_col = 4*$j+2, $end_row = $n+2+3*$num_Teacher, $end_col = 4*$j+3);#評委
          $writer->markMergedCell($sheet_name, $start_row = $n+2+4*$num_Teacher, $start_col = 4*$j+2, $end_row = $n+2+4*$num_Teacher, $end_col = 4*$j+3);#評委
        }
      }
      $descript2="嘉義大學".$Department."書面審查評分統計結果";
      $des1 = array($descript2);
      $des2 = array('','','面向一','','','','面向二','','','','面向三','','','');
      $des3 = array('','','高中(職)在校成績證明30%','','','','讀書計畫(含申請動機)50%','','','','英語能力檢定證明20%','','','');
      for($i=1;$i<=5;$i++){
        if($i==1){
          for($j=1;$j<=$num_Teacher;$j++){
            $text[$i][$j] = array('傑出90以上','',$teacher[$j].":".$grade[1][$j][$i],'','合計',$grade_all[1][$i],$teacher[$j].":".$grade[2][$j][$i],'','合計',$grade_all[2][$i],$teacher[$j].":".$grade[3][$j][$i],'','合計',$grade_all[3][$i]);
          }
        }elseif ($i==2) {
          for($j=1;$j<=$num_Teacher;$j++){
            $text[$i][$j] = array('優80-89','',$teacher[$j].":".$grade[1][$j][$i],'','合計',$grade_all[1][$i],$teacher[$j].":".$grade[2][$j][$i],'','合計',$grade_all[2][$i],$teacher[$j].":".$grade[3][$j][$i],'','合計',$grade_all[3][$i]);
          }
        }elseif ($i==3) {
          for($j=1;$j<=$num_Teacher;$j++){
            $text[$i][$j] = array('佳70-79','',$teacher[$j].":".$grade[1][$j][$i],'','合計',$grade_all[1][$i],$teacher[$j].":".$grade[2][$j][$i],'','合計',$grade_all[2][$i],$teacher[$j].":".$grade[3][$j][$i],'','合計',$grade_all[3][$i]);
          }
        }elseif ($i==4) {
          for($j=1;$j<=$num_Teacher;$j++){
            $text[$i][$j] = array('可60-69','',$teacher[$j].":".$grade[1][$j][$i],'','合計',$grade_all[1][$i],$teacher[$j].":".$grade[2][$j][$i],'','合計',$grade_all[2][$i],$teacher[$j].":".$grade[3][$j][$i],'','合計',$grade_all[3][$i]);
          }
        }elseif ($i==5) {
          for($j=1;$j<=$num_Teacher;$j++){
            $text[$i][$j] = array('不佳60以下','',$teacher[$j].":".$grade[1][$j][$i],'','合計',$grade_all[1][$i],$teacher[$j].":".$grade[2][$j][$i],'','合計',$grade_all[2][$i],$teacher[$j].":".$grade[3][$j][$i],'','合計',$grade_all[3][$i]);
          }
        }
        
          
        
        
      }
      
      $writer->writeSheetRow($sheet_name, $des1,$format1);   //write data
      $writer->writeSheetRow($sheet_name, $des2,$format1);   //write data
      $writer->writeSheetRow($sheet_name, $des3,$format2);   //write data
      for($i=1;$i<=5;$i++){
        for($j=1;$j<=$num_Teacher;$j++){
          $writer->writeSheetRow($sheet_name, $text[$i][$j],$format2);   //write data
        }
      }

      $writer->writeToFile("xlsx/".$Year."_".$code.".xlsx");
      if (file_exists("xlsx/".$Year."_".$code.".xlsx")){
        echo "<center><a href=\"xlsx/".$Year."_".$code.".xlsx\" class=\"btn btn-info\" role=\"button\">下載".$Year."學年度".$Department."excel檔</a></center>";
      }
      
    }else{
      echo "<h3><center>尚未上傳資料</center></h3>";
    }
    
  }
?>
