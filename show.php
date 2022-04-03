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
  function distribute($i){
    if($i>=90){
      echo "<td>{$i}</td><td></td><td></td><td></td><td></td>";
    }elseif ($i>=80&&$i<90) {
      echo "<td></td><td>{$i}</td><td></td><td></td><td></td>";
    }elseif ($i>=70&&$i<80) {
      echo "<td></td><td></td><td>{$i}</td><td></td><td></td>";
    }elseif ($i>=60&&$i<70) {
      echo "<td></td><td></td><td></td><td>{$i}</td><td></td>";
    }elseif ($i<60) {
      echo "<td></td><td></td><td></td><td></td><td>{$i}</td>";
    }
  }



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
              <button type="submit" id="submit" name="import" class="btn-submit">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div calss="container">
      <div class="row">
        <div class="col-sm-1">
        </div>
        <div class="col-sm-10">
<?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  $Year=$_POST['Year'];
  $_SESSION['Year']=$_POST['Year'];
  $Department=$_POST['Department'];

  

  $sql="SELECT `Teacher` FROM `grade` WHERE  `Year`='$Year' and `Department`='$Department' GROUP BY Teacher ORDER BY `Teacher` ASC";
  $result=mysqli_query($con,$sql);
  $numrow=mysqli_num_rows($result);
  if($numrow>=1){
    $result=mysqli_query($con,$sql);
    for($i=1;$i<=$numrow;$i++){
      $row = mysqli_fetch_assoc($result);
      $Teacher=$row['Teacher'];
      $sql2="SELECT * FROM `grade` WHERE  `Year`='$Year' and `Department`='$Department' and `Teacher`='$Teacher'";
      $result2=mysqli_query($con,$sql2);
      $numrow2=mysqli_num_rows($result2);
      echo"<h3><center><p>".$Year."學年度大學個人申請入學招生".$Department."書審評分表(".$i.") 評分人員:".$Teacher."</p></center></h3>";
      echo "<table class='table table-striped table-hover table-bordered'>";            
      echo "<tbody>\n";
      echo "<tr><th></th><th></th>";
      echo "<th colspan=\"5\"><h3><center>高中(職)在校成績證明(30%)</center></h3></th>";
      echo "<th colspan=\"5\"><h3><center>讀書計畫(含申請動機)(50%)</center></h3></th>";
      echo "<th colspan=\"5\"><h3><center>英語能力檢定證明(20%)</center></h3></th>";
      echo "<th>總分</th>";
      echo "<tr><th>編號</th><th>姓名</th>";for($j=0;$j<3;$j++){echo "<th>傑出(90~100)</th><th>優(80~89)</th><th>佳(70~79)</th><th>良(60~69)</th><th>可(~59)</th>";}
      echo "<th></th></tr>";
      if($numrow2>=1){
        $result2=mysqli_query($con,$sql2);
        for($j=0;$j<$numrow2;$j++){
          $row2 = mysqli_fetch_assoc($result2);
          
          echo "<tr><td>".$row2['ID']."</td><td>".$row2['Name']."</td>";
          echo distribute($row2['basic_knowledge_Grade']);
          echo distribute($row2['expression_ability_Grade']);
          echo distribute($row2['response_capability_Grade']);
          $average=0.3*$row2['basic_knowledge_Grade']+0.5*$row2['expression_ability_Grade']+0.2*$row2['response_capability_Grade'];
          echo "<td>".$average."</td>";
          echo "</tr>";
        }
        echo "</tbody>";
        echo "</table><br>";
      }
    }
  }
  
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
  
  
  if(isset($num2)){
    echo"<br><h3><center><p>嘉義大學".$Department."書面審查評分統計結果</p></center></h3>";
    echo "<table class='table table-striped table-hover table-bordered'>";            
    echo "<tbody style=\"text-align:center;\">\n";
    echo "<tr><th></th><th colspan=\"3\"><h3>面向一</h3><br>高中(職)在校成績證明30%</th><th colspan=\"3\"><h3>面向二</h3><br>讀書計畫(含申請動機)50%</th><th colspan=\"3\"><h3>面向三</h3><br>英語能力檢定證明20%</th></tr>\n";
    echo "<tr><th><h3>傑出90&#8593;</h3></th>";
    for($i=1;$i<=3;$i++){
      echo "<td>";
      for($j=1;$j<=$num;$j++){
      echo $teacher[$j].":".$grade[$i][$j][1]."<br>";
      }
      echo "</td><td><h4>合計</h4></td><td><h4>".$grade_all[$i][1]."</h4></td>";
    }
    echo "</tr>\n";
    
    echo "<tr><th><h3>優 80-89</h3></th>";
    for($i=1;$i<=3;$i++){
      echo "<td>";
      for($j=1;$j<=$num;$j++){
      echo $teacher[$j].":".$grade[$i][$j][2]."<br>";
      }
      echo "</td><td><h4>合計</h4></td><td><h4>".$grade_all[$i][2]."</h4></td>";
    }
    echo "</tr>\n";

    
    echo "<tr><th><h3>佳 70-79</h3></th>";
    for($i=1;$i<=3;$i++){
      echo "<td>";
      for($j=1;$j<=$num;$j++){
      echo $teacher[$j].":".$grade[$i][$j][3]."<br>";
      }
      echo "</td><td><h4>合計</h4></td><td><h4>".$grade_all[$i][3]."</h4></td>";
    }
    echo "</tr>\n";

    
    echo "<tr><th><h3>可 60-69</h3></th>";
    for($i=1;$i<=3;$i++){
      echo "<td>";
      for($j=1;$j<=$num;$j++){
      echo $teacher[$j].":".$grade[$i][$j][4]."<br>";
      }
      echo "</td><td><h4>合計</h4></td><td><h4>".$grade_all[$i][4]."</h4></td>";
    }
    echo "</tr>\n";
    
    echo "<tr><th><h3>不佳60&#8595; </h3></th>";
    for($i=1;$i<=3;$i++){
      echo "<td>";
      for($j=1;$j<=$num;$j++){
      echo $teacher[$j].":".$grade[$i][$j][5]."<br>";
      }
      echo "</td><td><h4>合計</h4></td><td><h4>".$grade_all[$i][5]."</h4></td>";
    }
    echo "</tr>\n";

    echo "</tbody></table><br>";

    $sql="SELECT * FROM `grade` WHERE  `Year`='$Year' and `Department`='$Department' GROUP BY Name ORDER BY `Name` ASC";
    $result=mysqli_query($con,$sql);
    $numrow=mysqli_num_rows($result);
    for($j=0;$j<4;$j++){
      $total[$j]=0;
    }
    for($j=1;$j<=9;$j++){
      $Average_[$j]=0;
    }
    echo"<br><h3><center><p>國立嘉義大學招生專業試辦計畫".$Department."評分分布圖</p></center></h3>";
    echo "<table class='table table-striped table-hover table-bordered'>";            
    echo "<tbody style=\"text-align:center;\">\n";
    echo "<tr><th></th><th><h4>95分&#8593;</h4></th><th><h4>90-94分</h4></th><th><h4>85-89分</h4></th><th><h4>80-84分</h4></th><th><h4>75-79分</h4></th><th><h4>70-74分</h4></th><th><h4>65-69分</h4></th><th><h4>60-64分</h4></th><th><h4>60分&#8595;</h4></th></tr>\n";
    echo "<tr><th><h3>人數</h3></th>";
    if($numrow>=1){
      $result=mysqli_query($con,$sql);
      for($i=1;$i<=$numrow;$i++){
        $row = mysqli_fetch_assoc($result);
        $Name=$row['Name'];
        $Number=$row['Number'];
        $sql2="SELECT * FROM `grade` WHERE  `Year`='$Year' and `Department`='$Department'  and `Name`='$Name' and `Number`='$Number'";
        $result2=mysqli_query($con,$sql2);
        $numrow2=mysqli_num_rows($result2);
        
        ##############檢查人數有少

        if($numrow2>=1){
          $result2=mysqli_query($con,$sql2);
          for($j=0;$j<$numrow2;$j++){
            $row2 = mysqli_fetch_assoc($result2);
            $total[$j]=$row2['Average'];
          }
          $Average=($total[0]+$total[1]+$total[2]+$total[3])/$numrow2;
          if($Average>=95){
            $Average_[1]++;
          }elseif ($Average>=90&&$Average<95) {
            $Average_[2]++;
          }elseif ($Average>=85&&$Average<90) {
            $Average_[3]++;
          }elseif ($Average>=80&&$Average<85) {
            $Average_[4]++;
          }elseif ($Average>=75&&$Average<80) {
            $Average_[5]++;
          }elseif ($Average>=70&&$Average<75) {
            $Average_[6]++;
          }elseif ($Average>=65&&$Average<70) {
            $Average_[7]++;
          }elseif ($Average>=60&&$Average<65) {
            $Average_[8];
          }elseif ($Average<60) {
            $Average_[9]++;
          }
        }
      }
    }
    for($i=1;$i<=9;$i++){
      echo "<td><h3>".$Average_[$i]."</h3></td>";
    }
    echo "</tr>";
    echo "</tbody></table>";
    


    
  }else{
    echo "<h3><center>尚未上傳資料</center></h3>";
  }
  
  
  echo "<center><input type=\"button\" onclick=\"produce()\" class=\"btn btn-primary btn-md\" value=\"Draw Radar Chart\"></center>";
  echo "<center><div id=\"chart\"></div></center>";
  echo "<div id=\"chartContainer\" style=\"height: 370px; width: 100%;\"></div>";





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
            { y: <?php echo $Average_[1];?>, label: "95分以上" },
            { y: <?php echo $Average_[2];?>,  label: "90-94分" },
            { y: <?php echo $Average_[3];?>,  label: "85-89分" },
            { y: <?php echo $Average_[4];?>,  label: "80-84分" },
            { y: <?php echo $Average_[5];?>,  label: "75-79分" },
            { y: <?php echo $Average_[6];?>,  label: "70-74分" },
            { y: <?php echo $Average_[7];?>,  label: "65-69分" },
            { y: <?php echo $Average_[8];?>,  label: "60-64分" },
            { y: <?php echo $Average_[9];?>,  label: "60分以下" }
          ]
        }]
      });
      chart.render();

      }
