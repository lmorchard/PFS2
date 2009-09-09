<?php
/**
 * General application tests.
 *
 * @package    Mozilla_PFS2
 * @subpackage tests
 * @author     lorchard@mozilla.com
 */
define('ENV', 'test');
define('APPPATH', dirname(dirname(__FILE__)));
include_once APPPATH.'/libs/Mozilla/PFS2.php';

class Pfs2Test extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->pfs2 = new Mozilla_PFS2(dirname(__FILE__).'/conf/config.php');
        $this->pfs2->resetDatabase();
        $this->plugin_ids = $this->_loadPlugins();
    }

    public function testPfsApplicationCreationWorks()
    {
        $this->assertTrue(TRUE);
    }

    public function testPfsLaterUpdatesShouldWork()
    {
        $later_plugin_ids = $this->_loadPlugins();
        $this->assertEquals($this->plugin_ids, $later_plugin_ids);

        $criteria = array(
            'appID'        => '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}',
            'mimetype'     => 'audio/x-pn-realaudio',
            'appVersion'   => '2008052906',
            'appRelease'   => '3.0',
            'clientOS'     => 'Windows NT 5.1',
            'chromeLocale' => 'en-US'
        );

        $results = $this->pfs2->lookup($criteria);
        $result = array_shift($results);
        $this->assertEquals(
            'Real Player', $result['name'],
            'Real Player mime type should yield Real PLayer plugin'
        );

        $plugin_update = array(
            'meta' => array(
                "pfs_id" => "real-player",
                "vendor" => "Real Networks", 
                "name" => "New Ultra Fake Player", 
                "platform" => array(
                    "app_id" => "{ec8030f7-c20a-464f-9b0e-13a3a9e97384}"
                ), 
                "url" => "http://www.real.com", 
                "manual_installation_url" => "http://www.real.com/", 
                "version" => "10.5", 
            )
        );

        list($plugin_id, $release_ids) = 
            $this->pfs2->loadPlugin($plugin_update);

        $results = $this->pfs2->lookup($criteria);
        $result = array_shift($results);
        $this->assertEquals(
            'New Ultra Fake Player', $result['name'],
            'Plugin name should have changed'
        );
    }

    public function testFlashMimeTypeShouldYieldFlashPlugin()
    {
        $results = $this->pfs2->lookup(array(
            'mimetype' => 'application/x-shockwave-flash',
            'clientOS' => 'ReactOS 23.42'
        ));
        $this->assertTrue(empty($results),
            "There should be no Flash plugin for clientOS ReactOS 23.42");

        $criteria = array(
            'appID'        => '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}',
            'mimetype'     => 'application/x-shockwave-flash',
            'appVersion'   => '2008052906',
            'appRelease'   => '3.0',
            'clientOS'     => 'Windows NT 5.1',
            'chromeLocale' => 'en-US'
        );

        $results = $this->pfs2->lookup($criteria);
        $this->assertEquals(
            1, count($results),
            'Query should yield just 1 result.'
        );

        $result = array_shift($results);
        $this->assertEquals(
            'Adobe Flash Player', $result['name'],
            'Flash mimetype should yield Flash plugin'
        );
        $this->assertEquals(
            'http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-win.xpi', $result['xpi_location'],
            "Flash XPI location should use the windows version"
        );

        $criteria['clientOS'] = 'Intel Mac OS X 10.5';

        $results = $this->pfs2->lookup($criteria);
        $result = array_shift($results);
        $this->assertEquals(
            'http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-mac.xpi', $result['xpi_location'],
            "Flash XPI location should use the mac version"
        );
    }

    public function testFlashMimeTypeAndJapaneseLocaleShouldYieldJapanese()
    {
        $criteria = array(
            'appID'        => '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}',
            'mimetype'     => 'application/x-shockwave-flash',
            'appVersion'   => '2009021906',
            'appRelease'   => '3.0.7',
            'clientOS'     => 'Intel Mac OS X 10.5',
            'chromeLocale' => 'en-US'
        );

        $results = $this->pfs2->lookup($criteria);
        $result = array_shift($results);
        $this->assertEquals(
            'http://www.adobe.com/go/eula_flashplayer', $result['license_url'],
            "License URL should be English"
        );

        $criteria['chromeLocale'] = 'ja-JP';

        $results = $this->pfs2->lookup($criteria);
        $result = array_shift($results);
        $this->assertEquals(
            'http://www.adobe.com/go/eula_flashplayer_jp', $result['license_url'],
            "License URL should be Japanese"
        );
    }

    public function _loadPlugins()
    {
        $plugins = array();
        foreach (glob(dirname(__FILE__).'/../plugins-info/*json') as $plugin_fn) {
            list($plugin_id, $release_ids) = 
                $this->pfs2->loadPlugin(file_get_contents($plugin_fn));
            $this->assertTrue(null != $plugin_id, "Plugin ID should not be null");
            $this->assertTrue(!empty($release_ids), "Release IDs should not be empty");
            $plugins[$plugin_id] = $release_ids;
        }
        return $plugins;
    }

}
