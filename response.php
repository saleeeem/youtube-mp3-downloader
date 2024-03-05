<?php
function response($result){
    header("Content-type: text/xml");
     
    $document=new DomDocument();
    $response   =   $document->createElement('response');
    $response->setAttribute('code',$result['code']);
    $message    =   $document->createElement('message',$result['message']);
    $url        =   $document->createElement('url',$result['url']);
    $image      =   $document->createElement('image',$result['image']);
    $response->appendChild($message);
    $response->appendChild($url);
    $response->appendChild($image);
    $document->appendChild($response);
    
    print $document->saveXML();
}
if(!isset($_GET['url'])){
    $result = ["code"=>"1","message"=>"URL UnKNOWN","url"=>"NONE","image"=>"none"];
    response($result);
    exit();
}
if(isset($_SERVER['HTTP_REFERER'])){    
    if(!strpos($_SERVER['HTTP_REFERER'],'localhost')){
        $result = ["code"=>"2","message"=>"You are not authorised to download this Video","url"=>"NONE","image"=>"none"];
        response($result);
        exit();
    }   
}
if(!isset($_SERVER['HTTP_REFERER'])){
    $result = ["ecode"=>"3","message"=>"UnKNOWN Source","url"=>"NONE","image"=>"none"];
    response($result);
    exit();
}   
$id     = uniqid();
shell_exec('youtube-dl '.$_GET['url'].' -o downloads/video_'.$id); 
$file   = "";
foreach(scandir("downloads") as $f){     
    if(strpos($f,$id))
        $file = $f;
}
shell_exec("ffmpeg -i downloads/$file downloads/$file.mp3");
shell_exec("ffmpeg -n -i downloads/$file -ss 00:00:01.000 -vframes 1 downloads/$id.png");
 
$result = ["code"=>0,"message"=>"SUCCESS","url"=>"http://localhost/downloads/$file.mp3","image"=>"http://localhost/downloads/$id.png"];
response($result);