<?php decorate_with('layout_1col') ?>

<?php slot('title') ?>
  <h1><?php echo __('Modifications') ?></h1>

<?php end_slot() ?>

<?php slot('content') ?>

<h1 class="label"><?php echo render_title($resource) ?></h1>

<table class="table table-bordered sticky-enabled">
  <thead>
    <tr>
      <th>
        <?php echo __('Date') ?>
      </th>
      <th>
        <?php echo __('Type') ?>
      </th>
      <th>
        <?php echo __('User') ?>
      </th>
    </tr>
  </thead><tbody>
    <?php foreach ($modifications as $modification): ?>
      <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
        <td>
          <?php echo $modification->createdAt ?>
        </td>
        <td>
          <?php echo $userActions[$modification->actionTypeId]->name ?>
        </td>
        <td>
          <?php
            if ($modification->userId)
            {
              $user = QubitUser::getById($modification->userId);
              $userText = link_to($user, array($user, 'module' => 'user'));
            }
            else
            {
              $userText = $modification->userName;
            }
          ?>
          <?php echo $userText ?>
        </td>
      </tr>
    <?php endforeach; ?>
  <tbody>
</table>

<?php end_slot() ?>

<?php slot('after-content') ?>
  <?php echo get_partial('default/pager', array('pager' => $pager)) ?>
<?php end_slot() ?>
