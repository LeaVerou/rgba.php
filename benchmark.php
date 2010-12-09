<?php

// For simpler output
header('Content-Type: text/plain');

// The number if times to run each method
define('RUN_COUNT', 1000000);

// The string we will parse
$rgba_string = 'rgba(100, 100, 100, 1.0)';

echo 'Running each method '.RUN_COUNT.' times...'."\n\n";

// Run the preg test
$start1 = microtime(true);
for ($i = 0; $i < RUN_COUNT; $i++) {
	$_rgba_string = preg_replace('#\s+#', '', substr($rgba_string, 1));
	preg_match('#rgba\((?P<r>\d{1,3}),(?P<g>\d{1,3}),(?P<b>\d{1,3}),(?P<a>\d?\.\d+)\)#i', $_rgba_string, $color_info);
}
$end1 = microtime(true);
echo 'Preg Method: '.($end1 - $start1).' seconds'."\n";
echo 'Result: '; print_r($color_info);

// Run the string test
$start2 = microtime(true);
for ($i = 0; $i < RUN_COUNT; $i++) {
	$color_info = explode(',', str_replace(' ', '', substr($rgba_string, 5, -1)));
}
$end2 = microtime(true);
echo "\n".'String Method: '.($end2 - $start2).' seconds'."\n";
echo 'Result: '; print_r($color_info);

/* End of file benchmark.php */
