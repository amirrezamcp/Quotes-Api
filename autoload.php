<?php

function classAutoloader($className) {
    $baseDIR = __DIR__ . DIRECTORY_SEPARATOR . "Controllers" . DIRECTORY_SEPARATOR;
    $className = trim($className, '\\');
    $className = explode('\\', $className)[1];
    $filePath = $baseDIR . $className . ".php";

    if(file_exists($filePath)) {
        include_once($filePath);
    }
}

spl_autoload_register("classAutoloader");