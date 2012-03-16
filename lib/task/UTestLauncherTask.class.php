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
            $classes = $this->getClassesNames($arguments['classes']);
        }
        catch(InvalidArgumentException $e) {
            echo "\n"; $this->logBlock($e->getMessage().' -- abording task.', 'ERROR'); echo "\n";

            return -1;
        }

        foreach ($classes as $classname) {

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
     * @var
     */

    protected $reflectionAnnoted;

    /**
     * core method
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

        $this->reflectionAnnoted = new ReflectionAnnotedService();
        $this->reflectionAnnoted->setTarget($classname);

        return $this->reflectionAnnoted->getInfos();
    }

    /**
     * generate php unit test file for class in parameter
     * @param string $classname
     * @param mixed $tags
     */
    protected function genTest($classname, $tags)
    {
        $testPath = $this->buildPath($classname);
        $test = $this->buildTest($tags);

        if (!file_put_contents($testPath, $test)) {
            throw new RuntimeException(sprintf('Error while writting "%s" test file at path "%s".',
                $classname, $testPath
            ));
        }

        $this->logSection('file+', realpath($testPath));

        return true;
    }

    /**
     * @var int
     */
    protected $treeLevel = 0;

    /**
     * build the test path for the class at param
     * @param string $classname
     * @return string path
     */
    protected function buildPath($classname)
    {
        // find class path in symfony autoload to build same dirs in tests
        $classpath = sfSimpleAutoload::getInstance()->getClassPath($classname);

        // move backward in file system to find a test dir
        $i = 0;
        $stackdir = array();

        do {
            $localTestDir = dirname($classpath).str_repeat('/..', $i);

            if(is_dir($localTestDir.'/test/')) {
                $testDir = $localTestDir;
            }
            elseif($localTestDir == sfConfig::get('sf_root_dir')) {
                $testDir = sfConfig::get('sf_test_dir');
            }
            else {
                array_push($stackdir, preg_filter('#^.+\/([A-Za-z0-9_]+)$#', '$1', dirname($localTestDir)));
            }

            $i++;
        } while(empty($testDir));

        $this->treeLevel = 0;
        $testDir = realpath($testDir).'/test/unit';
        foreach ($stackdir as $dir) {
            if ($dir == 'lib') {
                continue;
            }

            $testDir .= '/'.$dir;

            if (!is_dir($testDir)) {
                if (!mkdir($testDir)) {
                    throw new RuntimeException(sprintf('Error while creating a directory at path "%s".',
                        $testDir
                    ));
                }

                $this->logSection('dir+', $testDir);
            }

            $this->treeLevel++;
        }

        $testPath = sprintf('%s/%sTest.gen.php',
            $testDir, ucfirst($classname)
        );

        return $testPath;
    }

    /**
     * @Twig_Environment
     */
    protected $twig;

    /**
     * returns Twig engine
     * @return Twig_Environment
     */
    protected function getTwig()
    {
        if (!empty($this->twig)) {
            return $this->twig;
        }

        $this->twig = new Twig_Environment(
            new Twig_Loader_Filesystem(dirname(__FILE__).'/skeleton'), array(
                'autoescape'       => false,
                'strict_variables' => true
            )
        );

        return $this->twig;
    }


    /**
     * returns test file skeleton
     * @return mixed
     */
    protected function getSkeleton()
    {
        return $this->getTwig()->loadTemplate('utest_skeleton.twig.php');
    }

    /**
     * injects tags var in skeleton and returns it
     * @param mixed $tags
     * @return string
     */
    protected function buildTest($tags)
    {
        return $this->getSkeleton()->render(array_replace_recursive(array(
            'bootstrap' => str_repeat('/..', $this->treeLevel)
        ), $tags));
    }
}
