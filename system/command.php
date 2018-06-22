<?php

namespace Hasarius\system;

class Command
{

    abstract function parser();
    abstract function analyze();
    abstract function generate();

}