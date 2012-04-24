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
 * @version    $Id: Modules.php 1254 2011-05-04 11:47:12Z deeper $
 */
class Slys_Application_Resource_Modules extends Zend_Application_Resource_Modules
{

    public $_explicitType = 'modules';

    /**
     * Initialize modules
     *
     * @return array
     * @throws Zend_Application_Resource_Exception When bootstrap class was not found
     */
    public function init()
    {
        $appOptions = $this->getBootstrap()->getApplication()->getOptions();

        if(!empty($appOptions['resources']['doctrine']))
            $this->getBootstrap()->bootstrap('Doctrine');

        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('FrontController');
        $front = $bootstrap->getResource('FrontController');

        $modulesArray = $front->getControllerDirectory();
        $default = $front->getDefaultModule();
        $curBootstrapClass = get_class($bootstrap);
        $modules = new ArrayObject($modulesArray);
        foreach ($modules as $module => $moduleDirectory) {
            $bootstrapClass = $this->_formatModuleName($module) . '_Bootstrap';
            if (!class_exists($bootstrapClass, false)) {
                $bootstrapPath = dirname($moduleDirectory) . '/Bootstrap.php';
                if (file_exists($bootstrapPath)) {
                    $eMsgTpl = 'Bootstrap file found for module "%s" but bootstrap class "%s" not found';
                    include_once $bootstrapPath;
                    if (($default != $module)
                            && !class_exists($bootstrapClass, false)
                    ) {
                        throw new Zend_Application_Resource_Exception(sprintf(
                                        $eMsgTpl, $module, $bootstrapClass
                        ));
                    } elseif ($default == $module) {
                        if (!class_exists($bootstrapClass, false)) {
                            $bootstrapClass = 'Bootstrap';
                            if (!class_exists($bootstrapClass, false)) {
                                throw new Zend_Application_Resource_Exception(sprintf(
                                                $eMsgTpl, $module, $bootstrapClass
                                ));
                            }
                        }
                    }

                    $moduleConfigFile = realpath($moduleDirectory . '/../configs/module.ini');

                    if ($moduleConfigFile) {
                        $moduleConfig = new Zend_Config_Ini($moduleConfigFile);
                        $moduleConfig = $moduleConfig->get(APPLICATION_ENV);

                        if (!empty($moduleConfig)) {
                            $appOptions = $bootstrap->getOptions();
                            $mergedOptions = $this->mergeOptions($moduleConfig->toArray(), $appOptions);
                            if (isset($mergedOptions['bootstrap']))
                                unset($mergedOptions['bootstrap']);
                            $bootstrap->getApplication()->setOptions($mergedOptions);
                            $bootstrap->setOptions($mergedOptions);
                        }
                    }
                } else {
                    continue;
                }
            }

            if ($bootstrapClass == $curBootstrapClass) {
                // If the found bootstrap class matches the one calling this
                // resource, don't re-execute.
                continue;
            }

            // Custom modules options
            $moduleBootstrap = new $bootstrapClass($bootstrap);
            $moduleOptions = $bootstrap->getApplication()->getOption($module);
            if (!empty($moduleOptions)) {
                $moduleBootstrap->setOptions($moduleOptions);
            }

            // Slys custom module autoloader resources
            $moduleBootstrap
                    ->getResourceLoader()
                    ->addResourceTypes(array(
                        'library' => array(
                            'namespace' => 'Library',
                            'path' => 'library'
                        ),
                        'config' => array(
                            'namespace' => 'Config',
                            'path' => 'configs'
                        )
                    ));
            
            $this->_bootstraps[$module] = $moduleBootstrap;
            // Additional modulesof current modules
            $moduleModulesDir = dirname($moduleDirectory) . '/modules';
            if (is_dir($moduleModulesDir) && is_readable($moduleModulesDir)) {
                $front->addModuleDirectory($moduleModulesDir);
                $moduleModules = array_diff($front->getControllerDirectory(), $modules->getArrayCopy());
                foreach ($moduleModules as $name => $dir) {
                    $modules->offsetSet($name, $dir);
                }
            }
        }

        if(!empty($this->_bootstraps))
            foreach($this->_bootstraps as $bootstrap) {
                $bootstrap->bootstrap();
        }

        return $this->_bootstraps;
    }

}
