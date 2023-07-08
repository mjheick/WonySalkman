<?php
$config = [
	'music_path' => './music',
	'music_not_files ' => [
		'.',
		'..',
	],
];

/* Get a list of files */
$file_list = [];
if (!file_exists($config['music_path'])) {
	die('music_path does not exist');
}
$d = dir($config['music_path']);
while (false !== ($entry = $d->read())) {
	if (!in_array($entry, $config['music_not_files']))
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

function play_song(song, hash)
{
  var radio = document.getElementById('radio');
  radio.innerHTML = '<audio id="player-' + hash + '" controls src="music/' + song + '">Your browser does not support the audio html element</audio>';
  var player = document.getElementById('player-' + hash);
  // before we .play, we need to add every event we can
  set_player_events(player);
  if (player.paused)
  {
    player.play();
  }
  show_player_properties(player);
}

function add_player_text(text)
{
  var display = document.getElementById('display');
  display.innerHTML = display.innerHTML + text + "<br>\n";
}

function add_event_text(text)
{
  var eventdata = document.getElementById('eventdata');
  eventdata.innerHTML = text + "\n" + eventdata.innerHTML;
}

function show_player_properties(player)
{
  var display = document.getElementById('display');
  display.innerHTML = '';
  if (player.autoplay) { add_player_text('autoplay = true'); } else { add_player_text('autoplay = false'); }
  if (player.controls) { add_player_text('controls = true'); } else { add_player_text('controls = false'); }
  add_player_text('crossOrigin = ' + player.crossOrigin);
  add_player_text('currentSrc = ' + player.currentSrc);
  if (player.defaultMuted) { add_player_text('defaultMuted = true'); } else { add_player_text('defaultMuted = false'); }
  add_player_text('defaultPlaybackRate = ' + player.defaultPlaybackRate);
  if (player.disableRemotePlayback) { add_player_text('disableRemotePlayback = true'); } else { add_player_text('disableRemotePlayback = false'); }
  add_player_text('duration = ' + player.duration);
  if (player.ended) { add_player_text('ended = true'); } else { add_player_text('ended = false'); }
  if (player.loop) { add_player_text('loop = true'); } else { add_player_text('loop = false'); }
  if (player.muted) { add_player_text('muted = true'); } else { add_player_text('muted = false'); }
  add_player_text('networkState = ' + player.networkState);
  if (player.paused) { add_player_text('paused  = true'); } else { add_player_text('paused  = false'); }
  add_player_text('playbackRate = ' + player.playbackRate);
  add_player_text('readyState  = ' + player.readyState);
  add_player_text('sinkId = ' + player.sinkId);
  add_player_text('src = ' + player.src);
  add_player_text('volume = ' + player.volume);
}

function set_player_events(player)
{
  player.addEventListener('abort', function(e){add_event_text('abort');});
  player.addEventListener('canplay', function(e){add_event_text('canplay');});
  player.addEventListener('canplaythrough', function(e){add_event_text('canplaythrough');});
  player.addEventListener('durationchange', function(e){add_event_text('durationchange');});
  player.addEventListener('emptied', function(e){add_event_text('emptied');});
  player.addEventListener('ended', function(e){add_event_text('ended');});
  player.addEventListener('error', function(e){add_event_text('error');});
  player.addEventListener('loadeddata', function(e){add_event_text('loadeddata');});
  player.addEventListener('loadedmetadata', function(e){add_event_text('loadedmetadata');});
  player.addEventListener('loadstart', function(e){add_event_text('loadstart');});
  player.addEventListener('pause', function(e){add_event_text('pause');});
  player.addEventListener('play', function(e){add_event_text('play');});
  player.addEventListener('playing', function(e){add_event_text('playing');});
  player.addEventListener('progress', function(e){add_event_text('progress');});
  player.addEventListener('ratechange', function(e){add_event_text('ratechange');});
  player.addEventListener('seeked', function(e){add_event_text('seeked');});
  player.addEventListener('seeking', function(e){add_event_text('seeking');});
  player.addEventListener('stalled', function(e){add_event_text('stalled');});
  player.addEventListener('suspend', function(e){add_event_text('suspend');});
  player.addEventListener('timeupdate', function(e){add_event_text('timeupdate');});
  player.addEventListener('volumechange', function(e){add_event_text('volumechange');});
  player.addEventListener('waiting', function(e){add_event_text('waiting');});
}
      </script>
      <style>
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
    </style>
  </head>
  <body>
  <div style="float:left; width:50%;">
    <div id="radio"></div>
    <div><a href="https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement" target="_blank">https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement</a></div>
    <did id="playlist">
      <ul>
<?php
foreach($file_list as $file)
{
	echo "\t\t\t" . '<li><a href="#' . sha1($file) . '" onclick="javascript:play_song(\'' . str_replace("'", '\\\'', $file) . '\',\'' . sha1($file) . '\'); ">' . $file . '</a></li>' . "\n";
}
?>
      </ul>
    </div>
  </div>
  <div style="float:left; width:50%;">
    <div>Javascript Data</div>
    <div><textarea id="eventdata"></textarea></div>
    <div id="display"></div>
  </div>
  <div style="clear:both;"></div>
</body>
</html>
