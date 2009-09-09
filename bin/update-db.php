<?php
/**
 * Utility to update database from JSON definitions
 *
 * @package    Mozilla_PFS2
 * @subpackage bin
 * @author     lorchard@mozilla.com
 */
define('ENV', 'prod');
define('APPPATH', dirname(dirname(__FILE__)));
include_once APPPATH.'/libs/Mozilla/PFS2.php';
$app = new Mozilla_PFS2();

echo "Updating database...\n";
$plugins = array();
foreach (glob(dirname(__FILE__).'/../plugins-info/*json') as $plugin_fn) {
    echo "\t" . basename($plugin_fn) . ":\n";
    list($plugin_id, $release_ids) = 
        $app->loadPlugin(file_get_contents($plugin_fn));
    echo "\t\t".json_encode(array($plugin_id, $release_ids))."\n";
    $plugins[$plugin_id] = $release_ids;
}
echo count($plugins) . " plugins updated.\n\n";
