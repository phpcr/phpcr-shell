<?php

namespace PHPCR\Shell;

class Context
{
    protected $cwd = '/';

    public function getCwd()
    {
        return $this->cwd;
    }

    public function setCwd($cwd)
    {
        $this->cwd = $cwd;
    }
}
