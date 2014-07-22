<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
            'db'=>array(
                'connectionString' => 'mysql:host=localhost;dbname=trackstar_dev',
                //'connectionString' => 'mysql:host=localhost;dbname=trackstar_dev',
                'emulatePrepare' => true,
                'username' => 'root',
                'password' => '111',
                'charset' => 'utf8',
            ),
			/* uncomment the following to provide test database connection
			'db'=>array(
				'connectionString'=>'DSN for test database',
			),
			*/
		),
	)
);
