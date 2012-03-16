<?php

    include dirname(__FILE__).'/../../../bootstrap/unit.php';

    // $loader = new sfPropelData();
    // $loader->loadData(__DIR__ . '/../../data/fixtures/InstanceBlocTest.yml');

    $t = new lime_test();

    //---------------------------------------------------------------
    // CollectionBlocParameter::buildQuery()
    //---------------------------------------------------------------
    $t->info('CollectionBlocParameter::buildQuery()');

    $t->ok($parameter->buildQuery('Article') instanceof ArticleQuery, '::buildQuery() create the good query class');

    try {
        $msg = '::buildQuery() triggers an "InvalidArgumentException" when query class does not exists.';
        $parameter->buildQuery('Articleuh');
        $t->fail($msg);
    }
    catch(InvalidArgumentException $e) {
        $t->pass($msg);
    }
