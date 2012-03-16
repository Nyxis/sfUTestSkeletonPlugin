<?php

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
     * @var string
     */
    protected $classes;

    /**
     * @var array
     */
    protected $tags = array();

    /**
     * @var
     */

    protected $reflectionAnnoted;

    /**
     * core method
     */
    protected function execute($arguments = array(), $options = array())
    {
    	$this->reflectionAnnoted = new ReflectionAnnotedService();

        $classes = $arguments['classes'];

        if (is_dir($classes)) {

            $this->logSection('read-dir', sprintf('read "%s" directory', $classes));

            $classFileList = glob(sprintf('%s*.class.php', $classes));

            if(empty($classFileList)) {
                $this->logSection('read-dir', sprintf('directory "%s" contains no php classes, abording task', $classes));
                return -1;
            }

            foreach ($classFileList as $classFile) {
                $this->classes[] = preg_filter('/^([\w]+)\.class\.php$/', '$1', basename($classFile), 1);
            }
        }
        else {
            $this->classes = array($classes);
        }

        foreach ($this->classes as $classname) {
            $this->tags[$classname] = $this->getClassInfos($classname);
        }


    }


    /**
     * returns class infos, like comment's tags, classe and method name etc...
     * @param string $classname
     * @return array
     */
    protected function getClassInfos($classname)
    {
        $this->logSection('parsing', sprintf('Parsing class "%s"', $classname));

        $this->reflectionAnnoted->setTarget($classname);

       $this->reflectionAnnoted->getInfos();

        // TODO @Landry

        return $this->reflectionAnnoted->getInfos();
    }


}
