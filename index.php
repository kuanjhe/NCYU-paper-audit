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
      .center-div
      {
        margin: 0 auto;
        width: 100px; 
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
		  
			 <h2 style="text-align:center;">Excel匯入審查成績</h2>
			 <form  action="" method="post" name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
          <div  class="form-group">
            <label for="Year"><h4>請選擇學年度:</h4></label>
              <select class="form-control" id="Year" name="Year" required>
<?php
    for ($i=108; $i <= 112; $i++) {
      echo "<option value=\"{$i}\">{$i}</option>\n";
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
              <label for="Teacher_CName"><h4>請輸入評分老師：</h4></label>
              <input type="text" class="form-control" id="Teacher" name="Teacher" required>
          </div>
          <div class="form-group">
            <input type="file" name="file" id="file" accept=".xls,.xlsx" required>
          </div>
          <div class="form-group">
            <button type="submit"  id="submit" name="import" class="btn-submit">匯入</button>
          </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div calss="container-fluid">
    <div class="row">
      <div class="col-md">

<?php    
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $_SESSION['Year']=$_POST['Year'];
    $Year=$_POST['Year'];
    $Department=$_POST['Department'];
    $Teacher=$_POST['Teacher'];
    require_once('php-excel-reader/excel_reader2.php');
    require_once('SpreadsheetReader.php');
    require_once('SpreadsheetReader_XLSX.php');
    echo "<div class=\"col-md\">";
    $allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    if(in_array($_FILES["file"]["type"],$allowedFileType)){
      $targetPath = 'uploads/'.$_FILES['file']['name'];
      move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
      $Reader = new SpreadsheetReader($targetPath);
      $sheetCount = count($Reader->sheets());
      for($i=1;$i<=5;$i++){
      $a_gall_[$i]=0;
      $b_gall_[$i]=0;
      $c_gall_[$i]=0;
      }
      for($i=1;$i<=9;$i++){
      $total_[$i]=0;
      }




        for($i=0;$i<$sheetCount;$i++){
            
            $Reader->ChangeSheet($i);
            echo"<h3><center><p>".$Year."學年度大學個人申請入學招生".$Department."書審評分表</p></center></h3>";
            echo "<table class='table table-striped table-hover table-bordered'>";            
            echo "<tbody>\n";
            echo "<tr><th></th><th></th>";
            echo "<th colspan=\"5\"><h3><center>高中(職)在校成績證明(30%)</center></h3></th>";
            echo "<th colspan=\"5\"><h3><center>讀書計畫(含申請動機)(50%)</center></h3></th>";
            echo "<th colspan=\"5\"><h3><center>英語能力檢定證明(20%)</center></h3></th>";
            echo "<th>總分</th>";
            echo "<th>上傳狀態</th>";
            echo "<tr><th>編號</th><th>姓名</th>";for($j=0;$j<3;$j++){echo "<th>傑出(90~100)</th><th>優(80~89)</th><th>佳(70~79)</th><th>良(60~69)</th><th>可(~59)</th>";}
            echo "<th></th><th></th></tr>";
            foreach ($Reader as $Key=>$Row){
              for($i=1;$i<=5;$i++){
              $a_g_[$i]=0;
              $b_g_[$i]=0;
              $c_g_[$i]=0;
              
              }
              
              $Number = "";                
                if(isset($Row[2])) {
                  $Number = $Row[2];
                }  

              $Name = "";                   
              if(isset($Row[3])) {
                  $Name = $Row[3];
              }     

              $basic_knowledge_Grade = ""; 
              if(isset($Row[4])) {
                  $basic_knowledge_Grade = $Row[4];
              }
              
              $expression_ability_Grade = ""; 
              if(isset($Row[5])) {
                  $expression_ability_Grade = $Row[5];
              }
              $response_capability_Grade = ""; 
              if(isset($Row[6])) {
                  $response_capability_Grade = $Row[6];
              }                
              $total=$basic_knowledge_Grade*0.3+$expression_ability_Grade*0.5+$response_capability_Grade*0.2;

              if(!isset($t)){
                $t=1;
              }
              if ($Key!=0){
                if($total>=95){
                  $total_[1]++;
                }elseif ($total>=90&&$total<94) {
                  $total_[2]++;
                }elseif ($total>=85&&$total<89) {
                  $total_[3]++;
                }elseif ($total>=80&&$total<84) {
                  $total_[4]++;
                }elseif ($total>=75&&$total<79) {
                  $total_[5]++;
                }elseif ($total>=70&&$total<74) {
                  $total_[6]++;
                }elseif ($total>=65&&$total<69) {
                  $total_[7]++;
                }elseif ($total>=60&&$total<64) {
                  $total_[8];
                }elseif ($total<60) {
                  $total_[9]++;
                }
                
                echo "<tr>";
                
                echo "<td>$t</td>";
                $t=$t+1;
                echo "<td>{$Name}</td>";
                #echo "<td>{$Number}</td>";
                
                

                $a = "";
                $b = "";
                $c = "";
                $d = "";
                $e = "";
                
                if($basic_knowledge_Grade>=90){
                  $a=$basic_knowledge_Grade;
                  $a_p=1;
                  $a_g_[1]=$a_g_[1]+1;
                  $a_gall_[1]++;
                }elseif ($basic_knowledge_Grade>=80&&$basic_knowledge_Grade<90) {
                  $b=$basic_knowledge_Grade;
                  $a_p=2;
                  $a_g_[2]=$a_g_[2]+1;
                  $a_gall_[2]++;
                }elseif ($basic_knowledge_Grade>=70&&$basic_knowledge_Grade<80) {
                  $c=$basic_knowledge_Grade;
                  $a_p=3;
                  $a_g_[3]=$a_g_[3]+1;
                  $a_gall_[3]++;
                }elseif ($basic_knowledge_Grade>=60&&$basic_knowledge_Grade<70) {
                  $d=$basic_knowledge_Grade;
                  $a_p=4;
                  $a_g_[4]=$a_g_[4]+1;
                  $a_gall_[4]++;
                }elseif ($basic_knowledge_Grade<60) {
                  $e=$basic_knowledge_Grade;
                  $a_p=5;
                  $a_g_[5]=$a_g_[5]+1;
                  $a_gall_[5]++;
                }
                echo "<td>{$a}</td><td>{$b}</td><td>{$c}</td><td>{$d}</td><td>{$e}</td>";
                

########################################
                $a = "";
                $b = "";
                $c = "";
                $d = "";
                $e = "";
                
                if($expression_ability_Grade>=90){
                  $a=$expression_ability_Grade;
                  $b_p=6;
                  $b_g_[1]=$b_g_[1]+1;
                  $b_gall_[1]++;
                }elseif ($expression_ability_Grade>=80&&$expression_ability_Grade<90) {
                  $b=$expression_ability_Grade;
                  $b_p=7;
                  $b_g_[2]=$b_g_[2]+1;
                  $b_gall_[2]++;
                }elseif ($expression_ability_Grade>=70&&$expression_ability_Grade<80) {
                  $c=$expression_ability_Grade;
                  $b_p=8;
                  $b_g_[3]=$b_g_[3]+1;
                  $b_gall_[3]++;
                }elseif ($expression_ability_Grade>=60&&$expression_ability_Grade<70) {
                  $d=$expression_ability_Grade;
                  $b_p=9;
                  $b_g_[4]=$b_g_[4]+1;
                  $b_gall_[4]++;
                }elseif ($expression_ability_Grade<60) {
                  $e=$expression_ability_Grade;
                  $b_p=10;
                  $b_g_[5]=$b_g_[5]+1;
                  $b_gall_[5]++;
                }
                echo "<td>{$a}</td><td>{$b}</td><td>{$c}</td><td>{$d}</td><td>{$e}</td>";
#######################################
                $a = "";
                $b = "";
                $c = "";
                $d = "";
                $e = "";
                
                if($response_capability_Grade>=90){
                  $a=$response_capability_Grade;
                  $c_p=11;
                  $c_g_[1]=$c_g_[1]+1;
                  $c_gall_[1]++;
                }elseif ($response_capability_Grade>=80&&$response_capability_Grade<90) {
                  $b=$response_capability_Grade;
                  $c_p=12;
                  $c_g_[2]=$c_g_[2]+1;
                  $c_gall_[2]++;
                }elseif ($response_capability_Grade>=70&&$response_capability_Grade<80) {
                  $c=$response_capability_Grade;
                  $c_p=13;
                  $c_g_[3]=$c_g_[3]+1;
                  $c_gall_[3]++;
                }elseif ($response_capability_Grade>=60&&$response_capability_Grade<70) {
                  $d=$response_capability_Grade;
                  $c_p=14;
                  $c_g_[4]=$c_g_[4]+1;
                  $c_gall_[4]++;
                }elseif ($response_capability_Grade<60) {
                  $e=$response_capability_Grade;
                  $c_p=15;
                  $c_g_[5]=$c_g_[5]+1;
                  $c_gall_[5]++;
                }
                echo "<td>{$a}</td><td>{$b}</td><td>{$c}</td><td>{$d}</td><td>{$e}</td>";
                echo "<td>{$total}</td>";
                

                $sql = "SELECT * FROM `grade` WHERE `Number`='$Number' and `Year`='$Year' and `Department`='$Department' and `Teacher`='$Teacher'";
                $result=mysqli_query($con,$sql);
                $row = mysqli_fetch_assoc($result);
                if (mysqli_num_rows($result)>=1){
                  $sql = "UPDATE `grade` SET `basic_knowledge_Grade`='$basic_knowledge_Grade',`expression_ability_Grade`='$expression_ability_Grade',`response_capability_Grade`='$response_capability_Grade',`Average`='$total',`a_p`='$a_p',`b_p`='$b_p',`c_p`='$c_p'";
                  for($i=1;$i<=5;$i++){
                    $sql=$sql.", `a_g_[".$i."]`='$a_g_[$i]', `b_g_[".$i."]`='$b_g_[$i]', `c_g_[".$i."]`='$c_g_[$i]' " ;
                  }
                  
                  $sql=$sql."WHERE `Number`='$Number' and `Year`='$Year' and `Department`='$Department' and `Teacher`='$Teacher'";
                  if (mysqli_query($con,$sql)){
                    echo "<td>資料更新成功</td>";
                  } else {
                    echo "<td>資料更新失敗</td>";
                  }
                  
                } else { 
                  $sql = "INSERT INTO `grade` (`ID`, `Number`, `Name`, `basic_knowledge_Grade`, `expression_ability_Grade`,`response_capability_Grade`,`Time`,`Year`,`Department`,`Teacher`,`a_g_[1]`,`a_g_[2]`,`a_g_[3]`,`a_g_[4]`,`a_g_[5]`,`b_g_[1]`,`b_g_[2]`,`b_g_[3]`,`b_g_[4]`,`b_g_[5]`,`c_g_[1]`,`c_g_[2]`,`c_g_[3]`,`c_g_[4]`,`c_g_[5]`,`Average`,`a_p`,`b_p`,`c_p`) VALUES (NULL, '{$Number}', '{$Name}','{$basic_knowledge_Grade}','{$expression_ability_Grade}','{$response_capability_Grade}',CURRENT_TIME,'$Year','$Department','$Teacher','$a_g_[1]','$a_g_[2]','$a_g_[3]','$a_g_[4]','$a_g_[5]','$b_g_[1]','$b_g_[2]','$b_g_[3]','$b_g_[4]','$b_g_[5]','$c_g_[1]','$c_g_[2]','$c_g_[3]','$c_g_[4]','$c_g_[5]','$total','$a_p','$b_p','$c_p')";
                  
                  if (mysqli_query($con,$sql)){
                    echo "<td>上傳成功</td>";
                  } else {
                    echo "<td>上傳失敗</td>";
                    
                  }
                }
              } 
                                                    
              echo "</tr>\n";
            }
  $sql = "SELECT * FROM `list` WHERE  `Year`='$Year' and `Department`='$Department' and `Teacher`='$Teacher'";
  $result=mysqli_query($con,$sql);
  $row = mysqli_fetch_assoc($result);
  if (mysqli_num_rows($result)>=1){
    $sql = "UPDATE `list` SET `a_gall_[1]`='$a_gall_[1]'";
    for($k=2;$k<6;$k++){
      $sql =$sql.", `a_gall_[".$k."]`='{$a_gall_[$k]}'";
    }
    for($k=1;$k<6;$k++){
      $sql =$sql.", `b_gall_[".$k."]`='{$b_gall_[$k]}'";
    }
    for($k=2;$k<6;$k++){
      $sql =$sql.", `c_gall_[".$k."]`='{$c_gall_[$k]}'";
    }
    $sql=$sql." WHERE   `Year`='$Year' and `Department`='$Department' and `Teacher`='$Teacher'";
    if (mysqli_query($con,$sql)){
      
    } else {
      echo "資料更新失敗";
      
    }
    
  } else { 
    $sql = "INSERT INTO `list` (`ID`, `Year`, `Department`, `Teacher`,`a_gall_[1]`,`a_gall_[2]`,`a_gall_[3]`,`a_gall_[4]`,`a_gall_[5]`,`b_gall_[1]`,`b_gall_[2]`,`b_gall_[3]`,`b_gall_[4]`,`b_gall_[5]`,`c_gall_[1]`,`c_gall_[2]`,`c_gall_[3]`,`c_gall_[4]`,`c_gall_[5]`) VALUES (NULL, '{$Year}', '{$Department}','{$Teacher}','$a_gall_[1]','$a_gall_[2]','$a_gall_[3]','$a_gall_[4]','$a_gall_[5]','$b_gall_[1]','$b_gall_[2]','$b_gall_[3]','$b_gall_[4]','$b_gall_[5]','$c_gall_[1]','$c_gall_[2]','$c_gall_[3]','$c_gall_[4]','$c_gall_[5]')";
    if (mysqli_query($con,$sql)){
      
    } else {
      echo "上傳失敗";
      
    }
  }    
            echo "</tbody>";
            echo "</table>"; 


            echo"<br><h3><center><p>嘉義大學".$Department."書面審查評分統計結果</p></center></h3>";
            echo "<table class='table table-striped table-hover table-bordered'>";            
            echo "<tbody style=\"text-align:center;\">\n";
            echo "<tr><th></th><th><h3>面向一</h3><br>高中(職)在校成績證明30%</th><th><h3>面向二</h3><br>讀書計畫(含申請動機)50%</th><th><h3>面向三</h3><br>英語能力檢定證明20%</th></tr>\n";
            echo "<tr><th><h3>傑出90&#8593;</h3></th><td><h4>合計:".$a_gall_[1]."</h4></td><td><h4>合計:".$b_gall_[1]."</h4></td><td><h4>合計:".$c_gall_[1]."</h4></td></tr>\n";
            echo "<tr><th><h3>優 80-89</h3></th><td><h4>合計:".$a_gall_[2]."</h4></td><td><h4>合計:".$b_gall_[2]."</h4></td><td><h4>合計:".$c_gall_[2]."</h4></td></tr>\n";
            echo "<tr><th><h3>佳 70-79</h3></th><td><h4>合計:".$a_gall_[3]."</h4></td><td><h4>合計:".$b_gall_[3]."</h4></td><td><h4>合計:".$c_gall_[3]."</h4></td></tr>\n";
            echo "<tr><th><h3>可 60-69</h3></th><td><h4>合計:".$a_gall_[4]."</h4></td><td><h4>合計:".$b_gall_[4]."</h4></td><td><h4>合計:".$c_gall_[4]."</h4></td></tr>\n";
            echo "<tr><th><h3>不佳60&#8595; </h3></th><td><h4>合計:".$a_gall_[5]."</h4></td><td><h4>合計:".$b_gall_[5]."</h4></td><td><h4>合計:".$c_gall_[5]."</h4></td></tr>\n";
            echo "</tbody></table><br>";
                            
        }  
       }else { 
              $type = "error";
              $message = "Invalid File Type. Upload Excel File.";

            } 
      echo "</div>";
      
      
      
    }
  

?>
<script type="text/javascript">
        window.onload = function () {

        var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        theme: "light2", // "light1", "light2", "dark1", "dark2"
        title:{
          text: "Bar Chart"
        },
        axisY: {
          title: "人數"
        },
        data: [{        
          type: "column",  
          showInLegend: true, 
          legendMarkerColor: "grey",
          legendText: "科系",
          dataPoints: [      
            { y: <?php echo $total_[1];?>, label: "95分以上" },
            { y: <?php echo $total_[2];?>,  label: "90-94分" },
            { y: <?php echo $total_[3];?>,  label: "85-89分" },
            { y: <?php echo $total_[4];?>,  label: "80-84分" },
            { y: <?php echo $total_[5];?>,  label: "75-79分" },
            { y: <?php echo $total_[6];?>,  label: "70-74分" },
            { y: <?php echo $total_[7];?>,  label: "65-69分" },
            { y: <?php echo $total_[8];?>,  label: "60-64分" },
            { y: <?php echo $total_[9];?>,  label: "60分以下" }
          ]
        }]
      });
      chart.render();

      }
