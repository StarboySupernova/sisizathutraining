<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mariadb';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'zathutrain_mo828';
$CFG->dbuser    = 'zathutrain_mo828';
$CFG->dbpass    = '4hS0.poF1(';
$CFG->prefix    = 'mdl5n_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_general_ci',
);

$CFG->wwwroot   = 'https://www.sisizathutraining.com';
$CFG->dataroot  = '/home/zathutrain/moodledata';
$CFG->dirroot   = __DIR__ . '/public';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/public/lib/setup.php');