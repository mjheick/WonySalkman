<?php
$config = [
	'music_path' => './music',
	'music_extensions' => [
    'mp3',
	],
];

/* Get a list of files */
$list_of_audio_files = [];
if (!file_exists($config['music_path'])) {
	die('music_path does not exist');
}
$d = dir($config['music_path']);
while (false !== ($entry = $d->read())) {
  $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
	if (in_array($ext, $config['music_extensions']))
	{
		$list_of_audio_files[] = $entry;
	}
}
$d->close();

?>
<!doctype html>
<html>
  <head>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>WonySalkman</title>
    <script>
/* All the happy javascript */
/* https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement */

/* Globals */
var songlist = []; /* array */
var audioPlayer = null; /* object */
var playlist = null; /* array */
var nowPlaying = null; /* string */
var onDeck = null; /* string */
var visuallyDark = null; /* string */

/* Browser Session */
function loadSession()
{
  console.log('loading from localStorage...');
  if (localStorage.getItem("playlist") !== null)
  {
    playlist = localStorage.getItem("playlist");
  }
  else
  {
    playlist = [];
  }
  if (localStorage.getItem("nowPlaying") !== null)
  {
    nowPlaying = localStorage.getItem("nowPlaying");
  }
  else
  {
    nowPlaying = '';
  }
  if (localStorage.getItem("onDeck") !== null)
  {
    onDeck = localStorage.getItem("onDeck");
  }
  else
  {
    onDeck = '';
  }
  if (localStorage.getItem("visuallyDark") !== null)
  {
    visuallyDark = localStorage.getItem("visuallyDark");
  }
  else
  {
    visuallyDark = 'light';
  }

  /* Do we have an audio object? */
  if (typeof audioPlayer === 'object')
  {
    if (localStorage.getItem("ap.currentTime") !== null)
    {
        audioPlayer.currentTime = localStorage.getItem("ap.currentTime");
    }
    else
    {
      audioPlayer.currentTime = 0;
    }
    if (localStorage.getItem("ap.src") !== null)
    {
        audioPlayer.src = localStorage.getItem("ap.src");
    }
    else
    {
      audioPlayer.src = 'data:audio/wav;base64,UklGRjQAAABXQVZFZm10IBIAAAABAAEAQB8AAEAfAAABAAgAAABmYWN0BAAAAAEAAABkYXRhAQAAAIAA';
    }
    if (localStorage.getItem("ap.volume") !== null)
    {
        audioPlayer.volume = localStorage.getItem("ap.volume");
    }
    else
    {
      audioPlayer.volume = 1;
    }
  }
}

function saveSession()
{
  console.log('saving...');
  localStorage.setItem("ap.volume", audioPlayer.volume);
  localStorage.setItem("ap.currentTime", audioPlayer.currentTime);
  localStorage.setItem("ap.src", audioPlayer.src);
  localStorage.setItem("playlist", playlist);
  localStorage.setItem("nowPlaying", nowPlaying);
  localStorage.setItem("onDeck", onDeck);
  localStorage.setItem("visuallyDark", visuallyDark);
}

function show_playlist()
{
  let pl = document.getElementById('playlist');
  let pl_idx = 0;
  let pl_text = '<div>Playlist</div>';

  /* Playlist is a list of songlist ids. Just resolve them in the order that's present. */
  if (playlist.length == 0)
  {
    playlist = [];
    pl_text = pl_text + 'empty';
    pl.innerHTML = pl_text;
    return;
  }
  for (pl_idx = 0; pl_idx < playlist.length; pl_idx++)
  {
    pl_text = pl_text + '<div>' + songlist[playlist[pl_idx]] + '</div>';
  }
  pl.innerHTML = pl_text;
}

function add_to_playlist(id)
{
  playlist.push(id);
  show_playlist();
}

function flipVisual()
{
  if (visuallyDark == 'dark')
  {
    visuallyDark = 'light';
  }
  else
  {
    visuallyDark = 'dark';
  }
  setVisual();
}

function setVisual()
{
  let t = ''; /* "text" color */
  let b = ''; /* Background color */
  if (visuallyDark == 'dark')
  {
    t = 'white';
    b = 'black';
  }
  else
  {
    t = 'black';
    b = 'white';
  }
  let tags = ['a', 'input', 'img'];
  document.getElementsByTagName('body')[0].style.color = t;
  document.getElementsByTagName('body')[0].style.background = b;
  for (tag = 0; tag < tags.length; tag++)
  {
    for (let x = 0; x < document.getElementsByTagName(tags[tag]).length; x++)
    {
      document.getElementsByTagName(tags[tag])[x].style.color = t;
      document.getElementsByTagName(tags[tag])[x].style.background = b;
    }
  }
  saveSession(); /* Save the value since we flip the value for "next" below this line */
}



