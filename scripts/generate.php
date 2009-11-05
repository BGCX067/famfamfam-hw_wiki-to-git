<?php
$packer = "\"C:/program files/7-zip/7z.exe\"";

$downloads = array();
$files = array(
    array(
        'dir'       => '.',
        'summary'   => 'Silk icons',
    ),
    array(
        'dir' => 'flags',
        'summary'   => 'Flag Icons',
    ),
);
$bullet = '<img alt="" src="http://wiki.famfamfam.googlecode.com/hg/images/bullet_blue.png"/>';
$basedir = realpath("../");
foreach ($files as $data) {
    echo $data['summary'].PHP_EOL;
    $tmp = glob("{$basedir}/images/{$data['dir']}/*.*");
    $icons = array();
    foreach ($tmp AS $iconfile) {
        $ext = strtolower(substr($iconfile, -3));
        if (in_array($ext, array('png','gif'))) {
            $icons[$ext][] = $iconfile;
            $icons[null][] = $iconfile;
        }
    }

    foreach ($icons AS $filetype => $iconfiles) {
        $filename_wiki = $basedir."/".strtolower(str_replace(' ', '_', $data['summary'])).($filetype ? "_{$filetype}" : '').".wiki";
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
            file_put_contents($filename_wiki, "<img src='http://wiki.famfamfam.googlecode.com/hg/images/disk.png' alt='Download page' title='Download page' /> Download page can be found [http://code.google.com/p/famfamfam/wiki/Downloads here]".PHP_EOL, FILE_APPEND);
            
            foreach (array('zip','tar') AS $archiver) {
                $filename_archive = "filename_{$archiver}";
                $downloads[$archiver][$$filename_archive] = "<tr><td>{$bullet}</td><td>{$num_of_icons}</td><td>{$filetype} icons</td><td>[http://wiki.famfamfam.googlecode.com/hg/{$$filename_archive} {$$filename_archive}]</td></tr>".PHP_EOL;

                $filename_any = strtolower(str_replace(' ', '_', $data['summary'])).".{$archiver}";
                $downloads[$archiver][$filename_any] = "<tr><td>{$bullet}</td><td>{$num_of_icons_any}</td><td> </td><td>[http://wiki.famfamfam.googlecode.com/hg/{$filename_any} {$filename_any}]</td></tr>".PHP_EOL;
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
                $src = "http://".str_replace("//", "/", "wiki.famfamfam.googlecode.com/hg/images/".($data['dir'] != '.' ? $data['dir'] : '')."/{$basename}.{$filetype}"); 
//                $str .= "<img src='{$src}' alt='{$basename}' title='{$basename}' /> ";
                $str .= "{$src} ";
                $old_basename = $basename;
            }
            echo "  - done...".PHP_EOL;

        }
        foreach (array('zip','tar') AS $archiver) {
            $filename_archive = "filename_{$archiver}";
            echo "- Generating {$archiver} archive ({$$filename_archive}) with all {$filetype} icons...".PHP_EOL;
            @unlink($$filename_archive);
            $icondir = "{$basedir}/images/".($data['dir'] != '.' ? $data['dir'] : '');

            chdir($icondir);
            $cmd = "{$packer} a -t{$archiver} -x!flags/ {$$filename_archive} *".($filetype ? ".{$filetype}" : '')." > :NUL";
            $cmd = str_replace('//', '/', $cmd);

            echo $cmd.PHP_EOL;
            system($cmd);
            echo "  - done...".PHP_EOL;
            echo PHP_EOL;
            chdir(dirname($basedir));

        }
    }
}

$download_wiki = "Downloads.wiki";
file_put_contents($download_wiki, "#summary Downloads\n");
file_put_contents($download_wiki, "#labels Featured \n", FILE_APPEND);
file_put_contents($download_wiki, "\n", FILE_APPEND);

foreach ($downloads AS $archiver => $lines) {
    file_put_contents($download_wiki, "Following is a list of available *{$archiver}* achives".PHP_EOL, FILE_APPEND);
    file_put_contents($download_wiki, "<table>".PHP_EOL, FILE_APPEND);
    file_put_contents($download_wiki, implode('', $lines), FILE_APPEND);
    file_put_contents($download_wiki, "</table><br/>".PHP_EOL.PHP_EOL, FILE_APPEND);
}