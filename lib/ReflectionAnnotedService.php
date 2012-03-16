<?php

class ReflectionAnnotedService {

	private $target;

    public function setTarget($targetName) {

     $this->target = $targetName;

    }

    public function getInfos() {

    	$infos = array();
    	$reflectionClass = new ReflectionAnnotatedClass($this->target);

    	// Set name
    	$infos['class'] = $reflectionClass->getName();
        $infos['methods'] = array();

        // fixtures
        if (preg_match_all('/\* \@fixtures ([\w\.\/]+)/', $reflectionClass->getDocComment(), $matches, PREG_SET_ORDER)) {

            $infos['fixtures'] = array();
            foreach($matches as $lineMatch) {
                $infos['fixtures'][] = $lineMatch[1];
            }
        }

    	// Manage method of class
    	$methods = @$reflectionClass->getMethods();

		$infosMethods = array();
    	foreach ($methods as $reflectionMethod) {

    		// Test if method has @Utest annotation
			if ($reflectionMethod->hasAnnotation('Utest')) {

                $infosMethods = array(
                    'name' => $reflectionMethod->getName(),
                    'parameters' => array(),
                    'return' => array(),
                    'exceptions' => array()
                );

                $comments = $reflectionMethod->getDocComment();
                if(preg_match_all('/\* \@([a-z]+) ([\w]+)( \$[\w]+)?/', $comments, $matches, PREG_SET_ORDER)) {
                    foreach($matches as $matchLine) {
                        switch($matchLine[1]) {
                            case 'param':
                                $key = 'parameters';
                                break;

                            case 'return':
                                $key = 'return';
                                break;

                            case 'throws':
                                $key = 'exceptions';
                                break;

                            default:
                                break;
                        }

                        if(!empty($key)) {
                            $infosMethods[$key][] = $matchLine[2];
                        }
                    }
                }

                // Set infos method
                $infos['methods'][] = $infosMethods;
    		}
    	}

		return $infos;
    }
}