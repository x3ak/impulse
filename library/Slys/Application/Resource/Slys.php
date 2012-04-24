<?php
/**
 * SlyS
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://zendmania.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zendmania.com so we can send you a copy immediately.
 *
 * @category   SlyS
 * @package    SlyS
 * @copyright  Copyright (c) 2010-2011 Evgheni Poleacov (http://zendmania.com)
 * @license    http://zendmania.com/license/new-bsd New BSD License
 * @version    $Id: Slys.php 1193 2011-02-17 10:43:39Z criolit $
 */

class Slys_Application_Resource_Slys
    extends Zend_Application_Resource_ResourceAbstract
{
    static $_started = false;

    public function  __construct($options = null)
    {
        parent::__construct($options);

        if(!self::$_started) {

            self::$_started = true;
            if(!empty($options['config']))
            {
                $configPath = realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR.
                        'configs'.DIRECTORY_SEPARATOR.$options['config'].DIRECTORY_SEPARATOR.'application.ini');
                if($configPath) {
                    $customConfig = new Zend_Config_Ini($configPath);
                    $customConfig = $customConfig->get(APPLICATION_ENV);

                    if (!empty($customConfig)) {
                        $appOptions = $this->getBootstrap()->getOptions();
                        $mergedOptions = $this->mergeOptions($customConfig->toArray(), $appOptions);
                        $this->getBootstrap()->getApplication()->setOptions($mergedOptions);
                        $this->getBootstrap()->setOptions($mergedOptions);
                    }
                }
            }

            $this->getBootstrap()->getApplication()
                                 ->getAutoloader()
                                 ->registerNamespace('Slys');

            if(!$this->getBootstrap()->getPluginResource('modules'))
                $this->getBootstrap()->registerPluginResource('modules');
            $this->getBootstrap()->bootstrap('modules');
        }
    }

    /**
     * Slys requirements initialization
     */
    public function init()
    {
        $router = $this->getBootstrap()
                       ->getPluginResource('frontController')
                       ->getFrontController()
                       ->getRouter();

        if($this->getBootstrap()->view === null) {
            $this->getBootstrap()->registerPluginResource('view');
        }
        $this->getBootstrap()->bootstrap('view');
        $view = $this->getBootstrap()->view;

         if (false === $view->getPluginLoader('helper')->getPaths('Slys_View_Helper')) {
            $view->addHelperPath('Slys/View/Helper', 'Slys_View_Helper');
        }

        if(!$router->hasRoute('admin'))
            $router->addRoute(
                'admin',
                new Zend_Controller_Router_Route(
                    'admin/:module/:action/*',
                    array(
                        'controller' => 'admin',
                        'action' => 'index',
                        'module'=>'default'
                   )
                )
            );

        return $this;
    }
}
