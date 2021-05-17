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

function removeNonBasicMultilingualPlane(string $text): string {
   return \preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $text);
}

$logfile = fopen("all.log", "r");

echo '{"conversations": [[';

if ( $logfile ) {
  $i = 0;
  while ( ( $line = fgets( $logfile ) ) !== false) {

    // Remove lines that contain actions
    $ignored = array( 'opened', '-!-', 'Users', '[@', '!säännöt', ' * ', 'Topic', 'topic', '@', 'http', '’', 'changed', 'Changed', '---', ',,', '__', '>>', '[', ']', 'Jape`:', '`', 'nimipäivää', '(Viikko', '\o/', '/o\\', 'o/', '\o', 'O/', 'RCTIC', '&quot', '}', '{', '...', '  ', '', 'Kastepiste', 'Lämpötila', 'Sää', "/''\\", 'muistutus', 'MUISTUTUS', '^', '*', '=', ':\\', '/:\//', ':/"', ":\/", "yht'", '\m/', '\\' );
    $regex_find = array( '/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]/i', '/\<(.*?)\>/i', '/[a-zA-ZäöåÄÖÅ0-9_-]+:( )/i', '/[a-zA-ZäöåÄÖÅ0-9_-]+,( )/i', '/!+[äöåÄÖÅa-zA-ZäöåÄÖÅ]/i', '/"/', '/Ã¶/', '/Ã€/', '/Ã/', '/Â/', '/  /', '/ /', '//', '//', '//', '//', '//', '//', '//', '//', '//', '//', '//', '//', '//', '//', '//', '//', '//', '//', '//', '//', '//', '//' );
    $regex_replace = array( '', '', '', '', '', '\"', 'ö', 'ä', 'ö ', '', ' ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '' );
    // Note to self, get "Input test" from: https://www.fileformat.info/info/unicode/char/0094/browsertest.htm
    
    if ( ! strposa( $line, $ignored, 1 ) ) {

      $processed_output = preg_replace( $regex_find, $regex_replace, $line );
      $caps = ucfirst( substr( $processed_output, 2) );

        // Remove line breaks
        $remove_line_breaks = preg_replace( "/\r|\n/", "", $caps );

        // Remove diamond question marks
        $remove_diamonds = preg_replace( "/\x{FFFD}/u", "", $remove_line_breaks );

        // Output with double quotes
        $output = '"' . $remove_diamonds . '",';

        // Remove control characters like 0x13
        $output_remove_cntrl = preg_replace('/[[:cntrl:]]/', '', $output);

        // Final output
        $output_final = preg_replace( '/\"\./', '"', $output_remove_cntrl );

        // Remove mapping values from from output
        echo removeNonBasicMultilingualPlane( $output_final );

        // Add line break
        echo "\r\n";

    }

    $i++;
  }

fclose( $logfile );
}

echo ']]}';
