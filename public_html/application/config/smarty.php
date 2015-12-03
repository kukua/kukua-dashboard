<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Smartytpl library. Extends default Smarty class
 * for easy integration into CodeIgniter.
 *
 * @author	Eric 'Aken' Roberts <eric@cryode.com>
 * @author	JohnHeroHD <johnherohd@gmail.com>
 * @link	https://github.com/JohnHeroHD/Codeigniter-Smarty
 * @version	1.0.1
 */

/*
|--------------------------------------------------------------------------
| Template Directory
|--------------------------------------------------------------------------
|
| Set the directory where your templates (view files) are located
| WITH TRAILING SLASH. Most users will not need to change this
| directory. It is pre-set to CodeIgniter's default views directory.
|
| Default: APPPATH . 'views/';
|
*/
$config['smarty_template_dir'] = APPPATH . 'views/';

/*
|--------------------------------------------------------------------------
| File Extension
|--------------------------------------------------------------------------
|
| Set the file extension of the templates (views) you're
| using with the Smarty template library.
|
| Common examples: php, tpl, phtml
| Default: tpl
|
*/
$config['smarty_template_ext'] = 'tpl';

/*
|--------------------------------------------------------------------------
| Compiled Directory
|--------------------------------------------------------------------------
|
| Set the location of the compiled folder for Smarty files
| WITH TRAILING SLASH. This directory is required! Also,
| make sure it has write permissions set.
|
| Default: APPPATH . 'cache/smarty_compiled/';
|
*/
$config['smarty_compile_dir'] = APPPATH . 'cache/smarty_compiled/';