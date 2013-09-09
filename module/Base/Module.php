<?php
namespace Base;

class Module
{
    public function onBootstrap($e)
    {
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        $translator->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']))->setFallbackLocale('en_US');
        
        $e->getApplication()->getServiceManager()->get('ViewHelperManager')->setAlias('_', 'translate');
        $e->getApplication()->getServiceManager()->get('ViewHelperManager')->setAlias('plural', 'translateplural');        
        
    }
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
   
}
