<?php 

include 'global.php';
$namerr="";
$homerr="";
if($_SERVER['REQUEST_METHOD']=="POST"){
$name=test_input($_POST["name"]);
$grade=test_input($_POST["grade"]);
$subject=test_input($_POST["subject"]);
$home=test_input($_POST["home"]);
$count=test_input($_POST["counter"]);
$experience=[];
$flag=true;
if(empty($name)){
	$flag=false;
	$namerr="name is required";
}
if(empty($home)){
	$flag=false;
	$homerr="home address is required";
}

if($flag){

	for($i=0;$i<$count;$i+=2){
		if($_POST[$i."work"]&& $_POST[$i."time"]){
	$experience[test_input($_POST[$i."work"])]=test_input($_POST[$i."time"]);
	}}




// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO teacher (name,home,grade,subject)
VALUES ('$name', '$home', '$grade','$subject')";
if($conn->query($sql)){

$sql = "SELECT `id` FROM `teacher` WHERE `name`='$name' AND `home`='$home'" ;
$result=$conn->query($sql);
$row=$result->fetch_assoc();
$id=$row['id'];
echo $conn->error;
$i=0;
foreach($experience as $school => $tenure) {
  if($i==0){

    $sql="SELECT * FROM  school where  `school` like '$school%'";
    $result=$conn->query($sql);
    echo $conn->error;
    $num=$result->num_rows;
    if($num>0){
      
      $school=$school.";$num";
      
    }
    $sql="INSERT INTO school(teacher_id,school) VALUES('$id','$school')";
    $conn->query($sql);
    echo  $conn->error;

    $i=1;
  }
$sql="INSERT INTO experience(teacher,school,tenure) VALUES ('$id','$school','$tenure')";
if($conn->query($sql)){
	echo $school;
}}

}
else{
	echo $conn->error;
}



}}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <title>Bootstrap 101 Template</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<SCRIPT language="javascript">
function add(n) {
    //Create an input type dynamically.
    var i=n
    var n= +n;
    var trid= i+'tr' ;
    var tdid= i+'td' ;
    var workname= i+'work' ;
    var timename= i+'time' ;
    var tr = document.createElement("tr");
    tr.setAttribute("id",trid)
    var foo = document.getElementById("body");
 	foo.appendChild(tr);
 	var td = document.createElement("td");
 	td.setAttribute("id",tdid);
 	var foo= document.getElementById(trid);
 	foo.appendChild(td);
 	var work=document.createElement("input");
 	work.setAttribute("type", "name");
    work.setAttribute("name", workname);
     work.setAttribute("class", "form-control");
   var foo =document.getElementById(tdid);
   foo.appendChild(work);
   var tdid= (n+1) +'td' ;
   var td = document.createElement("td");
 	td.setAttribute("id",tdid);
 	var foo= document.getElementById(trid);
 	foo.appendChild(td);
 	var time=document.createElement("input");
 	time.setAttribute("type", "number");
    time.setAttribute("name", timename);
     time.setAttribute("class", "form-control");
   var foo =document.getElementById(tdid);
   foo.appendChild(time);
   var count=document.getElementById("counter");
   count.setAttribute("value",n+2);
  
}</script>
</head>
  <body>
  

<div class="container" >
<div class="row" style="background-color:aliceblue;">
<div class="col-md-3">
  <a href="input.php" ><h1> teacher-transfer </h1></a>
</div>
</div></div><br>
<div class="row">
<div class="col-md-3"></div>
<div class="col-md-7">
<form class="form-horizontal" role="form" method="post" action="input.php">

<div class="form-group">
  <div class="row">
   <div class="col-md-1"> <label for="name">Name:</label></div>
   <div class="col-md-4"> <input type="name" class="form-control" name="name">
  </div></div></div>


<div class="form-group">
  <div class="row">
  <div class="col-md-1">  <label for="grade">grade:</label></div>
   <div class="col-md-4">  <select class="form-control" name="grade">
    <?php 
  
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM grade";
$result = $conn->query($sql);
echo $conn->error;
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
       echo "<option value=$row[id]>$row[name]</option>";}}
else{
	$conn->error;
}
$conn->close(); ?>       
   
  </select>
   <span class="error"> </span>
  </div></div></div>


<div class="form-group">
  <div class="row">
  <div class="col-md-1">  <label for="subject">subject:</label></div>
   <div class="col-md-4">  <select class="form-control" name="subject">
    <?php 
   
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM subject";
$result = $conn->query($sql);
echo $conn->error;
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
       echo "<option value=$row[id]>$row[name]</option>";}}
else{
	$conn->error;
}
$conn->close(); ?>       
   
  </select>
   <span class="error"> </span>
  </div></div></div>

<div class="form-group">
  <div class="row">
   <div class="col-md-1"> <label for="home">home address:</label></div>
   <div class="col-md-4"> <input type="name" class="form-control" name="home">
  </div></div></div>
  <h3>Work Experience</h3>
<div class="form-group">
  <div class="row">
  <table class="table table-striped">
  	<thead>
  	<tr>
  		<td>school village/area</td>
  		<td>tenure(yrs.)</td></tr>
  	</thead>
  	<tbody id="body">
  		
  	</tbody>
  </table>

<input type="hidden" value=0 id="counter" name="counter">
 <INPUT type="button" value="Add Work experience" onclick="add(document.forms[0].counter.value)"/>
<input type="submit"value="submit">
 

</form></div></div></body>