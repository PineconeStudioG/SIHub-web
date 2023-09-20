<?php
    if(isset($_POST["channelName"]))
    {
        require("constances.php");

        $channel_file = fopen("included_channels.json","r");
        $channels = json_decode(fread($channel_file,filesize("included_channels.json")));
        fclose($channel_file);

        $data = Array();
        for($i = 0; $i < sizeof($channels); $i++)
        {
            if($channels[$i]->title == $_POST["channelName"])
            {
                array_push($data,$channels[$i]->title,$channels[$i]->id,$channels[$i]->uploads);
                $api_call = curl_init("https://youtube.googleapis.com/youtube/v3/playlistItems?part=snippet&key=".YOUTUBEAPIKEY."&playlistId=".$data[2]."&maxResults=10000");
                curl_setopt($api_call, CURLOPT_HEADER, 0);
                curl_setopt($api_call, CURLOPT_RETURNTRANSFER, 1);
                $response = json_decode(curl_exec($api_call));
                curl_close($api_call);
                array_push($data,Array());
                for($j = 0; $j < sizeof($response->items); $j++)
                {
                    $id = Array($response->items[$j]->snippet->resourceId->videoId,$response->items[$j]->snippet->thumbnails->medium->url,$response->items[$j]->snippet->title);
                    array_push($data[sizeof($data)-1],$id);
                }
            }
        }
        echo json_encode($data);
    }else
    {
        echo "not found";
        header("Location: error.html");
    } 
?>