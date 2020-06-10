<?php
	declare(strict_types=1);
   
    $dst = '';
    $patterns = [];
    $replacements = [];

    $filesToSkip = ['.git', '.gitignore', '.htaccess', 'private', 'repository', 'public'];

    function setProductionVars(){
        global $patterns;
        global $replacements;
        global $dst;

        $dst = __DIR__ . '/../backend-php-prod';

        $patterns[0] = '%// (.*) ///\$ %';
        $patterns[1] = '%.*//.*$(?:\r\n|\n)?%';
        $patterns[2] = '%^[^.]\n%';

        $replacements[0] = '$1';
        $replacements[1] = '';
        $replacements[2] = '';
    }

    function setDocsVars(){
        global $patterns;
        global $replacements;
        global $dst;
        global $filesToSkip;

        array_push($filesToSkip, 'test-v1.php', '1-tests.php');

        $dst = __DIR__ . '/../backend-php-docs';

        $patterns[] = '%.*///\? .*$(?:\r\n|\n)?%';
        $patterns[] .= '%.*///! .*$(?:\r\n|\n)?%';
        $patterns[] .= '%.*///!# .*$(?:\r\n|\n)?%';
        $patterns[] .= '%.*///\$ .*$(?:\r\n|\n)?%';
        
        $patterns[] .= '%///\* (.*)%';
        $patterns[] .= '%///\?\* (.*)%';

        $patterns[] .= "%// (.*')(.*)('.*) ///\% %";
        $patterns[] .= "%// (.*) ///\%\* %";
        
        $patterns[] .= '%^[^.]\n%';
        
        $replacements[] = '';
        $replacements[] .= '';
        $replacements[] .= '';
        $replacements[] .= '';

        $replacements[] .= '// $1';
        $replacements[] .= '// TODO: $1';
        $replacements[] .= '$1VALUE-DELETED$3 // e.g. value: $2';
        $replacements[] .= '$1';

        $replacements[] .= '';
    }

    function setDemoVars(){
        global $patterns;
        global $replacements;
        global $dst;
        global $filesToSkip;

        array_push($filesToSkip, 'test-v1.php', '1-tests.php');

        $dst = __DIR__ . '/../backend-php-demo';

        $patterns[] = '%.*///\? .*$(?:\r\n|\n)?%';
        $patterns[] .= '%.*///! .*$(?:\r\n|\n)?%';
        $patterns[] .= '%.*///!# .*$(?:\r\n|\n)?%';
        $patterns[] .= '%.*///\$ .*$(?:\r\n|\n)?%';
        
        $patterns[] .= '%///\* .*%';
        $patterns[] .= '%///\?\* .*%';

        $patterns[] .= '%// (.*) ///\% %';
        
        $patterns[] .= '%^[^.]\n%';
        
        $replacements[] = '';
        $replacements[] .= '';
        $replacements[] .= '';
        $replacements[] .= '';

        $replacements[] .= '';
        $replacements[] .= '';
        $replacements[] .= '$1';

        $replacements[] .= '';
    }

    function recurse_copy($src, $dst) {
        global $filesToSkip;
        global $patterns;
        global $replacements;
        
        $dir = opendir($src);
        if (!file_exists($dst)) {
            mkdir($dst, 0700);
        }
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' ) && ( !in_array($file, $filesToSkip))) { 
                if ( is_dir($src . '/' . $file) ) { 
                    recurse_copy($src . '/' . $file,$dst . '/' . $file); 
                }
                else {
                    echo '<br>TO: ' . $dst . '/' . $file;
                    $data = preg_replace($patterns, $replacements, file($src . '/' . $file));

                    file_put_contents($dst . '/' . $file, implode('', $data));
                } 
            } 
        } 
        closedir($dir); 
    }

    $src = __DIR__ . '/../../backend-php-dev';
    
    if (isset($_GET['prod'])) {
        setProductionVars();
    } elseif (isset($_GET['docs'])) {
        setDocsVars();
    } elseif (isset($_GET['demo'])) {
        setDemoVars();
    } else {
        echo 'Error! No environment set!';
        exit;
    }

    echo '<br>Files Packing: START';
    recurse_copy($src, $dst);
    echo '<br>Files Packing: END';

    exit;