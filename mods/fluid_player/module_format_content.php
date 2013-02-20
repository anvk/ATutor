<?php
// input string. DO NOT CHANGE.
global $_input, $_content_base_href;

$media_matches = array();

function addIncludes() {
    $includes = '';
    
    $includes .='    <script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/lib/MyInfusion.js"></script>'."\n";

    $includes .='    <script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/lib/jquery-ui/js/jquery.ui.button.js"></script>'."\n";
    $includes .='    <script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/lib/captionator/js/captionator.js"></script>'."\n";
    $includes .='    <script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/lib/mediaelement/js/mediaelement.js"></script>'."\n";
    
    $includes .='    <!-- [if lt IE 9]<script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/lib/html5shiv/js/html5shiv.js"></script>[endif] -->'."\n";
    
    $includes .='    <script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/js/VideoPlayer_framework.js"></script>'."\n";
    $includes .='    <script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/js/VideoPlayer_showHide.js"></script>'."\n";
    $includes .='    <script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/js/VideoPlayer.js"></script>'."\n";
    $includes .='    <script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/js/VideoPlayer_html5Captionator.js"></script>'."\n";
    $includes .='    <script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/js/VideoPlayer_controllers.js"></script>'."\n";
    $includes .='    <script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/js/ToggleButton.js"></script>'."\n";
    $includes .='    <script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/js/MenuButton.js"></script>'."\n";
    $includes .='    <script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/js/VideoPlayer_media.js"></script>'."\n";
    $includes .='    <script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/js/VideoPlayer_transcript.js"></script>'."\n";
    $includes .='    <script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/js/VideoPlayer_intervalEventsConductor.js"></script>'."\n";
    $$includes .='    <script type="text/javascript" src="'.AT_BASE_HREF.'mods/fluid_player/js/VideoPlayer_uiOptions.js"></script>'."\n";
    
    return $includes;
}

function getVideoType($src) {
    $type = '';
    if (empty($src)) {
        return '';
    } else if (strpos($src, 'www.youtube.com') !== false) {
        return 'video/youtube';
    }
    
    $parts = explode('.', $src);
    if (count($parts) == 2) {
        $type = sprintf('video/%s', $parts[1]);
    }
    return $type;
}

function addVideoPlayerMarkup($options) {
    
    // If no options were passed or there are no src attribute then do nothing
    if (empty($options) || empty($options['src'])) {
        return '';
    }
    
    $videoPlayerClass = 'videoPlayer' . $options['id'];
    $videoPlayerDiv = sprintf('<div class="%s fl-videoPlayer">hola</div>', $videoPlayerClass);
    
    $containerScript = sprintf('container: ".%s"', $videoPlayerClass);
    
    $sourcesScript = array();
    foreach ($options['src'] as $src) {
        $type = getVideoType($src);
        if (empty($type)) {
            continue;
        }
        $sourcesScript[] = sprintf('{ src: "%s", type: "%s" }', $src, $type);
    }
    $sourcesScript = implode(',', $sourcesScript);
    
    $captionsScript = array();
    foreach ($options['captions'] as $caption) {
        if (empty($caption)) {
            continue;
        }
        $captionsScript[] = sprintf('{ src: "%s", type: "%s", srclang: "%s", label: "%s" }', $caption['src'], $caption['type'], $caption['srclang'], $caption['label']);
    }
    $captionsScript = implode(',', $captionsScript);
    
    $transcriptsScript = '';
    
    $videoTitle = $options['videoTitle'];
    $videoTitleScript = (!empty($videoTitle)) ? sprintf(', videoTitle: "%s"', $videoTitle) : '';
    
    $videoPlayerScript = sprintf('<script>videoOptions = {%s, options: { video : { sources: [%s], captions: [%s], transcripts: [%s] } %s }};</script>',
                                     $containerScript, $sourcesScript, $captionsScript, '', $videoTitleScript);


/*
        <div class="videoPlayer fl-videoPlayer">
        </div>
*/

/*
        <script>

            var videoOptions = {container: ".videoPlayer", options: {
                video: {
                    sources: [
                        {
                            src: "videos/ReorganizeFuture/ReorganizeFuture.mp4",
                            type: "video/mp4"
                        },
                        {
                            src: "videos/ReorganizeFuture/ReorganizeFuture.webm",
                            type: "video/webm"
                        },
                        {
                            src: "http://www.youtube.com/v/_VxQEPw1x9E",
                            type: "video/youtube"
                        }
                    ],
                    captions: [
                        {
                            src: "http://www.youtube.com/watch?v=_VxQEPw1x9E&language=en",
                            type: "text/amarajson",
                            srclang: "en",
                            label: "English"
                        },
                        {
                            src: "videos/ReorganizeFuture/ReorganizeFuture.fr.vtt",
                            type: "text/vtt",
                            srclang: "fr",
                            label: "French"
                        }
                    ],
                    transcripts: [
                        {
                            // TO TEST the transcript processing of Universal Subtitles files for transcripts,
                            // substititute the following two lines
                            src: "http://www.youtube.com/watch?v=_VxQEPw1x9E&language=en",
                            type: "text/amarajson",

                            //src: "videos/ReorganizeFuture/ReorganizeFuture.transcripts.en.json",
                            //type: "JSONcc",
                            srclang: "en",
                            label: "English"
                        },
                        {
                            src: "videos/ReorganizeFuture/ReorganizeFuture.transcripts.fr.json",
                            type: "JSONcc",
                            srclang: "fr",
                            label: "French"
                        }
                    ]
                },
                videoTitle: "A chance to reorganize our future?"
            }};
        </script>
*/
    error_log($videoPlayerDiv);
    error_log($videoPlayerScript);
    error_log($videoPlayerDiv . $videoPlayerScript);
    return $videoPlayerDiv . $videoPlayerScript;
}

