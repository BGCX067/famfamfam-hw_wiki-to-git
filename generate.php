<?php
$packer = "\"C:/program files/7-zip/7z.exe\"";

$files = array(
    array(
        'dir'       => '.',
        'summary'   => 'All icons',
    ),
    array(
        'dir' => 'flags',
        'summary'   => 'All flag icons',
    ),
);

foreach ($files as $data) {
    echo $data['summary'].PHP_EOL;
    $tmp = glob(dirname(__FILE__) . "/images/{$data['dir']}/*.*");
    $icons = array();
    foreach ($tmp AS $iconfile) {
        $ext = strtolower(substr($iconfile, -3));
        if (in_array($ext, array('png','gif'))) {
            $icons[$ext][] = $iconfile;
            $icons[null][] = $iconfile;
        }
    }

    foreach ($icons AS $filetype => $iconfiles) {
        $filename_wiki = strtolower(str_replace(' ', '_', $data['summary'])).($filetype ? "_{$filetype}" : '').".wiki";
        $filename_tar  = str_replace('.wiki', '.tar', $filename_wiki);
        $filename_zip  = str_replace('.wiki', '.zip', $filename_wiki);

        @unlink($filename_wiki);
        $num_of_icons = count($iconfiles);
        $num_of_icons_any = count($icons[null]);

        $basename = $old_basename = '';
        $first = true;

        if ($filetype) {
            echo "- {$num_of_icons} icons of filetype {$filetype} ".PHP_EOL;
            @unlink($filename_wiki);
            file_put_contents($filename_wiki, "#summary {$data['summary']}\n", FILE_APPEND);
            file_put_contents($filename_wiki, "#labels Featured\n", FILE_APPEND);
            file_put_contents($filename_wiki, "\n", FILE_APPEND);

            foreach (array('zip','tar') AS $archiver) {
                $filename_archive = "filename_{$archiver}";
                file_put_contents($filename_wiki, "A {$archiver} archive with all {$num_of_icons} {$filetype} icons can be downloaded here: [http://wiki.famfamfam.googlecode.com/hg/{$$filename_archive} {$$filename_archive}]\n".PHP_EOL, FILE_APPEND);

                $filename_any = strtolower(str_replace(' ', '_', $data['summary'])).".{$archiver}";
                file_put_contents($filename_wiki, "A {$archiver} archive with all {$num_of_icons_any} icons can be downloaded here: [http://wiki.famfamfam.googlecode.com/hg/{$filename_any} {$filename_any}]\n".PHP_EOL, FILE_APPEND);
            }

            echo "- Generating wiki file '{$filename_wiki}'".PHP_EOL;
            foreach ($iconfiles AS $filename_icon) {
                $basename = basename($filename_icon, ".{$filetype}");
                if (substr($basename, 0, 1) != substr($old_basename, 0, 1)) {
                    if (!$first) {
                        file_put_contents($filename_wiki, $str." ||".PHP_EOL, FILE_APPEND);
                    }
                    $str = "|| ". strtoupper(substr($basename, 0, 1)) . " || ";
                    $first = false;
                }
                $str .= "<img src='http://wiki.famfamfam.googlecode.com/hg/images".($data['dir'] != '.' ? $data['dir'] : '')."/{$basename}.{$filetype}' alt='{$basename}' title='{$basename}' /> ";
                   $old_basename = $basename;
            }
            echo "  - done...".PHP_EOL;

        }
        foreach (array('zip','tar') AS $archiver) {
            $filename_archive = "filename_{$archiver}";
            echo "- Generating {$archiver} archive ({$$filename_archive}) with all {$filetype} icons...".PHP_EOL;
            @unlink($$filename_archive);
            $icondir = dirname(__FILE__)."/images/".($data['dir'] != '.' ? $data['dir'] : '');

            chdir($icondir);
            $cmd = "{$packer} a -t{$archiver} ".dirname(__FILE__)."/{$$filename_archive} *".($filetype ? ".{$filetype}" : '')." > :NUL";
            $cmd = str_replace('//', '/', $cmd);

//            echo $cmd.PHP_EOL;
            system($cmd);
            echo "  - done...".PHP_EOL;
            echo PHP_EOL;
            chdir(dirname(__FILE__));

        }
    }
}
