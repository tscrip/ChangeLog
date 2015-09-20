<?php
require_once('includes/config.php');
require_once(PHP_VENDOR.'autoload.php');
require_once(PHP_INCLUDES.'core.php');

\Slim\Slim::registerAutoloader();

/*
* Initializing Slim to Use Twig
*/
$app = new \Slim\Slim(array(
	'templates.path' => HTML_TEMPLATES,
	'view' => new \Slim\Views\Twig()
));

$app->add(new \Slim\Middleware\SessionCookie(array('secret' => 'Il0v3l@mp@ndIl1k3turt135')));

$authenticate = function ($app) {
    return function () use ($app) {
        if (!isset($_SESSION['Authenticated_User'])) {
            $app->flash('error', 'Login required');
            $app->redirect('login');
        }
    };
};



$app->map('/login', function () use ($app) {
	
	if ( $app->request()->isGet() ){
		$app->render('sign_in.html', array(
    		'title' => PAGE_TITLE,
    	));
	}
	elseif ( $app->request()->isPost() ){
		$username = json_encode($app->request()->post('uname'));
		$password = json_encode($app->request()->post('pass'));
		
		$core = new Core();
		$results = $core->CheckLogin($username,$password);
		
		if ($results['success'] == 1){
		    $app->redirect('./');
		}
		elseif ($results['success'] == 0){
			$app->flash('error', 'Access Denied');
            $app->redirect('login');
		}

	}
})->via('GET','POST');

$app->get('/', $authenticate($app), function() use ($app) {
    //Getting systems from DB
    $core = new Core();
    $results = $core->GetSystemNameArr();

    $app->render('home.html', array(
    	'title' => PAGE_TITLE,
    	'navbar_title' => PAGE_TITLE,
    	'systemsArray' => $results,
    	));
});

$app->map('/logout', $authenticate($app), function () use ($app) {

	if ($app->request()->isGet() ){
		$core = new Core();
		$results = $core->LogOut();
    	if ($results['success'] == 1 && $results['result'] == "User Logged Out"){
    		print("Logged Out");
    		$app->redirect('login');
    	}
	}
})->via('GET');

$app->map('/api/recent', $authenticate($app), function () use ($app) {

	if ($app->request()->isGet() ){
		$core = new Core();
		$results = $core->GetRecent();
    	print $results;
	}
})->via('GET');

$app->map('/api/owners', $authenticate($app), function () use ($app) {

	if ($app->request()->isGet() ){
		$core = new Core();
		$results = $core->GetChangers();
    	print $results;
	}
})->via('GET');

$app->map('/api/environments', $authenticate($app), function () use ($app) {

	if ($app->request()->isGet() ){
		$core = new Core();
		$results = $core->GetEnvironments();
    	print $results;
	}
})->via('GET');

$app->map('/api/systems', $authenticate($app), function () use ($app) {

	if ($app->request()->isGet() ){
		$core = new Core();
		$results = $core->GetSystems();
    	print $results;
	}
})->via('GET');

$app->map('/api/durations', $authenticate($app), function () use ($app) {

	if ($app->request()->isGet() ){
		$core = new Core();
		$results = $core->GetDurations();
    	print $results;
	}
})->via('GET');

$app->map('/api/change/:change_id', $authenticate($app), function ($change_id) use ($app) {

	if ($app->request()->isGet() ){
		$core = new Core();
		$results = $core->GetChangeByID($change_id);
    	print $results;
	}
})->via('GET');

//System APIs

$app->map('/api/:system', $authenticate($app), function ($system) use ($app) {

	if ($app->request()->isGet() ){
		$core = new Core();
		$results = $core->GetChangeBySystem($system);
    	print $results;
	}
})->via('GET');
//End System APIs

$app->map('/api/search/:criteria', $authenticate($app), function ($criteria) use ($app) {

	if ($app->request()->isGet() ){
		$core = new Core();
		$results = $core->GetChangeBySearch($criteria);
    	print $results;
	}
})->via('GET');

$app->map('/api/calendar', $authenticate($app), function () use ($app) {
	$datetime_from = $app->request->get('from');
	$datetime_to = $app->request->get('to');

	if ($app->request()->isGet() ){
		$core = new Core();
		$results = $core->GetCalendarChanges($datetime_from,$datetime_to);
    	print $results;
	}
})->via('GET');

$app->map('/api/change', $authenticate($app), function () use ($app) {

	if ($app->request()->isPost() ){
    	$start = $app->request()->params('start');
    	$duration = $app->request()->params('duration');
    	$summary = $app->request()->params('summary');
    	$change = $app->request()->params('change');
    	$owner = $app->request()->params('owner');
    	$environment = $app->request()->params('environment');
    	$system = $app->request()->params('system');
    	
    	$core = new Core();
		$results = $core->CreateChange($start,$duration,$summary,$change,$owner,$environment,$system);
    	print $results;
	}
})->via('POST');

$app->run();

?>