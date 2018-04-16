<?php decorate_with('layout_1col') ?>

<?php slot('title') ?>
  <h1><?php echo __('Editing History') ?></h1>

<?php end_slot() ?>

<?php slot('content') ?>

<h1 class="label"><?php echo render_title($resource->username) ?></h1>

<table class="table table-bordered sticky-enabled">
  <thead>
    <tr>
      <th>
        <?php echo __('Title') ?>
      </th>
      <th>
        <?php echo __('Date') ?>
      </th>
      <th>
        <?php echo __('Type') ?>
      </th>
    </tr>
  </thead><tbody>
    <?php foreach ($modifications as $modification): ?>
      <tr class="<?php echo 0 == @++$row % 2 ? 'even' : 'odd' ?>">
        <td>
          <?php $io = QubitInformationObject::getById($modification->objectId); ?>
          <?php echo $io->slug ?>
        </td>
        <td>
          <?php echo $modification->createdAt ?>
        </td>
        <td>
          <?php echo $userActions[$modification->actionTypeId]->name ?>
        </td>
      </tr>
    <?php endforeach; ?>
  <tbody>
</table>

<?php end_slot() ?>

<?php slot('after-content') ?>
  <?php echo get_partial('default/pager', array('pager' => $pager)) ?>
<?php end_slot() ?>
