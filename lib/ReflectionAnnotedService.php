<?php

class ReflectionAnnotedService {

	private $target;

    public function setTarget($targetName) {

     $this->target = new $targetName ();

    }

    public function getInfos() {

    	$infos = array();
    	$reflectionClass = new ReflectionAnnotatedClass($this->target);

    	// Set name
    	$infos ['name'] = $reflectionClass->getName();

    	// Manage method of class
    	$methods = $reflectionClass->getMethods();

		$infosMethods = array();
    	foreach ($methods as $reflectionMethod) {

    		// Test if method has @Utest annotation
			if ($reflectionMethod->hasAnnotation('Utest')) {

    			// get parameter
			//	$parameterMethod = $reflectionMethod->getParameters();var_dump($reflectionMethod->getAnnotation('Utest'));


				$infosMethods [$reflectionMethod->getName()] = $reflectionMethod->getAnnotation('Utest');


				// Set retrun
			//	if ($reflectionMethod->hasAnnotation('Uretrun')) {
		//			$infosMethods [$reflectionMethod->getName()]['return'] = $reflectionMethod->getAnnotation('Ureturn');
		//		}

    		}
    	}


    	// Set infos method
    	$infos ['method'] = $infosMethods;

    	var_dump($infos);
		return $infos;

    }
}