<?php

namespace App;

include __DIR__ . '/AcfFields.php';


class AppAcf
{
    protected $fields = [
    ];

    public function register()
    {
        if (!function_exists('acf_add_options_page')) {
            //acf not available
            return;
        }
        foreach ($this->fields as $f) {
            $f = new $f();
            $f->register();
        }
    }
}
