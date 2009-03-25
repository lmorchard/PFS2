<?php
/**
 * Main class for PFS2 application
 *
 * @package    Mozilla_PFS2
 * @subpackage libraries
 * @author     lorchard@mozilla.com
 */
class Mozilla_PFS2
{

    /**
     *
     */
    public function run()
    {
        $params = $this->getParams(array(
            'mimetype' => 'application/x-java-appletapp',
            'ID' => '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}',
            'appVersion' => '1.5.0.4',
            'clientOS' => 'Win',
            'chromeLocale' => 'en-US',
            'reqVersion' => '1',
            'id' => '{49f3fc85-dcfe-4e42-9301-226ebe658509}',
            'version' => '0.6.1',
            'maxAppVersion' => '2.0.0.*',
            'status' => 'userEnabled,incompatible',
            'appID' => '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}',
            'appVersion' => '3.0b3pre',
            'appOS' => 'Darwin',
            'appABI' => 'x86-gcc3',
            'locale' => 'en-US',
        ));
    }

    /**
     * Get parameters with a set of supplied defaults.
     *
     * @param  array Set of expected parameters and defaults
     * @return array
     */
    public function getParams($defaults)
    {
        $params = array();
        foreach ($params as $name=>$default) {
            if (!empty($_POST[$name])) {
                $params[$name] = $_POST[$name];
            } elseif (!empty($_GET[$name])) {
                $params[$name] = $_GET[$name];
            } else {
                $params[$name] = $default;
            }
        }
        return $params;
    }

    /**
     * Constructor, initialize the application environment.
     */
    public function __construct()
    {
        // Set up an include path that covers libs and vendors
        set_include_path(join(PATH_SEPARATOR, array_merge(
            array(APPPATH.'/libs'),
            glob(APPPATH.'/vendor/*', GLOB_ONLYDIR),
            array(get_include_path())
        )));

        // Set up the class namespace autoloader.
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Class autoloader, transforms underscores to path separators.
     *
     * @param string Class name
     * @return boolean Class was loaded.
     */
    public function autoload($class)
    {
        $file = str_replace('_', '/', $class);
        require_once $file;
        return TRUE;
    }

    /**
     * Construct and return an instance of this class.
     *
     * @return Mozilla_PFS2
     */
    public function factory()
    {
        return new self();
    }

}
