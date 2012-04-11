<?php

// init Twig
if (!class_exists('Twig_Autoloader')) {
    require_once dirname(__FILE__).'/../vendor/Twig/lib/Twig/Autoloader.php';
    Twig_Autoloader::register();
}

/**
 * Plugin's core task
 */
class UTestLauncherTask extends sfBaseTask
{
    protected function configure()
    {
        $this->addArguments(array(
            new sfCommandArgument('classes', sfCommandArgument::REQUIRED, 'Class which have test to be generate'),
        ));

        $this->addOptions(array(
            // new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            // new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            // new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
            // add your own options here
        ));

        $this->namespace        = 'utest';
        $this->name             = 'generate';
        $this->briefDescription = '';

    }

    /**
     * core method
     */
    protected function execute($arguments = array(), $options = array())
    {
        try {
            Skeleton::create()
                ->useTestEngine('lime')
                ->bind('find_class_path', array($this, 'getClassPath'))
                ->bind('log', array($this, 'sendLog'))
                ->run($arguments['classes']);
        }
        catch(InvalidArgumentException $e) {
            echo "\n"; $this->logBlock($e->getMessage().' -- abording task.', 'ERROR'); echo "\n";

            return -1;
        }
    }

    /**
     * callback method to get classpath throught sfAutoload
     */
    public function getClassPath($classname)
    {
        return sfSimpleAutoload::getInstance()->getClassPath($classname);
    }

    /**
     * callback for log event in skeleton classes
     */
    public function sendLog($label, $msg)
    {
        $this->logSection($label, $msg);
    }


}
