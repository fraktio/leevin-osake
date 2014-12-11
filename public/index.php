<!DOCTYPE html>
<html lang="fi">
<head>
  <meta charset="utf-8">
  <title>Osake</title>
  
</head>
<body>

<?php $company = $_GET['company']; ?>

<h1><?php echo $_GET['company']; ?></h1>

<?php
	error_reporting(E_ALL);
	ini_set("display_errors", 1);			

$servername = "localhost";
$username = "root";
$password = "fraktio";
$dbname = "osake";


############# Get all companies #################


    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

    $sql = "SELECT DISTINCT company FROM rate";

    $result = $conn->query($sql);

    $arr_companies = array();

    if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
	    $arr_companies[] = array(
	    
	      "company_name" => utf8_encode($row["company"]),
	      
	    );
	}
    } else {
	echo "0 results";
    }
    $conn->close();
    $json1 = json_encode($arr_companies	);


############# Get the data #################

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

    // Default values
    if(empty($company)) {  $company = "Kone"; } else { $company = $_GET["company"]; }

    $sql = "SELECT date, lowest, highest, final FROM rate WHERE company = '".$company."' AND date > '2014-10-01'";

    $result = $conn->query($sql);
    $looper = array();

    if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
	    $looper[] = array(
	      // "company" => utf8_encode($row["company"]),
	      "date" => $row["date"],
	      "final" => (float)$row["final"],
	      "lowest" => (float)$row["lowest"],
	      "highest" => (float)$row["highest"],
	    );		
	     
	}
    } else {
	echo "0 results";
    }
    $conn->close();
    $json = json_encode($looper);

?>


  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
  
  <div id="myfirstchart" style="height: 500px;"></div>

<script>
var data = JSON.parse ('<?php echo $json; ?>');

  Morris.Area({
  element: 'myfirstchart',
  data: data,
  xkey: 'date',
  ykeys: ['lowest', 'final', 'highest'],
  labels: ['Alinhinta', 'Päätöshinta', 'Ylinhinta'],
  lineColors: ['red', 'grey', 'Green'],
  behaveLikeLine: ['True'],
  
});

</script>

<?php // die($company); ?>

<form method="get" action="">

  <select name="company">

    <?php foreach($arr_companies as $one_company): ?>

	<?php if($one_company['company_name'] == $company): ?>
	<option value="<?php echo $one_company['company_name']; ?>" selected><?php echo $one_company['company_name']; ?></option>
      
	<?php else: ?>
	<option value="<?php echo $one_company['company_name']; ?>"><?php echo $one_company['company_name']; ?></option>
 
      <?php endif; ?>

    <?php endforeach; ?>
    
   </select>
    
  <input type="submit" value="Vaihda" /> 

</form>
