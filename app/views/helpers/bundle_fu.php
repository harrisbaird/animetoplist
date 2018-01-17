<?php
/**
 * Du\BundleFu
 *
 * LICENSE
 *
 * This source file is subject to the BSD license that is available
 * through the world-wide-web at this URL:
 * https://github.com/dotsunited/du-bundlefu/blob/master/LICENSE
 *
 * @category   Du
 * @package    Du_BundleFu
 * @subpackage Integration
 * @copyright  Copyright (C) 2010 - Present, Jan Sorgalla
 * @license    https://github.com/dotsunited/du-bundlefu/blob/master/LICENSE New BSD License
 */

/**
 * BundleFuHelper
 *
 * @category   Du
 * @package    Du_BundleFu
 * @subpackage Integration
 * @author     Jan Sorgalla
 * @copyright  Copyright (C) 2010 - Present, Jan Sorgalla
 * @license    https://github.com/dotsunited/du-bundlefu/blob/master/LICENSE New BSD License
 */
class BundleFuHelper extends Helper
{
    /**
     * @var \Du\BundleFu\BundleFu
     */
    protected $_bundleFu;

    /**
     * Constructor
     */
    public function __construct()
    {
        spl_autoload_register(function($className) {
            if (strpos($className, 'DotsUnited\\BundleFu\\') === 0) {
                require str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
            }
        });
    }

    /**
     * Set the BundleFu instance
     *
     * @param \Du\BundleFu\BundleFu $bundleFu
     * @return BundleFuHelper
     */
    public function setBundleFu(\DotsUnited\BundleFu\Bundle $bundleFu)
    {
        $this->_bundleFu = $bundleFu;
        return $this;
    }

    /**
     * Get the BundleFu instance
     *
     * @return \Du\BundleFu\BundleFu
     */
    public function getBundleFu()
    {
        if (null === $this->_bundleFu) {
            $this->_bundleFu = new \DotsUnited\BundleFu\Bundle();
            $this->_bundleFu->setDocRoot(WWW_ROOT);
        }

        return $this->_bundleFu;
    }

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        $return = call_user_func_array(array($this->getBundleFu(), $method), $params);

        switch ($method) {
            case 'start':
            case 'end':
            case substr($method, 0, 3) == 'set':
                return $this;
            default:
                return $return;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            $return = $this->getBundleFu()->render();
            return $return;
        } catch (\Exception $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
            return '';
        }
    }
}
?>
