<?php

namespace FlexibleORMDemoWebsite\Controllers;

use ORM\Utilities\Configuration;
use ORM\Controller\Request;
use ORM\Controller\SmartyTemplate;
use Exception;

require Configuration::PHPClassFiles('Smarty');

/**
 * This is the primary router class that instructs.
 *
 * @author Pierre Dumuid <pierre.dumuid@sustainabilityhouse.com.au>
 */
class Router {
    /**
     * The request variable.
     *
     * @var \ORM\Controller\Request
     */
    private $_request;

    /**
     * An array of controller names and the corresponding Controller Class used to process the action.
     *
     * @var type
     */
    static $controllers = array(
        'demo'    => 'FlexibleORMDemoWebsite\Controllers\DemoController',
        'demo2'   => 'FlexibleORMDemoWebsite\Controllers\Demo2Controller',
    );

    /**
     * Process request.
     *
     * @param \ORM\Controller\Request $request
     *     Optional (default to creating a request object from $_GET and $_POST).
     */
    public function run(Request $request = null) {
        // Construct the request object if it hasn't been passed as a parameter.
        $this->_request = is_null($request) ? new Request($_GET, $_POST) : $request;
        $this->loadSmarty();

        // Load Controller
        $controllerClassName = $this->resolveControllerClassName($this->_request->get->controller);
        if (!class_exists($controllerClassName)) {
            throw new Exception("Could not find the controller, `$controllerClassName`.");
        }
        $controller = new $controllerClassName($this->_request, $this->smarty);

        // Output the result
        try {
            echo $controller->performAction();
        } catch( Exception $e ) {
            echo '<p>An exception was encountered in '.$e->getFile().'('.$e->getLine().'): <br/>',
                '<div style="margin-left:10px">'.$e->getMessage().'</div>',
                 '</p><h4>Trace:</h4><pre>',
                 $e->getTraceAsString(),
                 '</pre>';
        }
    }

    /**
     * Load in the smarty template, and register some configuration settings.
     */
    public function loadSmarty() {
        // Setup Smarty
        $this->smarty = new SmartyTemplate();
        $this->smarty->template_dir = __DIR__.'/../views/';
        $this->smarty->compile_dir  = '/tmp/templates_c';
        $this->smarty->registered_classes['ORM_Utilities_Configuration'] = '\ORM\Utilities\Configuration';
    }

    /**
     * Determine the fully qualified class name.
     *
     * @param string $controllerName
     * @return string
     */
    public function resolveControllerClassName($controllerName) {
        return array_key_exists($controllerName, self::$controllers)
            ? self::$controllers[$controllerName]
            : __NAMESPACE__.'\DefaultController';
    }

    public function resolveControllerClassNameAlternative($controllerName) {
        if ($controllerName === '') {
            return __NAMESPACE__.'\DefaultController';
        }
        // Convert dash-seperated-urls to CapsFirstCamelCase.
        $className = preg_replace("/-([a-zA-Z0-9])/e", "strtoupper('\\1')", $controllerName);
        $className[0] = strtoupper($className[0]);
        return __NAMESPACE__.'\\'.$className."Controller";
    }

}