function setup_radio()
{
  audioPlayer = document.getElementById('audioPlayer');
  audioPlayer.addEventListener('abort', function(e) {console.log('abort');});
  audioPlayer.addEventListener('canplay', function(e) {console.log('canplay');});
  audioPlayer.addEventListener('canplaythrough', function(e) {console.log('canplaythrough');});
  audioPlayer.addEventListener('durationchange', function(e) {console.log('durationchange');});
  audioPlayer.addEventListener('emptied', function(e) {console.log('emptied');});
  audioPlayer.addEventListener('ended', function(e) {
    document.getElementById('now-playing').innerHTML = '';
    saveSession();
    console.log('ended');
  });
  audioPlayer.addEventListener('error', function(e) {console.log('error');});
  audioPlayer.addEventListener('loadeddata', function(e) {console.log('loadeddata');});
  audioPlayer.addEventListener('loadedmetadata', function(e) {console.log('loadedmetadata');});
  audioPlayer.addEventListener('loadstart', function(e) {console.log('loadstart');});
  audioPlayer.addEventListener('pause', function(e) {
    console.log('pause');
  });
  audioPlayer.addEventListener('play', function(e) {
    document.getElementById('now-playing').innerHTML = 'Now Playing: ' + songlist[nowPlaying];
    saveSession();
    console.log('play');
  });
  audioPlayer.addEventListener('playing', function(e) {
    console.log('playing');
  });
  audioPlayer.addEventListener('progress', function(e) {console.log('progress');});
  audioPlayer.addEventListener('ratechange', function(e) {console.log('ratechange');});
  audioPlayer.addEventListener('seeked', function(e) {console.log('seeked');});
  audioPlayer.addEventListener('seeking', function(e) {console.log('seeking');});
  audioPlayer.addEventListener('stalled', function(e) {console.log('stalled');});
  audioPlayer.addEventListener('suspend', function(e) {console.log('suspend');});
  audioPlayer.addEventListener('timeupdate', function(e){
    saveSession();
    /* console.log('timeupdate'); */
  });
  audioPlayer.addEventListener('volumechange', function(e){
    saveSession();
    console.log('volumechange');
  });
  audioPlayer.addEventListener('waiting', function(e){console.log('waiting');});
}

function play_song(hash)
{
  nowPlaying = hash;
  audioPlayer.src = "<?php echo $config['music_path']; ?>/" + songlist[nowPlaying];
  if (audioPlayer.paused)
  {
    audioPlayer.play();
  }
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
      search_results_buffer = search_results_buffer + '<img class="small-button" src="button-play.svg" title="play now" />';
      search_results_buffer = search_results_buffer + '</a>';
      search_results_buffer = search_results_buffer + '<a href=\'#' + hash + '\' onclick="javascript:add_to_playlist(\'' + hash + '\');">';
      search_results_buffer = search_results_buffer + '<img class="small-button" src="button-plus.svg" title="add to playlist" />';
      search_results_buffer = search_results_buffer + '</a>';
      search_results_buffer = search_results_buffer + songname;
      search_results_buffer = search_results_buffer + '</a>';
      search_results_buffer = search_results_buffer + '</li>';
    }
    search_results_buffer = search_results_buffer + '</ul>';
  }
  search_results_output.innerHTML = search_results_buffer;
}

function initialize()
{
  setup_radio();
  loadSession();
  setVisual();
  show_playlist();
  doSearch();
}

/* Populate songlist[] */
<?php
foreach($list_of_audio_files as $file)
{
  echo 'songlist["' . sha1($file) . '"] = "' . $file . '";' . "\n";
}
?>
      </script>
      <style>
body, a {
}
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
  /* background: white; */
  height: 16px;
  width: 16px;
}
    </style>
  </head>
  <body>
  <div>
    <div id="radio"><audio id="audioPlayer" controls src="data:audio/wav;base64,UklGRjQAAABXQVZFZm10IBIAAAABAAEAQB8AAEAfAAABAAgAAABmYWN0BAAAAAEAAABkYXRhAQAAAIAA">Your browser does not support the audio html element</audio></div>
    <div id="now-playing"></div>
    <div id="on-deck"></div>
    <did id="playlist"></div>
    <div id="search">Search <input id="search-text" type="text" value="" placeholder="search here..." onkeyup="doSearch();"/></div>
    <div id="search-results"></div>
    <div id="footer"><a href="https://github.com/mjheick/WonySalkman" target="_blank">https://github.com/mjheick/WonySalkman</a></div>
    <div><button onclick="flipVisual();">Dark/Light</button></div>
  </div>
</body>
<script>
window.addEventListener("load", (event) => {
  initialize();
});
</script>
</html>
