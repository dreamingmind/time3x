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
    <?= $this->Html->script(['jquery-1.10.2', 'jquery-ui', 'kickstart', 'app', 'timekeep']); ?>

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
    </script>
    <?php
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
echo $this->Html->tag('h5', 'Reveal Session Data', array('id' => 'sqlD', 'class' => 'toggle'));
echo $this->Html->div('sqlD hide', NULL);
debug($this->request->getSession()->read());
echo '</div>';
?>
</body>
<footer id="footer"><img src="img/crane_black_transparent_400_400.png" alt="the origami crane" width="20px">  Copyright 2014 Origami Structures</footer>

</body>
</html>
