<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bimathew
 * Date: 7/31/13
 * Time: 10:39 AM
 * To change this template use File | Settings | File Templates.
 */


class psrx
{

    const FOUR_SPACES = '    ';
    private $path = null;
    private $namespace = array();
    private $splitLine = 0;
    private $reservedKeyWords = array(
        'Abstract',
        'Interface',
        'Array',
        'Exception',
        'Default',
        'List',
        'Global',
        'Declare'
    );
    private $reserveCheck = false;
    private $fileMapper = array();
    private $renameFileLogger = "renameFile.txt";
    private $renameClassLogger = "renameClass.txt";
    private $errorLog = "error.txt";
    private $fileChanged = array();
    private $rootDirectory = null;
    private $classSearch = array();
    private $classReplace = array();
    private $allowedFileExtensions = array('php', 'phtml', 'html');

    public function __construct($path, $rootDirectory, $def = false)
    {
        $this->path = $path;
        $this->rootDirectory = $rootDirectory;
        //@unlink($this->renameClassLogger);
        //@unlink($this->renameFileLogger);
        //unlink($this->errorLog);

    }

    public function logFile($logFile, $string)
    {
        file_put_contents($logFile, $string, FILE_APPEND);
        clearstatcache();
    }

    public function convertToPSRX()
    {
        clearstatcache();
        $files = $this->scanDirectory($this->path);
        if (count($files)) {
            $this->scanFile($files);
        } else {

            throw new exception("Files cannot be processed");
        }
    }

    protected function scanDirectory($path, $onlyPhp = true)
    {
        $files = array();
        clearstatcache();
        if (is_dir($path)) {
            $rdi = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
            foreach (new \RecursiveIteratorIterator($rdi) as $file) {
                /** @var $file \SplFileInfo */
                if ($onlyPhp) {
                    if ($file->getExtension() != 'php') {
                        continue;
                    }
                } else {
                    if (!in_array($file->getExtension(), $this->allowedFileExtensions)) {
                        continue;
                    }
                }

                $files[] = $file->getRealPath();
            }
        } elseif (is_file($path)) {
            $files[] = $path;
        }
        return $files;
    }

    private function compareInString($line, $start, $end, $compare)
    {
        return (substr($line, $start, $end) === $compare);
    }


    private function scanFile($fileArray)
    {
        foreach ($fileArray as $file) {
            $info = new SplFileInfo($file);
            if ($info->getExtension() != 'php') {
                echo "Only php files can be used for namespace formatting \n";
                continue;
            }
            clearstatcache();
            $lines = file($file);
            $parsedLine = null;
            $this->namespace = array();
            $count = 0;
            $braceStarted = false;
            $starBraceCheck = false;
            echo "$file psr1 process started \n";
            foreach ($lines as $line) {
                $trimLine = trim($line);
                if ($this->compareInString($trimLine, 0, 5, 'class') || $this->compareInString(
                        $trimLine,
                        0,
                        7,
                        'extends'
                    ) || $this->compareInString($trimLine, 0, 10, 'implements')
                ) {
                    if ($this->compareInString($line, 0, 5, 'class')) {
                        $this->splitLine = $count;
                        $starBraceCheck = true;
                    }
                    $parsedLine = $this->scanClass($line, $parsedLine, $file);
                } else {
                    if ($this->compareInString($line, 0, 14, 'abstract class')) {
                        $this->splitLine = $count;
                        $starBraceCheck = true;
                        $parsedLine = $this->scanClass($line, $parsedLine, $file);
                    } else {
                        if ($this->compareInString($line, 0, 9, "interface")) {
                            $starBraceCheck = true;
                            $this->splitLine = $count;
                            $parsedLine = $this->scanClass($line, $parsedLine, $file);
                        } else {
                            if ($this->compareInString($line, 0, 11, "final class")) {
                                $starBraceCheck = true;
                                $this->splitLine = $count;
                                $parsedLine = $this->scanClass($line, $parsedLine, $file);
                            } else {
                                if ($trimLine === '{') {
                                    $braceStarted = true;
                                    $parsedLine[] = $line;
                                } else {
                                    if ($braceStarted == false && $starBraceCheck == true) {
                                        $parsedLine = $this->scanClass($line, $parsedLine, $file, true);
                                    } else {
                                        $parsedLine[] = $line;
                                    }
                                }
                            }
                        }
                    }
                }
                $count++;

            }
            $this->makeFile($file, $parsedLine, $this->namespace);
            echo "$file PSR1 transformation completed \n";


        }
        $this->globalClassnameScanner();
    }

