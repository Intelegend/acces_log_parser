<?php
$handle = fopen('access_log.txt','r') or die ('Файл access_log не удалось открыть');
$file = file_get_contents('script.json');
$google=0;
$bing=0;
$yandex=0;
$baidu=0;
$score = 0;
$score2 = 0;
$count=array();
$urlcount=0;
$traffic=0;
$views=1;
$url=array();
for($i=0;$i<!feof($handle);$i++)
{
    $dd = fgets($handle); 
    $parts = explode('"', $dd);
    $url[$i]=$parts[3];
    if (hasRequestType($parts[1], 'POST'))
    { 
        $traffic=$traffic+intval(substr($parts[2], 4));
    }
    $score++;
}

while (!feof($handle)) {

    $dd = fgets($handle); 
    $parts = explode('"', $dd);
    if($url[0]<>$parts[3])
    {
        $urlcount++;
        $url[0]=$parts[3];
    }
    $count=array(intval($parts[0]));
    $views=$views+count($count);
    
    if (hasRequestType($parts[5], 'Google')) $google++;
    if (hasRequestType($parts[5], 'Bing')) $bing++;
    if (hasRequestType($parts[5], 'Yandex')) $yandex++;
    if (hasRequestType($parts[5], 'Baidu')) $baidu++;
    if (hasRequestType($parts[2], '200')) $score++;
    if (hasRequestType($parts[2], '301')) $score2++;
    if (hasRequestType($parts[1], 'POST'))
    {
        $traffic=$traffic+intval(substr($parts[2], 4));
    }
    
}
$arr = array (
    'views'=>$views,'urls'=>$urlcount,'traffic'=>$traffic,'crawlers'=>array("Google"=>$google,"Bing"=>$bing,"Baidu"=>$baidu,"Yandex"=>$yandex),'statusCode'=>array("200"=>$score,"301"=>$score2));

echo json_encode($arr,JSON_PRETTY_PRINT);

$taskList = json_decode($file,TRUE);                    
unset($file);
$taskList[] = array ('views'=>$views,'urls'=>$urlcount,'traffic'=>$traffic,'crawlers'=>array("Google"=>$google,"Bing"=>$bing,"Baidu"=>$baidu,"Yandex"=>$yandex),'statusCode'=>array("200"=>$score,"301"=>$score2));       
file_put_contents('script.json',json_encode($taskList,JSON_PRETTY_PRINT));      
unset($taskList);     

fclose($handle);

function hasRequestType($l,$s) {
        return substr_count($l,$s) > 0;
}

?>

