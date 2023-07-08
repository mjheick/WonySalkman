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

function add_debug_audioPlayer_text(text)
{
  var display = document.getElementById('display');
  display.innerHTML = display.innerHTML + text + "<br>\n";
}

function add_debug_event_text(text)
{
  var date = new Date();
  var eventdata = document.getElementById('eventdata');
  eventdata.innerHTML = date.toISOString() + ":" + text + "\n" + eventdata.innerHTML;
  player_debug_properties();
}

function player_debug_properties()
{
  var display = document.getElementById('display');
  display.innerHTML = '';
  if (audioPlayer.autoplay) { add_debug_audioPlayer_text('autoplay = true'); } else { add_debug_audioPlayer_text('autoplay = false'); }
  if (audioPlayer.controls) { add_debug_audioPlayer_text('controls = true'); } else { add_debug_audioPlayer_text('controls = false'); }
  add_debug_audioPlayer_text('crossOrigin = ' + audioPlayer.crossOrigin);
  add_debug_audioPlayer_text('currentSrc = ' + audioPlayer.currentSrc);
  if (audioPlayer.defaultMuted) { add_debug_audioPlayer_text('defaultMuted = true'); } else { add_debug_audioPlayer_text('defaultMuted = false'); }
  add_debug_audioPlayer_text('defaultPlaybackRate = ' + audioPlayer.defaultPlaybackRate);
  if (audioPlayer.disableRemotePlayback) { add_debug_audioPlayer_text('disableRemotePlayback = true'); } else { add_debug_audioPlayer_text('disableRemotePlayback = false'); }
  add_debug_audioPlayer_text('duration = ' + audioPlayer.duration);
  if (audioPlayer.ended) { add_debug_audioPlayer_text('ended = true'); } else { add_debug_audioPlayer_text('ended = false'); }
  if (audioPlayer.loop) { add_debug_audioPlayer_text('loop = true'); } else { add_debug_audioPlayer_text('loop = false'); }
  if (audioPlayer.muted) { add_debug_audioPlayer_text('muted = true'); } else { add_debug_audioPlayer_text('muted = false'); }
  add_debug_audioPlayer_text('networkState = ' + audioPlayer.networkState);
  if (audioPlayer.paused) { add_debug_audioPlayer_text('paused  = true'); } else { add_debug_audioPlayer_text('paused  = false'); }
  add_debug_audioPlayer_text('playbackRate = ' + audioPlayer.playbackRate);
  add_debug_audioPlayer_text('readyState  = ' + audioPlayer.readyState);
  add_debug_audioPlayer_text('sinkId = ' + audioPlayer.sinkId);
  add_debug_audioPlayer_text('src = ' + audioPlayer.src);
  add_debug_audioPlayer_text('volume = ' + audioPlayer.volume);
}

function set_audioPlayer_events(audioPlayer)
{
  audioPlayer.addEventListener('abort', function(e) {add_debug_event_text('abort');});
  audioPlayer.addEventListener('canplay', function(e) {add_debug_event_text('canplay');});
  audioPlayer.addEventListener('canplaythrough', function(e) {add_debug_event_text('canplaythrough');});
  audioPlayer.addEventListener('durationchange', function(e) {add_debug_event_text('durationchange');});
  audioPlayer.addEventListener('emptied', function(e) {add_debug_event_text('emptied');});
  audioPlayer.addEventListener('ended', function(e) {
    document.getElementById('now-playing').innerHTML = '';
    add_debug_event_text('ended');
  });
  audioPlayer.addEventListener('error', function(e) {add_debug_event_text('error');});
  audioPlayer.addEventListener('loadeddata', function(e) {add_debug_event_text('loadeddata');});
  audioPlayer.addEventListener('loadedmetadata', function(e) {add_debug_event_text('loadedmetadata');});
  audioPlayer.addEventListener('loadstart', function(e) {add_debug_event_text('loadstart');});
  audioPlayer.addEventListener('pause', function(e) {add_debug_event_text('pause');});
  audioPlayer.addEventListener('play', function(e) {
    document.getElementById('now-playing').innerHTML = 'Now Playing: ' + songlist[nowPlaying];
    add_debug_event_text('play');
  });
  audioPlayer.addEventListener('playing', function(e) {add_debug_event_text('playing');});
  audioPlayer.addEventListener('progress', function(e) {add_debug_event_text('progress');});
  audioPlayer.addEventListener('ratechange', function(e) {add_debug_event_text('ratechange');});
  audioPlayer.addEventListener('seeked', function(e) {add_debug_event_text('seeked');});
  audioPlayer.addEventListener('seeking', function(e) {add_debug_event_text('seeking');});
  audioPlayer.addEventListener('stalled', function(e) {add_debug_event_text('stalled');});
  audioPlayer.addEventListener('suspend', function(e) {add_debug_event_text('suspend');});
  audioPlayer.addEventListener('timeupdate', function(e){
    /* add_debug_event_text('timeupdate'); */
  });
  audioPlayer.addEventListener('volumechange', function(e){
    masterVolume = audioPlayer.volume;
    add_debug_event_text('volumechange');
  });
  audioPlayer.addEventListener('waiting', function(e){add_debug_event_text('waiting');});
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
    </style>
  </head>
  <body>
  <div>
    <div id="radio"></div>
    <div id="now-playing"></div>
    <div id="on-deck"></div>

    <did id="playlist">
      <ul>
<?php
foreach($file_list as $file)
{
  echo "\t\t\t" . '<li><a href="#' . sha1($file) . '" onclick="javascript:play_song(\'' . sha1($file) . '\'); ">' . $file . '</a></li>' . "\n";
}
?>
      </ul>
    </div>

    <div id="search">Search <input type="text" value="" placeholder="search here..." /></div>
    <div id="debug">
      <div>Debug Data</div>
      <div><textarea id="eventdata"></textarea></div>
      <div id="display"></div>
    </div>
  </div>
</body>
</html>
