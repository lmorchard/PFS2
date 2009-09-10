<?php
if (empty($config)) $config = array();

$config['primary_database'] = array(
    'hostname' => 'localhost',
    'port'     => 3306,
    'username' => 'pfs2',
    'password' => 'pfs2',
    'database' => 'pfs2_test'
);

/**
 * Shadow database configuration.  Accepts multiple shadows.  Weight MUST be non-zero
 * and weights across all the shadows MUST add up to 100.
 */
$config['shadow_databases'] = array(
    0 => array(
        'hostname' => 'localhost',
        'port'     => 3306,
        'username' => 'pfs2',
        'password' => 'pfs2',
        'database' => 'pfs2_test',
        'weight'   => 100
    )
);

/**
 * Memcache configuration.  Accepts multiple servers, indexed by host.  Weight is the
 * number of chances that the server will be chosen relative to all others.  More
 * documentation is found at http://php.oregonstate.edu/manual/en/function.Memcache-addServer.php
 */
$config['memcache_config'] = array(
    'localhost' => array(
       'port'           => '11211',
       'persistent'     => true,
       'weight'         => '1',
       'timeout'        => '1',
       'retry_interval' => 15
    )
);

/**
 * A prefix for the keys in memcache (essentially a namespace).  Useful for purging.
 * Default: AUS_
 */
if (!defined('MEMCACHE_PREFIX'))
    define('MEMCACHE_PREFIX', 'AUS_');

/**
 * The amount of time to store elements in the memcache servers.  Measured in
 * seconds.
 * Default: 60
 */
if (!defined('MEMCACHE_TIMEOUT'))
    define('MEMCACHE_TIMEOUT', 60);

