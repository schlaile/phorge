<?php

$tables = array(
  new PhabricatorWorkerActiveTask(),
  new PhabricatorWorkerArchiveTask(),
);

foreach ($tables as $table) {
  $conn = $table->establishConnection('w');
  $table_name = $table->getTableName();

  $has_column = queryfx_one(
    $conn,
    'SHOW COLUMNS FROM %T LIKE %>',
    $table_name,
    'containerPHID');

  if ($has_column) {
    continue;
  }

  queryfx(
    $conn,
    'ALTER TABLE %T ADD containerPHID VARBINARY(64)',
    $table_name);
}
