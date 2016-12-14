<?php
	define('DS', DIRECTORY_SEPARATOR);
	define('UCMS_ROOT', dirname(__FILE__) . DS);

	/*引入ORM自动加载*/
	include __DIR__ . '/vendor/autoload.php';

	require UCMS_ROOT . '/core/base.php';
	/*ORM命名空间*/
	use Illuminate\Container\Container;
	use Illuminate\Database\Capsule\Manager as DB;

	$database = [
		'driver'    => 'mysql',
		'host'      => 'localhost',
		'database'  => 'demo',
		'username'  => 'root',
		'password'  => '',
		'charset'   => 'utf8',
		'collation' => 'utf8_unicode_ci',
		'prefix'    => '',
	];

	$capsule = new DB;
	// 创建链接
	$capsule->addConnection($database);
	// 设置全局静态可访问
	$capsule->setAsGlobal();
	// 启动Eloquent
	$capsule->bootEloquent();
?>