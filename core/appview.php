<?php

$output = file_get_contents($theme_path."/app.html");

$output = str_replace("{{ title }}", $podcast_title, $output);

$output = str_replace("{{ subtitle }}", $podcast_subtitle, $output);

echo $output;

?>