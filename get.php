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
    $result = ["code"=>"1","message"=>"URL UNKNOWN","url"=>"NONE","image"=>"none"];
    response($result);
    exit();
}
if(isset($_SERVER['HTTP_REFERER'])){    
    if(!strpos($_SERVER['HTTP_REFERER'],'localhost')){
        $result = ["code"=>"2","message"=>"You are not authorized to download this Video","url"=>"NONE","image"=>"none"];
        response($result);
        exit();
    }   
}
if(!isset($_SERVER['HTTP_REFERER'])){
    $result = ["code"=>"3","message"=>"UNKNOWN Source","url"=>"NONE","image"=>"none"];
    response($result);
    exit();
}

$id = uniqid();
$format = "mp3";

// Set the directory to save the downloads
$downloadsFolder = __DIR__ . DIRECTORY_SEPARATOR . 'downloads' . DIRECTORY_SEPARATOR;
// Create the directory if it doesn't exist
if (!file_exists($downloadsFolder)) {
    mkdir($downloadsFolder, 0777, true);
}

// Download the video using youtube-dlc
shell_exec('youtube-dlc -x ' . $_GET['url'] . ' -o "' . $downloadsFolder . 'video_' . $id . '.' . $format . '"');

// Find the downloaded file
$files = scandir($downloadsFolder);
foreach ($files as $file) {
    if (strpos($file, $id) !== false) {
        $downloadedFile = $file;
        break;
    }
}

// Convert to mp3 using ffmpeg
shell_exec("ffmpeg -i $downloadsFolder$downloadedFile $downloadsFolder$id.mp3");
shell_exec("ffmpeg -n -i $downloadsFolder$downloadedFile -ss 00:00:01.000 -vframes 1 $downloadsFolder$id.png");

// Set the suggested filename
$filename = 'video_' . $id . '.mp3';

// Provide the download URL and image URL in the response
$result = [
    "code" => 0,
    "message" => "SUCCESS",
    "url" => "http://localhost/youtube-dl/downloads/$id.mp3",
    "image" => "http://localhost/youtube-dl/downloads/$id.png"
];
response($result);

?>



