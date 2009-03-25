<?php
/**
 * Application bootstrap.
 *
 * @package    Mozilla_PFS2
 * @subpackage bootstrap
 * @author     lorchard@mozilla.com
 */
define('ENV', 'prod');
define('APPPATH', dirname(dirname(__FILE__)));
include_once APPPATH.'/libs/Mozilla/PFS2.php';
Mozilla_PFS2::factory()->run();
