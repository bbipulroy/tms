<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['system_site_short_name']='tms';
$config['offline_controllers']=array('home','sys_site_offline');
$config['external_controllers']=array('home');//user can use them without login
$config['system_max_actions']=7;

$config['system_status_active']='Active';
$config['system_status_inactive']='In-Active';
$config['system_status_delete']='Deleted';
$config['system_image_base_url']='http://localhost/tms_2017_07/';

$config['USER_TYPE_EMPLOYEE']=1;
