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
    // {{{ Test data

    public static $test_plugin = array(
        'meta' => array(
            'pfs_id'   => 'foobar-media',
            'name'     => 'Foobar Media Viewer',
            'vendor'   => 'Foobar',
            'filename' => 'foobar.plugin',
            'platform' => array(
                "app_id" => "{ec8030f7-c20a-464f-9b0e-13a3a9e97384}"
            )
        ),
        'aliases' => array(
            'Super Happy Future Viewer',
            'Foobar Corporation Viewer of Media',
        ),
        'mimes' => array(
            'audio/x-foobar-audio',
            'video/x-foobar-video'
        ),
        'releases' => array(
            array(
                'version' => '100.2.6',
                'guid'    => 'foobar-win-100.2.6',
                'os_name' => 'win',
                'installer_location' => 'http://example.com/foobar/win.exe',
            ),
            array(
                'version' => '100.2.6',
                'guid'    => 'foobar-mac-100.2.6',
                'os_name' => 'mac',
                'installer_location' => 'http://example.com/foobar/mac.dmg',
            ),
            array(
                'version' => '100.2.6',
                'guid'    => 'foobar-other-100.2.6',
                'installer_location' => 'http://example.com/foobar/others.zip',
            ),
            array(
                'version' => '99.9.9',
                'name'    => 'Horribly Broken Media Viewer',
                'guid'    => 'foobar-bad-99.9.9',
                'status'  => 'vulnerable',
                'vulnerability_description' => 'Kicks puppies',
                'vulnerability_url' => 'http://example.com/foobar/oops.html',
            ),
            array(
                'version' => '100.2.6',
                'guid' => 'foobar-mac-ja_JP-100.2.6',
                'os_name' => 'mac',
                'platform' => array(
                    'app_id' => '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}',
                    'locale' => 'ja-JP',
                ),
                'installer_location' => 'http://example.com/foobar/mac-ja-JP.dmg'
            ),
        )
    );

    // }}}

    /**
     * Set up for each test.
     */
    public function setUp()
    {
        $this->pfs2 = new Mozilla_PFS2(dirname(__FILE__).'/conf/config.php');
        $this->pfs2->resetDatabase();
        $this->pfs2->loadPlugin(self::$test_plugin);
    }

    /**
     * Perform some simple searches against mime-types and platforms.
     */
    public function testMimeTypeAndOsCriteriaShouldYieldCorrectPlugins()
    {

        // No plugins defined for this app or OS, so results should be empty.
        $results = $this->pfs2->lookup(array(
            'appID'    => '{abcdef123456789}',
            'mimetype' => 'audio/x-foobar-audio',
            'clientOS' => 'ReactOS 23.42'
        ));
        $this->assertTrue(empty($results),
            "There should be no plugin for clientOS ReactOS 23.42");

        // No plugins defined for this mimetype
        $results = $this->pfs2->lookup(array(
            'appID'    => '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}',
            'mimetype' => 'application/x-xyzzy-animation',
            'clientOS' => 'win'
        ));
        $this->assertTrue(empty($results),
            "There should be no plugin for application/x-xyzzy-animation");

        // Try some criteria for which results are expected:
        $criteria = array(
            'appID'        => '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}',
            'mimetype'     => array(
                'audio/x-foobar-audio',
                'video/x-foobar-video'
            ),
            'appVersion'   => '2008052906',
            'appRelease'   => '3.0',
            'clientOS'     => 'Windows NT 5.1',
            'chromeLocale' => 'en-US'
        );

        $results = $this->pfs2->lookup($criteria);

        $this->assertTrue( !empty($results[0]),
            'Plugin of pfs_id "foobar-media" should be returned');

        $this->assertEquals('100.2.6', $results[0]['latest_release'],
            'Latest version should be present and match.');

        // Assert that the plugin has expected aliases
        $this->assertTrue( !empty($results[0]['aliases']),
            'Plugin should provide aliases');
        $expected = array(
            'Foobar Corporation Viewer of Media',
            'Foobar Media Viewer',
            'Horribly Broken Media Viewer',
            'Super Happy Future Viewer',
        );
        sort($expected); sort($results[0]['aliases']);
        $this->assertEquals($expected, $results[0]['aliases']);

        // Ensure the expected versions are sent
        $this->assertTrue( !empty($results[0]['releases']['99.9.9']),
            'Release v99.9.9 of "foobar-media" should be returned');
        $this->assertTrue( !empty($results[0]['releases']['100.2.6']),
            'Release v100.2.6 of "foobar-media" should be returned');

        // Verify the names for expected versions
        foreach (array('99.9.9', '100.2.6') as $version) {
            $this->assertEquals(
                'Foobar Media Viewer', 
                $results[0]['releases'][$version]['name'],
                'Plugin should be named correctly'
            );
        }

        // Verify the GUIDs and statuses
        $this->assertEquals('foobar-win-100.2.6', 
            $results[0]['releases']['100.2.6']['guid']);
        $this->assertEquals('latest', 
            $results[0]['releases']['100.2.6']['status']);
        $this->assertEquals('foobar-bad-99.9.9', 
            $results[0]['releases']['99.9.9']['guid']);
        $this->assertEquals('vulnerable', 
            $results[0]['releases']['99.9.9']['status']);

        // Now, switch to Mac and expect one of the plugins to change.
        $criteria['clientOS'] = 'Intel Mac OS X 10.5';

        $results = $this->pfs2->lookup($criteria);

        $this->assertTrue( !empty($results[0]),
            'Plugin of pfs_id "foobar-media" should be returned');
        $this->assertTrue( !empty($results[0]['releases']['99.9.9']),
            'Release v99.9.9 of "foobar-media" should be returned');
        $this->assertTrue( !empty($results[0]['releases']['100.2.6']),
            'Release v100.2.6 of "foobar-media" should be returned');

        foreach (array('99.9.9', '100.2.6') as $version) {
            $this->assertEquals(
                'Foobar Media Viewer', 
                $results[0]['releases'][$version]['name'],
                'Plugin should be named correctly'
            );
        }
        $this->assertEquals('foobar-mac-100.2.6', 
            $results[0]['releases']['100.2.6']['guid']);
        $this->assertEquals('foobar-bad-99.9.9', 
            $results[0]['releases']['99.9.9']['guid']);

        // Now get specific with locale and expect the Mac release to change again.
        $criteria['chromeLocale'] = 'ja-JP';
        $results = $this->pfs2->lookup($criteria);
        $this->assertEquals('foobar-mac-ja_JP-100.2.6', 
            $results[0]['releases']['100.2.6']['guid']);
        $this->assertEquals('foobar-bad-99.9.9', 
            $results[0]['releases']['99.9.9']['guid']);

    }

    public function testLaterUpdatesShouldWork()
    {
        $plugin_update = array(
            'meta' => array(
                'pfs_id'   => 'foobar-media',
                'name'     => 'Foobar Media Viewer',
                'vendor'   => 'Foobar',
                'filename' => 'foobar.plugin',
                'platform' => array(
                    "app_id" => "{ec8030f7-c20a-464f-9b0e-13a3a9e97384}"
                )
            ),
            'aliases' => array(
                'Super Happy Future Viewer',
                'Foobar Corporation Viewer of Media',
            ),
            'mimes' => array(
                'audio/x-foobar-audio',
                'video/x-foobar-video'
            ),
            'releases' => array(
                array(
                    'version' => '200.9.9',
                    'guid'    => 'foobar-win-200.9.9',
                    'os_name' => 'win',
                    'installer_location' => 'http://example.com/foobar/win-200.exe',
                ),
                array(
                    'version' => '200.9.9',
                    'guid'    => 'foobar-mac-200.9.9',
                    'os_name' => 'mac',
                    'installer_location' => 'http://example.com/foobar/mac.dmg',
                ),
                array(
                    'version' => '200.9.9',
                    'guid'    => 'foobar-other-200.9.9',
                    'installer_location' => 'http://example.com/foobar/others.zip',
                ),
            )
        );
        $this->pfs2->loadPlugin($plugin_update);

        foreach (array( 'Windows NT 5.1', 'Intel Mac OS X 10.5', 'React OS' ) as $os_name ) {

            // Try some criteria for which results are expected:
            $criteria = array(
                'appID'        => '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}',
                'mimetype'     => array(
                    'audio/x-foobar-audio',
                    'video/x-foobar-video'
                ),
                'appVersion'   => '2008052906',
                'appRelease'   => '3.5',
                'clientOS'     => $os_name,
                'chromeLocale' => 'en-US'
            );

            $results = $this->pfs2->lookup($criteria);

            switch ($os_name) {
                case 'Windows NT 5.1':
                    $expected_installer = 'http://example.com/foobar/win-200.exe';
                    break;
                case 'Intel Mac OS X 10.5':
                    $expected_installer = 'http://example.com/foobar/mac.dmg';
                    break;
                case 'React OS':
                    $expected_installer = 'http://example.com/foobar/others.zip';
                    break;
            }
            $this->assertEquals($expected_installer,
                $results[0]['releases']['200.9.9']['installer_location']);

            $this->assertTrue( !empty($results[0]),
                'Plugin of pfs_id "foobar-media" should be returned');

            $this->assertEquals('200.9.9', $results[0]['latest_release'],
                'Latest version should be present and match.');

            $this->assertTrue( !empty($results[0]['releases']['200.9.9']),
                'Release v200.9.9 of "foobar-media" should be returned');
            $this->assertEquals('latest', 
                $results[0]['releases']['200.9.9']['status']);

            $this->assertTrue( !empty($results[0]['releases']['100.2.6']),
                'Release v100.2.6 of "foobar-media" should be returned');
            $this->assertEquals('outdated', 
                $results[0]['releases']['100.2.6']['status']);
            
            $this->assertTrue( !empty($results[0]['releases']['99.9.9']),
                'Release v99.9.9 of "foobar-media" should be returned');
            $this->assertEquals('vulnerable', 
                $results[0]['releases']['99.9.9']['status']);

        }
    }

}
