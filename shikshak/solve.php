<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">

</script>
</head>
<body>



<?php
include 'global.php';
 

$id_home=[];
$pref_array=[];
$pref_array2=[];
$school_array=[];
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * from `school`";
$result=$conn->query($sql);
if($result->num_rows> 0){

    while($row=$result->fetch_assoc()){

        $school_array[$row['teacher_id']]=$row['school'];
       
    }
}

$sql = "SELECT `id`,`home` from `teacher`";
$result=$conn->query($sql);
if($result->num_rows> 0){

    while($row=$result->fetch_assoc()){

            $id_home[$row['id']]=$row['home'];

}
}


foreach ($id_home as $id=> $home) {
                $pq = new SplPriorityQueue();
        foreach ($school_array as $key => $school) {
            $school_same=split(";", $school);         
            $response=json_decode(file_get_contents('http://maps.googleapis.com/maps/api/distancematrix/json?origins='.urlencode($home).'&destinations='.urlencode($school_same[0]).'&mode=driving&language=nl-BE&sensor=false'));  
            $distance=-(int)$response->rows[0]->elements[0]->distance->text;

            $pq->insert($school,$distance);
            $pref_array2[$id][$school]=$distance;
        }
        $pref_array[$id]=$pq;
      

}


$engage_array=[];
$avn_array=[];


$id_enagage=[];

while(count($engage_array) < count($school_array)){
    foreach ($pref_array as $id => $pq) {
        if(!isset($id_enagage[$id]) || !$id_enagage[$id] ){
       if(!$pq->isEmpty()){
        $top=$pq->top();
        if(!isset($engage_array[$top])){
            $engage_array[$top]=$id;
           		$id_enagage[$id]=true;
           	 //echo "$top***$id<br>";
        }
        else{
                $old=$engage_array[$top];
             
                if(!isset($avnarray[$old])){
                $avnarray[$old]=avn($old,$conn,$pref_array2,$id_home);
                //echo "###$avnarray[$old]<br>";
             }

             if(!isset($avnarray[$id])){
                $avnarray[$id]=avn($id,$conn,$pref_array2,$id_home);
                //echo "!!!!!$avnarray[$id]<br>";
             }
            if($avnarray[$old]>$avnarray[$id]){

                    $pq->extract();

            }
            else{
                $pref_array[$old]->extract();
                $id_enagage[$old]=false;
                $id_enagage[$id]=true;
                $engage_array[$top]=$id;
                //echo "$top&&&$id----$old<br>";
            }



        }}}

}
}
$km_old_avg=0;
foreach ($school_array as $id => $school) {
	$km_old_avg+=$pref_array2[$id][$school];
	echo "$id($id_home[$id])=>$school".$pref_array2[$id][$school]."<br>";
}
$km_old_avg=$km_old_avg/count($school_array);

echo "<br><br> old avg: $km_old_avg<br>";
$km_new_avg=0;
foreach ($engage_array as $a => $b) {
    $km_new_avg+=$pref_array2[$b][$a];
    echo $b ."($id_home[$b])***$a".$pref_array2[$b][$a]."<br>";

    }

$km_new_avg=$km_new_avg/count($school_array);

echo "<br><br> new avg: $km_new_avg<br>";

foreach ($pref_array as $id => $value) {
	$avn_array[$id]=avn($id,$conn,$pref_array2,$id_home);
}
var_dump($avn_array);

function avn($id,$conn,$pref_array2,$id_home){
    $avn=0;
    $sql="SELECT `school`,`tenure` FROM `experience` where `teacher`='$id'";
    $result=$conn->query($sql);
    if($result->num_rows>0){
        while($row=$result->fetch_assoc()){
           if(isset($pref_array2[$id][$row["school"]])){
            $avn=$avn-$pref_array2[$id][$row["school"]]*$row["tenure"];
        }
        else{
        	 $school_same=split(";", $row['school']);         
            $response=json_decode(file_get_contents('http://maps.googleapis.com/maps/api/distancematrix/json?origins='.urlencode($id_home[$id]).'&destinations='.urlencode($school_same[0]).'&mode=driving&language=nl-BE&sensor=false'));  
            $distance=(int)$response->rows[0]->elements[0]->distance->text;
            $avn=$avn+$distance*$row['tenure'];
        }


    	}

    }
return $avn;
}








  ?>  <br>
  
</body>
</html>