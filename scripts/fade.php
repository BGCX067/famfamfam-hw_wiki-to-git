<?php
require_once 'Console/CommandLine.php';

$parser = new Console_CommandLine();
$parser->description = 'ImageMagick Helper';
$parser->version = '0.1';
$parser->addOption('files', array(
    'short_name'  => '-f',
    'long_name'   => '--files',
    'description' => 'target file pattern',
    'help_name'   => 'PATTERN',
    'action'      => 'StoreString'
));
try {
    $result = $parser->parse();
    // do something with the result object
    if (isset($result->options['files'])) {
        $pattern = $result->options['files'];
        $files = glob($pattern);
        $convert = "C:/Program Files/ImageMagick-6.5.6-Q16-windows/ImageMagick-6.5.6-6/convert.exe";
        $postfix = '_faded';
        if (count($files)) {
            foreach ($files AS $file) {
                $ext = substr($file, -4);
                $dirname = dirname($file);
                $basename = basename($file, $ext);
                $cmd = "\"{$convert}\" {$file} -fill white -colorize 80% {$dirname}/{$basename}{$postfix}{$ext}";
                echo $cmd . PHP_EOL;
                system($cmd);
            }
        }
    }
} catch (Exception $exc) {
    $parser->displayError($exc->getMessage());
}