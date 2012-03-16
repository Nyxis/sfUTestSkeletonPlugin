<?php

class UTestLauncherTask extends sfBaseTask
{
    protected function configure()
    {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

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

    protected function execute($arguments = array(), $options = array())
    {
        $this->log('process generating unit test');
    }
}
