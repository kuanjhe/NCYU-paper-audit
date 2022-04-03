<?php
	echo "<!------------------------------header.php:begin------------------------------>\n";
	echo "<div class=\"jumbotron text-center\">\n";
	echo "<h1>國立嘉義大學書面審查系統</h1>\n";
  	
  	include_once('phpqrcode/qrlib.php');
	if (!file_exists("../QRCode/QRCode_System.png")){
		QRcode::png("http://120.113.174.17/student/s1042653/M20190416/index.php", "QRCode/QRCode_System.png");
	}
	echo "<p style=\"text-align:center\"><img src=\"QRCode/QRCode_System.png\"></p>";
	echo "<a href=\"index.php\"><font size=\"5\">excel匯入頁面</font></a>&nbsp;&nbsp;";
  	echo "<a href=\"show.php\"><font size=\"5\">各科系評分表頁面</font></a>&nbsp;&nbsp;";
  	echo "<a href=\"output_excel.php\"><font size=\"5\">匯出excel檔</font></a>";
	echo "</div>\n";
	echo "<!------------------------------header.php:end-------------------------------->\n";
?>