</script> 

<script type="text/javascript">
  <?php
    $max=max($total_[1],$total_[2],$total_[3],$total_[4],$total_[5],$total_[6],$total_[7],$total_[8],$total_[9]);
  ?>
  function produce(){
    t = [
  [
    {axis:"95分以上",value: <?php echo $total_[1];?>,maxvalue:<?php echo $max;?>},
    {axis:"90-94分",value: <?php echo $total_[2];?>,maxvalue:<?php echo $max;?>},
    {axis:"85-89分",value: <?php echo $total_[3];?>,maxvalue:<?php echo $max;?>},
    {axis:"80-84分",value: <?php echo $total_[4];?>,maxvalue:<?php echo $max;?>},
    {axis:"75-79分",value: <?php echo $total_[5];?>,maxvalue:<?php echo $max;?>},
    {axis:"70-74分",value: <?php echo $total_[6];?>,maxvalue:<?php echo $max;?>},
    {axis:"65-69分",value: <?php echo $total_[7];?>,maxvalue:<?php echo $max;?>},
    {axis:"60-64分",value: <?php echo $total_[8];?>,maxvalue:<?php echo $max;?>},
    {axis:"60分以下",value: <?php echo $total_[9];?>,maxvalue:<?php echo $max;?>}
    ]
  ];
    
    var mycfg = {
       radius: 5,
       w: 600,
       h: 600,
       factor: .65,
       factorLegend: .8,
       levels: 4,
       maxValue: 0,
       radians: 2 * Math.PI,
       opacityArea: 0.5,
       fontSize: 14,
       color: d3.scale.category10()
      };
  
    d3.select("svg").remove();
    RadarChart.draw("#chart", t, mycfg);
  }

