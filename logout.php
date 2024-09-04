<?php
session_start();
session_destroy();
header('Location: royal.html');
exit;
?>
