<?php
echo $this->Form->create(null);
echo $this->Form->control('user', [
    'type' => 'select',
    'empty' => 'Select a user',
    'options' => $users,
    'value' => $this->request->getSession()->read('User.id'),
]);
echo $this->Form->submit();
echo $this->Form->end();
