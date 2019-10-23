
<!-- View/Emements/menu.ctp -->
<?php
$menu = array(
    $this->Html->link('Time Keeper', array('controller' => 'times', 'action' => 'track')) => array(
        $this->Html->link('Clients', array('controller' => 'clients', 'action' => 'index')) => array(
            $this->Html->link(__('New Client'), array('action' => 'add')),
            ),
        $this->Html->link('Projects', array('controller' => 'projects', 'action' => 'index')) => array(
            $this->Html->link(__('New Project'), array('controller' => 'projects', 'action' => 'add'))
        ),
        $this->Html->link('Tasks', array('controller' => 'tasks', 'action' => 'index')) => array(
            $this->Html->link(__('New Task'), array('controller' => 'tasks', 'action' => 'add'))
        ),
        $this->Html->link('Users', array('controller' => 'users', 'action' => 'index')) => array(
            $this->Html->link(__('New User'), array('action' => 'add')),
            $this->Html->link(__('List Groups'), array('controller' => 'groups', 'action' => 'index')),
            $this->Html->link(__('New Group'), array('controller' => 'groups', 'action' => 'add'))
        ),
        $this->Html->link('Times', array('controller' => 'times', 'action' => 'index')) => array(
            $this->Html->link(__('New Time'), array('controller' => 'times', 'action' => 'add'))
        ),
        $this->Html->link('Track Time', array('controller' => 'times', 'action' => 'track')),
        $this->Html->link('Logout', array('controller' => 'users', 'action' => 'logout'))
        )
    );
echo $this->Html->div('menuDiv', NULL);
echo $this->Html->nestedList($menu, array('class' => 'menu vertical'));
echo '</div>';
?>

<!-- END View/Emements/menu.ctp -->