</script> 

<script type="text/javascript">
  <?php
    $max=max($Average_[1],$Average_[2],$Average_[3],$Average_[4],$Average_[5],$Average_[6],$Average_[7],$Average_[8],$Average_[9]);
  ?>
  function produce(){
    t = [
  [
    {axis:"95分以上",value: <?php echo $Average_[1];?>,maxvalue:<?php echo $max;?>},
    {axis:"90-94分",value: <?php echo $Average_[2];?>,maxvalue:<?php echo $max;?>},
    {axis:"85-89分",value: <?php echo $Average_[3];?>,maxvalue:<?php echo $max;?>},
    {axis:"80-84分",value: <?php echo $Average_[4];?>,maxvalue:<?php echo $max;?>},
    {axis:"75-79分",value: <?php echo $Average_[5];?>,maxvalue:<?php echo $max;?>},
    {axis:"70-74分",value: <?php echo $Average_[6];?>,maxvalue:<?php echo $max;?>},
    {axis:"65-69分",value: <?php echo $Average_[7];?>,maxvalue:<?php echo $max;?>},
    {axis:"60-64分",value: <?php echo $Average_[8];?>,maxvalue:<?php echo $max;?>},
    {axis:"60分以下",value: <?php echo $Average_[9];?>,maxvalue:<?php echo $max;?>}
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


</div>