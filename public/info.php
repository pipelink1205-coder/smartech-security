<?php
echo extension_loaded('pdo_mysql') ? 'PDO MySQL: OK' : 'PDO MySQL: FALTA';
echo '<br>';
echo extension_loaded('pdo') ? 'PDO: OK' : 'PDO: FALTA';
echo '<br>';
echo phpversion();
?>