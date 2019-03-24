<?php 
session_start();
error_reporting(-1);
ini_set('display_errors', 1);
define('BASEPATH',__DIR__ );
	
// Load the base twig class
require_once BASEPATH.'/core/lib/vendor/autoload.php';
require_once BASEPATH.'/config/config.php';
require_once BASEPATH.'/core/base.php';

// Load the base controller class
require_once BASEPATH.'/core/Controller.php';

/**
 * Reference to the CI_Controller method.
 *
 * Returns current CI instance object
 *
 * @return CI_Controller
 */
function &get_instance()
{
	return Controller::get_instance();
}

$url = core_file_loader('Uri');
//print_r($url);
//echo $url->segment(1);

$router = core_file_loader('Router',$url);
$e404 = FALSE;
$class = ucfirst($router->fetch_class());
$method = $router->fetch_method();



	if (empty($class) OR ! file_exists(BASEPATH.'/controller/'.$router->directory.$class.'.php')){
		$e404 = TRUE;
	}else{
		
		require_once(BASEPATH.'/controller/'.$router->directory.$class.'.php');
		
		if ( ! class_exists($class, FALSE) OR $method[0] === '_' OR method_exists('CI_Controller', $method))
		{
			$e404 = TRUE; 
		}
		elseif (method_exists($class, '_remap'))
		{ 
			$params = array($method, array_slice($url->rsegments, 2));
			$method = '_remap';
		}
		elseif ( ! method_exists($class, $method))
		{
			$e404 = TRUE;
		}
		
		
		if($e404){
			
			error_404('Page Not Found');
		}
		
		/**
		 * DO NOT CHANGE THIS, NOTHING ELSE WORKS!
		 *
		 * - method_exists() returns true for non-public methods, which passes the previous elseif
		 * - is_callable() returns false for PHP 4-style constructors, even if there's a __construct()
		 * - method_exists($class, '__construct') won't work because CI_Controller::__construct() is inherited
		 * - People will only complain if this doesn't work, even though it is documented that it shouldn't.
		 *
		 * ReflectionMethod::isConstructor() is the ONLY reliable check,
		 * knowing which method will be executed as a constructor.
		 */
		/* 
		elseif ( ! is_callable(array($class, $method)))
		{

			$reflection = new ReflectionMethod($class, $method);
			if ( ! $reflection->isPublic() OR $reflection->isConstructor())
			{
				$e404 = TRUE;
			}
		}*/
		
	}
	
	if ($method !== '_remap')
	{
		$params = array_slice($url->rsegments, 2);
	}	
	
	
///calling controller and function 	

//$params = array();
$PC = new $class();
call_user_func_array(array(&$PC, $method), $params);

?>