    private function globalClassnameScanner()
    {
        clearstatcache();
        if (is_dir($this->rootDirectory) && !empty($this->classSearch) & !empty($this->classReplace)) {
            $files = $this->scanDirectory($this->rootDirectory, false);
            $search = array();
            foreach ($this->classSearch as $searchKey) {
                $search[] = "/\\" . $searchKey . "\\b/";
            }
            if (count($search) === count($this->classReplace) && count($search) === count($this->classSearch)) {
                $this->classSearch = $search;
                foreach ($files as $file) {
                    clearstatcache();
                    //$contents=str_replace($this->classSearch,$this->classReplace,file_get_contents($file));
                    $contents = preg_replace($this->classSearch, $this->classReplace, file_get_contents($file));
                    file_put_contents($file, $contents);
                }
            } else {
                $string = "Cannot do a global scan and replacement , Error Please do check the rename class and rename files" . "\n";
                $this->logFile($this->errorLog, $string);
                echo "Check Error Log \n";
            }


        }

    }

    private function scanClass($line, $array, $file, $parseFlag = false)
    {
        $exp = explode(" ", $line);
        $parse = $parseFlag;
        $string = "";
        $this->reservecheck = false;
        $count = 0;
        foreach ($exp as $value) {
            clearstatcache();
            $val = trim(strtolower($value));
            if ($this->isComment($val)) {
                $tempArr = array_slice($exp, $count + 1, count($exp));
                $newString = implode(" ", $tempArr);
                $string = $string . $newString;
                break;
            }
            if (trim(
                    $val
                ) == '' || $val === 'abstract' || $val === 'class' || $val === 'final' || $val === 'interface' || $val === 'extends' || $val === 'implements'
            ) {
                $parse = true;
                if ($val === 'abstract' || $val === 'class' || $val === 'final' || $val === 'interface') {
                    $this->reserveCheck = true;
                } else {
                    $this->reserveCheck = false;
                }
                if (trim($val) == '') {
                    $string = $string . ' ';

                } else {
                    $string = $string . $value . " ";
                }
                continue;
            }

            if ($parse) {
                $vals = explode(",", $value);
                $multipleImplements = false;
                if (count($vals) > 1) {
                    $multipleImplements = true;
                }
                foreach ($vals as $val) {
                    //fix for the global scanner

                    $val = str_replace("\\", "_", $val);
                    $val = trim($val);
                    if ($this->reserveCheck) {
                        $namespace =
                            "namespace " . str_replace(
                                '_',
                                "\\",
                                substr($val, 0, strrpos($val, '_'))
                            ) . ';' . "\n" . "\n";
                        $namespaceCheck = trim(str_replace('namespace ;', '', $namespace));
                        if ($namespaceCheck) {
                            $this->namespace[] = $namespace;
                        } else {

                            $this->fileChanged[] = $file;
                        }

                        if (strpos($val, '_') !== false) {
                            $newClass = substr($val, strrpos($val, '_') + 1);
                        } else {
                            $newClass = $val;
                        }
                        if ((in_array(trim($newClass), $this->reservedKeyWords)) && $namespaceCheck) {
                            $newClass = $this->setMapReservedFiles(
                                trim($newClass),
                                $namespace,
                                $file,
                                $this->reserveCheck
                            );
                            $this->reserveCheck = false;

                        }
                        $change = "\\" . str_replace(
                                "_",
                                "\\",
                                trim(
                                    str_replace(
                                        "\\",
                                        "_",
                                        str_replace(
                                            ';',
                                            '',
                                            trim(str_replace('namespace', '', $namespace))
                                        ) . "\\" . trim($newClass)
                                    )
                                )
                            );
                        if ((trim($val) !== 'implements') || (trim($val) !== 'extends')) {
                            $val=str_replace('//','',trim($val));
                            $change=str_replace('//','',trim($change));
                            $mess = trim($val) . "  =>  " . $change . "\n";
                            $this->classSearch[] = trim($val);
                            $this->classReplace[] = $change;
                            $this->logFile($this->renameClassLogger, $mess);
                        }

                    } else {
                        if (strpos($val, '_') !== false) {
                            $tempClass = substr($val, strrpos($val, '_') + 1);
                        } else {
                            $tempClass = $val;
                        }

                        if (in_array(trim($tempClass), $this->reservedKeyWords)) {
                            $tempNameSpace = str_replace(
                                '_',
                                "\\",
                                substr($val, 0, strrpos($val, '_'))
                            );
                            $newClass = "\\" . $tempNameSpace . "\\" . trim(
                                    $this->setMapReservedFiles($tempClass, $tempNameSpace, $file, $this->reserveCheck)
                                );

                        } else {
                            $newClass = "\\" . str_replace('_', "\\", $val);
                        }
                    }
                    $newClass = str_replace("\\\\", "\\", $newClass);
                    if ($multipleImplements && trim($val) != trim($vals[count($vals) - 1])) {
                        $string = $string . $newClass . ",";
                    } else {
                        $string = $string . $newClass . " ";
                    }

                }
            }
            $count++;
        }
        if(substr($string, -1) == '\\') {
            $string = substr($string, 0, -1);
}
        $string = rtrim($string) . "\n";
        $array[] = $string;
        return $array;
    }


