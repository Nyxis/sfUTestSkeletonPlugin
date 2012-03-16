<?php
    include dirname(__FILE__).'/bootstrap/unit.php';

    /**
     * Mock class to access protected methods
     */
    class UTestClassMock extends UTestClass {
        public function __call($name, $args) {
            if (method_exists($this,$name)) { return call_user_func_array(array($this,$name), $args); }
            throw new BadMethodCallException('Invalid method '.$name);
        }
    }

    

    $t = new lime_test();
    $utestclass = new UTestClassMock();

    //---------------------------------------------------------------
    // UTestClass::maFonctionPublique()
    //---------------------------------------------------------------
    $t->info('UTestClass::maFonctionPublique()');

    $t->ok($utestclass->maFonctionPublique(), '::maFonctionPublique() ...');

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

    $t->ok($utestclass->maFonctionProtected(), '::maFonctionProtected() ...');

    try {
        $msg = '::maFonctionProtected() triggers an "InvalidArgumentException" when ...';
        $utestclass->maFonctionProtected();
        $t->fail($msg);
    }
    catch(InvalidArgumentException $e) { $t->pass($msg); }
        