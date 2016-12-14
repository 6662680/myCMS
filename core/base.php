<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
date_default_timezone_set('Asia/Shanghai');
spl_autoload_register(array('UCMS', 'autoload'));

define('IN_UCMS', true);
define('UCMS_CORE', dirname(__FILE__) . DS);
define('UCMS_DATA', UCMS_ROOT . 'data' . DS);
define('UCMS_SKIN', UCMS_ROOT . 'template' . DS);
define('UCMS_DEBUG', UCMS::load_config('global', 'debug'));
define('UCMS_COOKIE_PRE', UCMS::load_config('global', 'cookie_pre'));

UCMS::load_func('global');
UCMS::creat_app();

class UCMS {

	public static function autoload($classname){
		self::load_class($classname);
	}

	public static function creat_app() {
		self::init_app();
		self::init_db();
	}

	public static function init_db() {
		$driver = function_exists('mysql_connect') ? 'db_driver_mysql' : 'db_driver_mysqli';
		DB::init($driver, self::load_config('db'));
	}

	public static function init_app(){
		define('ROUTE_C', self::route_c());
		define('ROUTE_A', self::route_a() . 'Action');
		echo 'Controller: ' . ROUTE_C ."<br>";
		echo 'Action: ' . ROUTE_A ."<br>";
		$controller = self::load_controller();
		if (method_exists($controller, ROUTE_A)) {
			if (preg_match('/^[_]/i', ROUTE_A)) {
				exit('You are visiting the action is to protect the private action.');
			} else {
				call_user_func(array($controller, ROUTE_A));
			}
		} else {
			exit('Action does not exist.');
		}
	}

	public static function load_file($filename) {
		static $files = array();
		if (!$filename) return false;
		 if (!isset($files[$filename])) {
			if (!file_exists($filename)) {
				exit($filename . ' not found!');
			}
			include_once $filename;
			$files[$filename] = true;
		}
		return $files[$filename];
	}

	public static function load_func($funcname) {
		static $funcs = array();
		$path = 'library' . DS . 'function' . DS . $funcname . '.func.php';
		$key = md5($path);
		if (isset($funcs[$key])) return true;
		if (file_exists(UCMS_CORE . $path)) {
			include UCMS_CORE . $path;
		} else {
			$funcs[$key] = false;
			return false;
		}
		$funcs[$key] = true;
		return true;
	}

	public static function load_class($classname) {
		static $class = array();
		$path = 'library' . DS . 'class' . DS . $classname . '.class.php';
		$key = md5($path);
		if (isset($class[$key])) {
			if (!empty($class[$key])) {
				return $class[$key];
			} else {
				return true;
			}
		}
		if (file_exists(UCMS_CORE . $path)) {
			include UCMS_CORE . $path;
			$class[$key] = new $classname;
			return $class[$key];
		} else {
			return false;
		}
	}

	public static function load_model($modelname) {
		static $model = array();
		$path = 'model' . DS . $modelname . '.model.php';
		$key = md5($path);
		if (isset($model[$key])) {
			if (!empty($model[$key])) {
				return $model[$key];
			} else {
				return true;
			}
		}
		if (file_exists(UCMS_CORE . $path)) {
			include UCMS_CORE . $path;
			$model[$key] = new $modelname;
			return $model[$key];
		} else {
			return false;
		}
	}

	public static function load_config($file, $key = '', $default = '') {
		static $configs = array();
		$path = UCMS_DATA . 'config' . DS . $file.'.ini.php';
		if (file_exists($path)) {
			$configs[$file] = include $path;
		}
		if (empty($key)) {
			return $configs[$file];
		} elseif (isset($configs[$file][$key])) {
			return $configs[$file][$key];
		} else {
			return $default;
		}
	}

	private function load_controller() {
		$path =  UCMS_CORE . 'controller' . DS . ROUTE_C.'.php';
		$classname = ROUTE_C;
		if (file_exists($path)) {
			include $path;
			if(class_exists($classname)){
				return new $classname;
			}else{
				exit('Controller does not exist.');
 			}
		} else {
			exit('Controller does not exist.');
		}
	}

	public function route_c() {
		$c = isset($_GET['c']) && !empty($_GET['c']) ? $_GET['c'] : (isset($_POST['c']) && !empty($_POST['c']) ? $_POST['c'] : '');
		$c = self::safe_deal($c);
		if (empty($c)) {
			return 'index';
		} else {
			if(is_string($c)) return $c;
		}
	}

	public function route_a() {
		$a = isset($_GET['a']) && !empty($_GET['a']) ? $_GET['a'] : (isset($_POST['a']) && !empty($_POST['a']) ? $_POST['a'] : '');
		$a = self::safe_deal($a);
		if (empty($a)) {
			return 'index';
		} else {
			if(is_string($a)) return $a;
		}
	}

	private function safe_deal($str) {
		return str_replace(array('/', '.'), '', $str);
	}

}
?>