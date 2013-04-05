<?php
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set('display_errors', '1');
	
    $config = array();
    
	$config['db']['dsn']                     = "mysql:host=kidzartrds.cfdeoujljpwn.us-east-1.rds.amazonaws.com;dbname=Register";
	$config['db']['host']                    = "kidzartrds.cfdeoujljpwn.us-east-1.rds.amazonaws.com";
	$config['db']['db']                      = "Register";
	$config['db']['un']                      = "register";
	$config['db']['pw']                      = "jyAZC24jYEZfDjsE";
	
	$config['admin']['name']                 = "Sue Bartman"; 
	$config['admin']['email']                = "sue@kidzart.com";
	$config['admin']['password']             = '$2a$10$stIapougoewluzOuylAQo.wR2zJTHaevNdjNfZIos.tVt0t8.JQfS';
	
	$config['email']['default_welcome']      = "509c6aa3ed353";
	$config['email']['default_registration'] = "509c77fd60b9d";
	$config['email']['default_change']       = "509ca7889e440";
	$config['email']['default_reminder']     = "509cc46b8eef8";
	
	$final_width_of_image                    = 100;
	$path_to_image_directory                 = 'images/fullsized/';  
	$path_to_thumbs_directory                = 'images/thumbs/';
	
	$config['rosterprefs'][]                 = "child[name]";
	$config['rosterprefs'][]                 = "child[birthdate]";
	$config['rosterprefs'][]                 = "parent[name]";
	$config['rosterprefs'][]                 = "parent[phone]";
	$config['rosterprefs'][]                 = "parent[email]";
	$config['rosterprefs'][]                 = "parent[emergency_contact]";
	$config['rosterprefs'][]                 = "notes";
?>