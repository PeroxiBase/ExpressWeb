<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';

$route['admin/'] = "admin/admin";
$route['admin/(:any)'] = "admin/admin/$1";
$route['admin/(:any)/(:any)'] = "admin/admin/$1/$2";

$route['create_table'] = "admin/create_table";
$route['create_table/(:any)/(:any)'] = "admin/create_table/$1/$2";
$route['create_table/(:any)'] = "admin/create_table/$1";

$route['toolbox'] = "admin/toolbox";
$route['toolbox/(:any)/(:any)'] = "admin/toolbox/$1/$2";
$route['toolbox/(:any)'] = "admin/toolbox/$1";

$route['dashboard'] = "admin/dashboard";
$route['dashboard/(:any)/(:any)'] = "admin/dashboard/$1/$2";
$route['dashboard/(:any)'] = "admin/dashboard/$1";
// http://yourdomain.com/admin/user/5 will map to 'user' function in 'home' controller with '5' as parameter 
 
$route['admin/(:any)'] = "admin/admin/$1";

/*$route['auth'] = "auth";*/

$route['auth/(:any)/(:any)/(:any)'] = "auth/$1/$2/$3";
$route['auth/(:any)/(:any)'] = "auth/$1/$2";
$route['auth/(:any)'] = "auth/$1";
$route['translate_uri_dashes'] = FALSE;
