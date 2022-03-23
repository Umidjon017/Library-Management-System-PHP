<?php
include "../database_connection.php";
include "../functions.php";
if (!is_admin_login())
{
    header('Location: ../admin_login.php');
}
include "../header.php";
?>