<?php
/**
 * The main router file that handles all URL requests that have been
 * re-written as directed by the .htaccess file.
 *
 * @file
 * @author Pierre Dumuid <pierre.dumuid@sustainabilityhouse.com.au>
 *
 */

// @note Normally namespaces are related to the directory path, however
// "Public" is a PHP reserved name, and thus cannot be used.

// The autoloader will not be able to automatically resolve this path!
namespace FlexibleORMDemoWebsite\PublicContent;
use FlexibleORMDemoWebsite\Controllers\Router;
use Exception;

try {
    require_once __DIR__.'/../'."Autoload.php";
    $router = new Router();
    $router->run();
} catch (Exception $e) {
    echo '<p>An exception was encountered in '.$e->getFile().'('.$e->getLine().'): <br/>',
        '<div style="margin-left:10px">'.$e->getMessage().'</div>',
         '</p><h4>Trace:</h4><pre>',
         $e->getTraceAsString(),
         '</pre>';
}