</script>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  echo"<br><h3><center><p>國立嘉義大學招生專業試辦計畫".$Department."評分分布圖</p></center></h3>";
  echo "<table class='table table-striped table-hover table-bordered'>";            
  echo "<tbody style=\"text-align:center;\">\n";
  echo "<tr><th></th><th><h4>95分&#8593;</h4></th><th><h4>90-94分</h4></th><th><h4>85-89分</h4></th><th><h4>80-84分</h4></th><th><h4>75-79分</h4></th><th><h4>70-74分</h4></th><th><h4>65-69分</h4></th><th><h4>60-64分</h4></th><th><h4>60分&#8595;</h4></th></tr>\n";
  
  echo "<tr><th><h3>人數</h3></th>";
  for($i=1;$i<=9;$i++){
    echo "<td><h3>".$total_[$i]."</h3></td>";
  }
  echo "</tr>";

  echo "</tbody></table>";
  echo "<center><input type=\"button\" onclick=\"produce()\" class=\"btn btn-primary btn-md\" value=\"Draw Radar Chart\"></center>";
  echo "<center><div id=\"chart\"></div></center>";
  echo "<div id=\"chartContainer\" style=\"height: 370px; width: 100%;\"></div>"; 
}
?>

    </div>
  </div>
</div>

</body>
</html>