    private function setMapReservedFiles($newClass, $nameSpace, $file, $reserveCheck)
    {
        clearstatcache();
        $newClass = trim($newClass);
        $string = explode("\\", trim($nameSpace));
        if ($newClass === 'Exception' || $newClass === 'Trait' || $newClass === 'Interface') {
            $newClass = trim(str_replace(";", '', ($string[count($string) - 1]))) . $newClass;
        } else {
            $newClass = $newClass . trim(str_replace(";", '', ($string[count($string) - 1])));
        }
        if ($reserveCheck) {
            $newFileName = dirname($file) . "\\" . $newClass . '.php';
            $this->fileMapper[$file] = $newFileName;
        }
        return $newClass;
    }

    private function renameReservedFileNames($file)
    {
        if (isset($this->fileMapper[$file])) {
            clearstatcache();
            if (rename($file, $this->fileMapper[$file])) {
                $string = $file . " =>  " . $this->fileMapper[$file] . "\n";
                $this->logFile($this->renameFileLogger, $string);
            } else {
                $string = $file . " cannot be  renamed to " . $this->fileMapper[$file] . "\n";
                $this->logFile($this->errorLog, $string);
            }
        }

    }

    private function makeFile($file, $array, $namespace)
    {

        $first_Array = (array_slice($array, 0, $this->splitLine));
        $key_split = (array_slice($array, $this->splitLine));
        $array = array_merge($first_Array, $namespace, $key_split);

        $string = "";
        // new line feeder
        if (end($array) == "}") {
            $array[] = "\n";
        }
        foreach ($array as $key) {
            $string = $string . $key;
        }

        if (!in_array($file, $this->fileChanged)) {
            clearstatcache();
            file_put_contents($file, $string);
            $this->renameReservedFileNames($file);
        }


    }

    private function isComment($str)
    {
        $str = trim($str);
        $first_two_chars = substr($str, 0, 2);
        $last_two_chars = substr($str, -2);
        return $first_two_chars == '//' || substr(
            $str,
            0,
            1
        ) == '#' || ($first_two_chars == '/*' && $last_two_chars == '*/');
    }

}


//Processing CommandLine arguments
// php psrx.php (Dir/File)Location rootdirectory
// root directory

if (isset($argv[1])) {
    $rootDirectory = false;
    if (isset($argv[2])) {
        $rootDirectory = $argv[2];
        $update = explode("=", $argv[2]);
        if (isset($update[1])) {
            $rootDirectory = trim($update[1]);
        }
    }
    $src = explode("=", $argv[1]);
    if (isset($src[1])) {
        $src = trim($src[1]);

    } else {
        throw new exception("src paramter cannot be empty");
    }

    $PSRX = new psrx($src, $rootDirectory);
    $PSRX->convertToPSRX();
} else {
    echo "Please provide the arguments";
}
