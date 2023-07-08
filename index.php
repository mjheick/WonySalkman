<?php
$config = [
	'music_path' => './music',
	'music_extensions' => [
    'mp3',
	],
];

/* Get a list of files */
$file_list = [];
if (!file_exists($config['music_path'])) {
	die('music_path does not exist');
}
$d = dir($config['music_path']);
while (false !== ($entry = $d->read())) {
  $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
	if (in_array($ext, $config['music_extensions']))
	{
		$file_list[] = $entry;
	}
}
$d->close();

?>
<!doctype html>
<html>
  <head>
    <title>WonySalkman</title>
    <script>
/* All the happy javascript */
/* https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement */

/* Globals */
var masterVolume = 1;
var audioPlayer = null;
var songlist = [];
var playlist = [];
var nowPlaying = null;
var onDeck = null;

function play_song(hash)
{
  nowPlaying = hash;
  var radio = document.getElementById('radio');
  radio.innerHTML = '<audio id="audioPlayer-' + hash + '" controls src="music/' + songlist[nowPlaying] + '">Your browser does not support the audio html element</audio>';
  audioPlayer = document.getElementById('audioPlayer-' + hash);
  // before we .play, we need to add every event we can
  set_audioPlayer_events(audioPlayer);
  audioPlayer.volume = masterVolume;
  if (audioPlayer.paused)
  {
    audioPlayer.play();
  }
}

function set_audioPlayer_events(audioPlayer)
{
  audioPlayer.addEventListener('abort', function(e) {console.log('abort');});
  audioPlayer.addEventListener('canplay', function(e) {console.log('canplay');});
  audioPlayer.addEventListener('canplaythrough', function(e) {console.log('canplaythrough');});
  audioPlayer.addEventListener('durationchange', function(e) {console.log('durationchange');});
  audioPlayer.addEventListener('emptied', function(e) {console.log('emptied');});
  audioPlayer.addEventListener('ended', function(e) {
    document.getElementById('now-playing').innerHTML = '';
    console.log('ended');
  });
  audioPlayer.addEventListener('error', function(e) {console.log('error');});
  audioPlayer.addEventListener('loadeddata', function(e) {console.log('loadeddata');});
  audioPlayer.addEventListener('loadedmetadata', function(e) {console.log('loadedmetadata');});
  audioPlayer.addEventListener('loadstart', function(e) {console.log('loadstart');});
  audioPlayer.addEventListener('pause', function(e) {console.log('pause');});
  audioPlayer.addEventListener('play', function(e) {
    document.getElementById('now-playing').innerHTML = 'Now Playing: ' + songlist[nowPlaying];
    console.log('play');
  });
  audioPlayer.addEventListener('playing', function(e) {console.log('playing');});
  audioPlayer.addEventListener('progress', function(e) {console.log('progress');});
  audioPlayer.addEventListener('ratechange', function(e) {console.log('ratechange');});
  audioPlayer.addEventListener('seeked', function(e) {console.log('seeked');});
  audioPlayer.addEventListener('seeking', function(e) {console.log('seeking');});
  audioPlayer.addEventListener('stalled', function(e) {console.log('stalled');});
  audioPlayer.addEventListener('suspend', function(e) {console.log('suspend');});
  audioPlayer.addEventListener('timeupdate', function(e){
    /* console.log('timeupdate'); */
  });
  audioPlayer.addEventListener('volumechange', function(e){
    masterVolume = audioPlayer.volume;
    console.log('volumechange');
  });
  audioPlayer.addEventListener('waiting', function(e){console.log('waiting');});
}

function doSearch()
{
  let search_term = document.getElementById('search-text').value;
  let search_results_output = document.getElementById('search-results');
  let search_results_keys = [];
  let search_results_buffer = '';
  let k = 0;
  let hash = '';
  let songname = '';

  if (search_term.length < 3)
  {
    search_results_output.innerHTML = '';
    return;
  }
  search_term = search_term.toLowerCase();
  let songKeys = Object.keys(songlist);
  for (k = 0; k < songKeys.length; k++)
  {
    songname = songlist[songKeys[k]];
    songname = songname.toLowerCase();
    if (songname.indexOf(search_term) >= 0)
    {
      search_results_keys.push(songKeys[k]);
    }
  }
  if (search_results_keys.length == 0)
  {
    search_results_buffer = 'No results found';
  }
  else
  {
    search_results_buffer = '<ul>';
    for (k = 0; k < search_results_keys.length; k++)
    {
      hash = search_results_keys[k];
      songname = songlist[hash];
      search_results_buffer = search_results_buffer + '<li>';
      search_results_buffer = search_results_buffer + '<a href=\'#' + hash + '\' onclick="javascript:play_song(\'' + hash + '\');">';
      search_results_buffer = search_results_buffer + '<img class="small-button" src="button-play.svg" />';
      search_results_buffer = search_results_buffer + '</a>';
      search_results_buffer = search_results_buffer + '<a href=\'#' + hash + '\' onclick="javascript:add_to_playlist(\'' + hash + '\');">';
      search_results_buffer = search_results_buffer + '<img class="small-button" src="button-plus.svg" />';
      search_results_buffer = search_results_buffer + '</a>';
      search_results_buffer = search_results_buffer + songname;
      search_results_buffer = search_results_buffer + '</a>';
      search_results_buffer = search_results_buffer + '</li>';
    }
    search_results_buffer = search_results_buffer + '</ul>';
  }
  search_results_output.innerHTML = search_results_buffer;
}

/* Populate songlist[] */
<?php
foreach($file_list as $file)
{
  echo 'songlist["' . sha1($file) . '"] = "' . $file . '";' . "\n";
}
?>
      </script>
      <style>
div {
  width: 100%;
}
ul, li {
  list-style-position: inside;
  list-style-type: none;
  list-style: none;
  margin: 0;
  padding: 0;
}
li a {
  background: white;
  color: black;
  text-decoration: none;
}
li a:hover {
  font-weight: bold;
}
#id, #eventdata {
  width: 100%;
}
div#debug {
  display: none;
}
img.small-button {
  height: 16px;
  width: 16px;
}
    </style>
  </head>
  <body>
  <div>
    <div id="radio"></div>
    <div id="now-playing"></div>
    <div id="on-deck"></div>
    <did id="playlist"></div>
    <div id="search">Search <input id="search-text" type="text" value="" placeholder="search here..." onkeyup="doSearch();"/></div>
    <div id="search-results"></div>
    <div id="footer"><a href="https://github.com/mjheick/WonySalkman" target="_blank">https://github.com/mjheick/WonySalkman</a></div>
  </div>
</body>
</html>
