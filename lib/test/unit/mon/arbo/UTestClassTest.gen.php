<?php
    /**
     * Auto-generated test by sfUTestSkeletonPlugin
     * run this test with : "php symfony test:unit UTestClass"
     */

    include dirname(__FILE__).'/../../bootstrap/unit.php';

    /**
     * Mock class to access protected methods
     */
    class UTestClassMock extends UTestClass {
        public function __call($name, $args) {
            if (method_exists($this,$name)) { return call_user_func_array(array($this,$name), $args); }
            throw new BadMethodCallException('Invalid method '.$name);
        }
    }

    // loads data
    $loader = new sfPropelData();
    $loader->loadData(__DIR__ . '/../../../fixtures/UTestClass.yml');

    $t = new lime_test();
    $utestclass = new UTestClassMock();

    //---------------------------------------------------------------
    // UTestClass::maFonctionPublique()
    //---------------------------------------------------------------
    $t->info('UTestClass::maFonctionPublique()');
    $t->todo('::maFonctionPublique() have to be tested !');

    // takes : string, int
    // returns : bool

    // $t->ok($utestclass->maFonctionPublique(), '::maFonctionPublique() ... ');

    try {
        $msg = '::maFonctionPublique() triggers an "InvalidArgumentException" when ...';
        $utestclass->maFonctionPublique();
        $t->fail($msg);
    }
    catch(InvalidArgumentException $e) { $t->pass($msg); }

    //---------------------------------------------------------------
    // UTestClass::maFonctionProtected()
    //---------------------------------------------------------------
    $t->info('UTestClass::maFonctionProtected()');
    $t->todo('::maFonctionProtected() have to be tested !');

    // takes : string, int
    // returns : UTestClass

    // $t->ok($utestclass->maFonctionProtected(), '::maFonctionProtected() ... ');

    try {
        $msg = '::maFonctionProtected() triggers an "InvalidArgumentException" when ...';
        $utestclass->maFonctionProtected();
        $t->fail($msg);
    }
    catch(InvalidArgumentException $e) { $t->pass($msg); }

    try {
        $msg = '::maFonctionProtected() triggers an "LogicException" when ...';
        $utestclass->maFonctionProtected();
        $t->fail($msg);
    }
    catch(LogicException $e) { $t->pass($msg); }

    