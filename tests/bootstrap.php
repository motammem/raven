<?php

require_once __DIR__ . '/../bootstrap.php';
function data($path){
    return file_get_contents(__DIR__ . '/_data/' . ltrim($path, '/'));
}