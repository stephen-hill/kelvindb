<?php

require('../vendor/autoload.php');

$config = \Noodlehaus\Config::load('../config/databases.json');

$klein = new \Klein\Klein();

$klein->respond('GET', '/[a:database]/[a:document].json', function ($request, $response) use ($config) {
    $database = $request->database;
    $document = $request->document;
    
    if ($config->has($database) === false)
    {
        return $response->code(404);
    }
    
    $dir = '../data/' . $database;
    
    if (is_dir($dir) === false)
    {
        mkdir($dir);
    }
    
    $path = $dir . "/" . $document . '.json';
    
    if (file_exists($path) === false)
    {
        return $response->code(404);
    }
    
    $raw = file_get_contents($path);
    $object = json_decode($raw);
    
    return $response->json($object);
});

$klein->respond('GET', '/[a:database]/all.json', function ($request, $response) use ($config) {
    $database = $request->database;
    $document = $request->document;
    
    if ($config->has($database) === false)
    {
        return $response->code(404);
    }
    
    $dir = '../data/' . $database;
    
    if (is_dir($dir) === false)
    {
        mkdir($dir);
    }
    
    $files = scandir ($dir, SCANDIR_SORT_ASCENDING);
    
    $documents = [];
    
    foreach($files as $filename)
    {
        if (substr($filename, -5) === '.json')
        {
            $path = $dir . "/" . $filename;
            $raw = file_get_contents($path);
            $object = json_decode($raw);
            
            $documents[] = $object;
        }
    }
    
    return $response->json($documents);
});

$klein->dispatch();
