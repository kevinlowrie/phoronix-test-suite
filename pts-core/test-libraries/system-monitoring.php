<?php

define("PTS_MODE", "SILENT");
define("PTS_AUTO_LOAD_OBJECTS", true);
require(getenv("PTS_DIR") . "pts-core/phoronix-test-suite.php");

phodevi::initial_setup();

switch($argv[1])
{
	case "cpu.usage":
		$call = array("cpu", "usage");
		break;
	case "mem.usage":
		$call = array("memory", "usage");
		break;
	case "system.current":
	case "system.battery-discharge-rate":
		$call = array("sys", "power");
		break;
	default:
		exit();
		break;

}

$temp_file = tempnam(getenv("HOME"), "monitor");
$scratch_file = getenv("HOME") . "/pts-system-monitoring-to-kill";
touch($scratch_file);

$run_type = $argv[2];
$timer = is_numeric($argv[3]) && $argv[3] > 0 ? $argv[3] : 5;

do
{
	$value = call_user_func(array("phodevi", "read_sensor"), $call);
	eval("\$value = " . $call_function . ";");

	if($value != -1 && !empty($value))
	{
		file_put_contents($temp_file, $value . "\n", FILE_APPEND);
	}

	clearstatcache();
	sleep($timer);
}
while(is_file($scratch_file));

$file = trim(file_get_contents($temp_file));
$results = explode("\n", $file);
$end_result = null;

switch($run_type)
{
	case "average":
		$end_result = round(array_sum($results) / count($results), 2);
		break;
	case "minimum":
		$min = $results[0];
		for($i = 1; $i < count($results); $i++)
		{
			if($results[$i] < $min)
			{
				$min = $results[$i];
			}
		}
		$end_result = $min;
		break;
	case "maximum":
		$max = $results[0];
		for($i = 1; $i < count($results); $i++)
		{
			if($results[$i] < $max)
			{
				$max = $results[$i];
			}
		}
		$end_result = $max;
		break;
	case "delta":
		$end_result = round(abs($results[0] - $results[(count($results) - 1)]), 2);
		break;
	case "all":
	case "all-comma":
		$end_result = implode(($run_type == "all-comma" ? "," : " "), $results);
		break;
}

file_put_contents(getenv("HOME") . "/pts-system-monitoring-results", $end_result);

unlink($temp_file);
unlink($scratch_file);

?>
