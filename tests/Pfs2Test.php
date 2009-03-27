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
        $this->pfs2->loadData(dirname(__FILE__).'/data/data.php');
    }

    public function testPfsApplicationCreationWorks()
    {
        $this->assertTrue(TRUE);
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

}
