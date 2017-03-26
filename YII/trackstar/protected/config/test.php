<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
			 // uncomment the following to provide test database connection
			'db'=>array(
                'connectionString' => 'mysql:host=127.0.0.1;dbname=trackstar_test',
                'emulatePrepare' => true,
                'username' => 'root',
                'password' => '123456',
                'charset' => 'utf8',
			),

		),
	)
);
