<?php
    include dirname(__FILE__).'{{ bootstrap }}/bootstrap/unit.php';

    /**
     * Mock class to access protected methods
     */
    class {{ class }}Mock extends {{ class }} {
        public function __call($name, $args) {
            if (method_exists($this,$name)) { return call_user_func_array(array($this,$name), $args); }
            throw new BadMethodCallException('Invalid method '.$name);
        }
    }

    {% if fixtures is defined %}// loads data
    $loader = new sfPropelData();
    {% for fixture in fixtures %}$loader->loadData(__DIR__ . '{{ bootstrap }}/../fixtures/{{ fixture }}');{% endfor %}
    {% endif %}


    $t = new lime_test();
    ${{ class|lower }} = new {{ class }}Mock();

    {% for method in methods %}//---------------------------------------------------------------
    // {{ class }}::{{ method.name }}()
    //---------------------------------------------------------------
    $t->info('{{ class }}::{{ method.name }}()');

    $t->ok(${{ class|lower }}->{{ method.name }}(), '::{{ method.name }}() ...');

    {% for exception in method.exceptions %}try {
        $msg = '::{{ method.name }}() triggers an "{{ exception }}" when ...';
        ${{ class|lower }}->{{ method.name }}();
        $t->fail($msg);
    }
    catch({{ exception }} $e) {
        $t->pass($msg);
    }
    {% endfor %}{% endfor %}