// .mp4
preg_match_all("#\[media[0-9a-z\|]*\]([.\w\d]+[^\s\"]+)\.mp4\[/media\]#i", $_input, $media_matches, PREG_SET_ORDER);

if (!empty($media_matches)) {
    error_log(print_r($media_matches, true), 0);
    
    $_input .= addIncludes();
    $_input .='    <script>var videoOptions;</script>'."\n";
    
    foreach ($media_matches as $i=>$media) {
        $videoPlayerMarkup = addVideoPlayerMarkup(array('id' => $media[1] . $i,
                                            'src' => array('videos/ReorganizeFuture/ReorganizeFuture.mp4',
                                                            'videos/ReorganizeFuture/ReorganizeFuture.webm',
                                                            'http://www.youtube.com/v/_VxQEPw1x9E',
                                                            'random stuff'),
                                            'captions' => array(
                                                array('src' => 'http://www.youtube.com/watch?v=_VxQEPw1x9E&language=en',
                                                    'type' => 'text/amarajson',
                                                    'srclang' => 'en',
                                                    'label' => 'English'),
                                                array('src' => 'videos/ReorganizeFuture/ReorganizeFuture.fr.vtt',
                                                    'type' => 'text/vtt',
                                                    'srclang' => 'fr',
                                                    'label' => 'French')
                                            ),
                                            'videoTitle' => $media[1]
                                        )
        );
        
        $_input = str_replace($media[0], $videoPlayerMarkup, $_input);
    }
}



//// .mov
//preg_match_all("#\[media[0-9a-z\|]*\]([.\w\d]+[^\s\"]+)\.mov\[/media\]#i",$_input,$media_matches[],PREG_SET_ORDER);
//$media_replace[] ="<a class=\"".$flowplayerholder_class."\" style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\" href=\"".AT_BASE_HREF."get.php/".$_content_base_href."##MEDIA1##.mov\"></a>";
//
//// .mp3
//preg_match_all("#\[media[0-9a-z\|]*\](.+[^\s\"]+)\.mp3\[/media\]#i",$_input,$media_matches[],PREG_SET_ORDER);
//$media_replace[] ="<a class=\"".$flowplayerholder_class."\" style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\" href=\"".AT_BASE_HREF."get.php/".$_content_base_href."##MEDIA1##.mp3\"></a>";



/*
// Include the javascript only if:
// 1. $flowplayerholder_class is used but not defined
// 2. exclude from export common cartridge or content package
if (strpos($_input, $flowplayerholder_class) 
    && !strpos($_input, $flowplayerholder_def)
    && !strpos($_SERVER['PHP_SELF'], "ims_export.php"))
{
	$_input .= '<script type="text/javascript">
'.$flowplayerholder_def.', "'.AT_BASE_HREF.'mods/_standard/flowplayer/flowplayer-3.2.4.swf", { 
  clip: { 
  autoPlay: false,
  baseUrl: \''.AT_BASE_HREF.'get.php/'.$_content_base_href.'\'},
  plugins:  { 
    controls: { 
      buttons:true, 
      play: true,  
      scrubber: true, 
      autoHide:false
    }         
  }
});
</script>'."\n";
}
*/
?>