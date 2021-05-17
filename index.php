<?php
// Irssi log parser
function strposa( $haystack, $needles = array(), $offset = 0 ) {
  $chr = array();
  foreach( $needles as $needle ) {
    $res = strpos($haystack, $needle, $offset);
    if ($res !== false) $chr[$needle] = $res;
  }

  if ( empty( $chr ) ) return false;
  return min( $chr );
}

$logfile = fopen("logs/pulina-2010-03.log", "r");

echo 'conversation = [';

if ( $logfile ) {
  $i = 0;
  while ( ( $line = fgets( $logfile ) ) !== false) {

    // Remove lines that contain actions
    $ignored = array( 'opened', '-!-', 'Users', '[@', '!säännöt', ' * ', 'Topic', 'topic', '@', 'http', '’', 'changed', 'Changed', '---', ',,', '__', '>>', '[', ']', 'Jape`:', '`' );
    $regex_find = array( '/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]/i', '/\<(.*?)\>/i', '/[a-zA-ZäöåÄÖÅ0-9_-]+:( )/i', '/[a-zA-ZäöåÄÖÅ0-9_-]+,( )/i', '/!+[äöåÄÖÅa-zA-ZäöåÄÖÅ]/i', '/"/', '/Ã¶/', '/Ã€/', '/Ã/', '/Â/', '/\:\\\\/', '/\\\\/' );
    $regex_replace = array( '', '', '', '', '', '\"', 'ö', 'ä', 'ö ', '', ":/", "\\\\\\" );
    
    if ( ! strposa( $line, $ignored, 1 ) ) {

      $processed_output = preg_replace( $regex_find, $regex_replace, $line );
      $caps = ucfirst( substr( $processed_output, 2) );

        // Remove line breaks
        $remove_line_breaks = preg_replace( "/\r|\n/", "", $caps );

        // Remove diamond question marks
        $remove_diamonds = preg_replace( "/\x{FFFD}/u", "", $remove_line_breaks );

        // Output with double quotes
        $output = '"' . $remove_diamonds . '",';

        // Remove mapping values from from output
        echo preg_replace( '/\"\./', '"', $output );

        echo "\r\n";

    }

    $i++;
  }

fclose( $logfile );
}

echo ']';
