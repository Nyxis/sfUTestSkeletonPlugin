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
     * core method
     */
    protected function execute($arguments = array(), $options = array())
    {
        try {
            $this->classes = $this->getClassesNames($arguments['classes']);
        }
        catch(InvalidArgumentException $e) {
            echo "\n"; $this->logBlock($e->getMessage().' -- abording task.', 'ERROR'); echo "\n";

            return -1;
        }

        foreach ($this->classes as $classname) {

            $tags = $this->getClassInfos($classname);

            try {
                $this->genTest($classname, $tags);
            }
            catch(RuntimeException $e) {
                echo "\n"; $this->logBlock($e->getMessage().' -- abording task.', 'ERROR'); echo "\n";

                return -1;
            }
        }
    }

    /**
     * return classes to generate
     * @param string $dirOrClass directory path or class name
     * @return array
     */
    protected function getClassesNames($dirOrClass)
    {
        if (is_dir($dirOrClass)) {

            $this->logSection('read-dir', sprintf('read "%s" directory', $dirOrClass));

            $classFileList = glob(sprintf('%s*.class.php', $dirOrClass));

            if(empty($classFileList)) {
                throw new InvalidArgumentException(sprintf('Directory "%s" contains no php classes', $dirOrClass));
            }

            $classes = array();
            foreach ($classFileList as $classFile) {
                $classes[] = preg_filter('/^([\w]+)\.class\.php$/', '$1', basename($classFile), 1);
            }
        }
        else {
            if (!class_exists($dirOrClass)) {
                throw new InvalidArgumentException(sprintf('Class "%s" does not exists', $dirOrClass));
            }

            $classes = array($dirOrClass);
        }

        return $classes;
    }


    /**
     * returns class infos, like comment's tags, classe and method name etc...
     * @param string $classname
     * @return array
     */
    protected function getClassInfos($classname)
    {
        $this->logSection('parsing', sprintf('Parsing class "%s"', $classname));

        // TODO @Landry

        return array();
    }

    /**
     * generate php unit test file for class in parameter
     * @param string $classname
     * @param mixed $tags
     */
    protected function genTest($classname, $tags)
    {
        $testPath = $this->buildPath($classname);

        $skeleton = $this->getSkeleton();

        $test = $this->buildTest($skeleton, $tags);

        if (!file_put_contents($testPath, $test)) {
            throw new RuntimeException(sprintf('Error while writting "%s" test file at path "%s".',
                $classname, $testPath
            ));
        }

        $this->logSection('file+', realpath($testPath));

        return true;
    }

    /**
     * build the test path for the class at param
     * @param string $classname
     * @return string path
     */
    protected function buildPath($classname)
    {
        //TODO

        return sfConfig::get('sf_root_dir').'/'.ucfirst($classname).'Test.php';
    }

    /**
     * returns test file skeleton
     * @return mixed
     */
    protected function getSkeleton()
    {
        //TODO

        return '{{ test }}';
    }

    /**
     * injects tags var in skeleton and returns it
     * @param string $skeleton
     * @param mixed $tags
     * @return string
     */
    protected function buildTest($skeleton, $tags)
    {
        return $skeleton;
    }



}
