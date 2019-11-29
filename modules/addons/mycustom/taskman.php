<?php

$dpimport = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'root',
		'password' => '123456',
		'database' => 'dayxam',
		'prefix' => '',
		//'encoding' => 'utf8',
	);
//users:
$name='users';
$tables[$name]['mapname']='cockpit/accounts';
$tables[$name]['pk']='id';
$tables[$name]['mapping']=[];
$tables[$name]['mapping']['username']='user';
$tables[$name]['mapping']['email']='email';
$tables[$name]['mapping']['name']='name';
$tables[$name]['mapping']['published']='active';
//status:
$name='statuses';
$tables[$name]['mapname']='addons/status';
$tables[$name]['pk']='id';
$tables[$name]['mapping']=[];
$tables[$name]['mapping']['name']='name';
$tables[$name]['mapping']['default']='default';

//projects:
$name='projects';
$tables[$name]['mapname']='addons/projects';
$tables[$name]['pk']='id';
$tables[$name]['fk']=['users'=>['pk'=>'id','table'=>'users','mapname'=>'userjoin','isarray'=>1],
					'manager'=>['pk'=>'id','table'=>'users','mapname'=>'leader'],
					'status_id'=>['pk'=>'id','table'=>'statuses','mapname'=>'status'],
					'user_id'=>['pk'=>'id','table'=>'users','mapname'=>'uid']];
$tables[$name]['mapping']=[];
$tables[$name]['mapping']['name']='name';
$tables[$name]['mapping']['code']='code';
$tables[$name]['mapping']['description']='description';

//tasks:
$name='tasks';
$tables[$name]['mapname']='addons/tasks';
$tables[$name]['pk']='id';
$tables[$name]['fk']=['assignees'=>['pk'=>'id','table'=>'users','mapname'=>'assignto','isarray'=>1],
					'project_id'=>['pk'=>'id','table'=>'projects','mapname'=>'pid'],
					'status_id'=>['pk'=>'id','table'=>'statuses','mapname'=>'status'],
					'user_id'=>['pk'=>'id','table'=>'users','mapname'=>'uid']];
$tables[$name]['mapping']=[];
$tables[$name]['mapping']['name']='name';
$tables[$name]['mapping']['code']='code';
$tables[$name]['mapping']['description']='description';

$tables[$name]['mapping']['taskcode']='codename';
//comments:
$name='comments';
$tables[$name]['mapname']='addons/comments';
$tables[$name]['pk']='id';
$tables[$name]['fk']=['task_id'=>['pk'=>'id','table'=>'tasks','mapname'=>'tid'],					
					'user_id'=>['pk'=>'id','table'=>'users','mapname'=>'uid']];
$tables[$name]['mapping']=[];
$tables[$name]['mapping']['comment']='message';

//hosts:
$name='hosts';
$tables[$name]['mapname']='addons/hosts';
$tables[$name]['pk']='id';
$tables[$name]['fk']=['task_id'=>['pk'=>'id','table'=>'tasks','mapname'=>'tid'],					
					'user_id'=>['pk'=>'id','table'=>'users','mapname'=>'uid'],
					'project_id'=>['pk'=>'id','table'=>'projects','mapname'=>'pid'],
					'users'=>['pk'=>'id','table'=>'users','mapname'=>'userpush','isarray'=>1]];
$tables[$name]['mapping']=[];
$tables[$name]['mapping']['name']='name';
$tables[$name]['mapping']['hostname']='hostname';
$tables[$name]['mapping']['username']='username';
$tables[$name]['mapping']['password']='password';
$tables[$name]['mapping']['mapfrom']='mapfrom';
$tables[$name]['mapping']['mapto']='mapto';

//sourcesafe:
$name='source_controls';
$tables[$name]['mapname']='addons/sourcesafes';
$tables[$name]['pk']='id';
$tables[$name]['fk']=[				
					'user_id'=>['pk'=>'id','table'=>'users','mapname'=>'uid'],
					'project_id'=>['pk'=>'id','table'=>'projects','mapname'=>'pid']
					];
$tables[$name]['mapping']=[];
$tables[$name]['mapping']['name']='name';
$tables[$name]['mapping']['url']='url';
$tables[$name]['mapping']['username']='username';
$tables[$name]['mapping']['password']='password';
$tables[$name]['mapping']['active']='default';

//deploys:
$name='deploys';
$tables[$name]['mapname']='addons/hostverions';
$tables[$name]['pk']='id';
$tables[$name]['fk']=[				
					'host_id'=>['pk'=>'id','table'=>'users','mapname'=>'hostid'],
					'project_id'=>['pk'=>'id','table'=>'projects','mapname'=>'pid']
					];
$tables[$name]['mapping']=[];
$tables[$name]['mapping']['version']='version';
$tables[$name]['mapping']['file']='file';



