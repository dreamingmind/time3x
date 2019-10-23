<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = 'CakePHP: the rapid development php framework';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?=  $this->Html->meta('icon') ?>
    <?=  $this->Html->css('kickstart') ?>
    <?=  $this->Html->css('kickstart-grid') ?>

    <?= $this->Html->css('time'); ?>
    <?= $this->Html->script(['jquery-1.10.2', 'jquery-ui', 'app', 'timekeep']); ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>

    <script type="text/javascript">
        <?php
        $json = [
            'webroot' => $this->request->getAttribute('webroot'),
            'action' => $this->request->getParam('action') . '/',
            'controller' => $this->request->getParam('controller') . '/'
        ];
        echo 'OSTime = ' . json_encode($json);
        ?>
    </script>;
    <?php
//    echo "<script type=\"text/javascript\">
//        //<![CDATA[
//        // global data for javascript\n";
//    echo "var webroot = '{$this->request->webroot}';\n";
//    echo "var action = '{$this->request->params['action']}/';\n";
//    echo "var controller = '{$this->request->params['controller']}/';\n";
//    echo"\r//]]>
//        </script>";

    ?>
</head>
<body>
<div id="container">
    <div id="header">
        <!--<h6>Project Time Keeping</h6>-->
        <?php echo $this->element('menu') ?>
    </div>
    <div id="content">

        <?php echo $this->Flash->render(); ?>

        <?php echo $this->fetch('content'); ?>
    </div>
    <div id="footer">
    </div>
</div>
<?php
echo $this->Html->tag('h5', 'SQL Dump', array('id' => 'sqlD', 'class' => 'toggle'));
echo $this->Html->div('sqlD hide', NULL);
echo $this->element('sql_dump');
echo '</div>';
?>
</body>
<footer><img src="img/crane_black_transparent_400_400.png" alt="the origami crane" width="20px">  Copyright 2014 Origami Structures</footer>

<!--    <nav class="top-bar expanded" data-topbar role="navigation">-->
<!--        <ul class="title-area large-3 medium-4 columns">-->
<!--            <li class="name">-->
<!--                <h1><a href="">--><?//= $this->fetch('title') ?><!--</a></h1>-->
<!--            </li>-->
<!--        </ul>-->
<!--        <div class="top-bar-section">-->
<!--            <ul class="right">-->
<!--                <li><a target="_blank" href="https://book.cakephp.org/3.0/">Documentation</a></li>-->
<!--                <li><a target="_blank" href="https://api.cakephp.org/3.0/">API</a></li>-->
<!--            </ul>-->
<!--        </div>-->
<!--    </nav>-->
<!--    --><?//= $this->Flash->render() ?>
<!--    <div class="container clearfix">-->
<!--        --><?//= $this->fetch('content') ?>
<!--    </div>-->
<!--    <footer>-->
<!--    </footer>-->
</body>
</html>
