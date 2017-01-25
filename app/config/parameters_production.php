<?php
    $db = parse_url('mysql://b52d1a652c093a:558dc316@eu-cdbr-west-01.cleardb.com/heroku_c00a0f39ccee3b4?reconnect=true');

    $container->setParameter('database_driver', 'pdo_mysql');
    $container->setParameter('database_host', 'eu-cdbr-west-01.cleardb.com');
    $container->setParameter('database_port', 3306);
    $container->setParameter('database_name', 'heroku_c00a0f39ccee3b4');
    $container->setParameter('database_user', 'b52d1a652c093a');
    $container->setParameter('database_password', '558dc316');
    $container->setParameter('secret', 'your_super_token');
    $container->setParameter('locale', 'en');
    $container->setParameter('mailer_transport', null);
    $container->setParameter('mailer_host', null);
    $container->setParameter('mailer_user', null);
    $container->setParameter('mailer_password', null);
