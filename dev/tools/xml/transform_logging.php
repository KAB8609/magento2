<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

$search = $argv[1];
$files = shell_exec("find . -name $search");
$xsl = 'dev/tools/xml/logging.xslt';
$saxon = 'dev/tools/xml/saxon9he.jar';

foreach (preg_split("/((\r?\n)|(\r\n?))/", $files) as $file) {
    if (!empty($file)) {
        if (!file_exists($saxon)) {
            $url = 'http://repo1.maven.org/maven2/net/sf/saxon/Saxon-HE/9.5.1-1/Saxon-HE-9.5.1-1.jar';
            system("wget $url --output-document=$saxon");
        }
        $cmd = "java -jar $saxon -l:on -s:$file -xsl:$xsl -o:$file";
        echo "$cmd \n";
        system($cmd);
    }
}