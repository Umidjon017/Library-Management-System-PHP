<?php

if (!function_exists("baseUrl"))
{
    function baseUrl()
    {
        return "http://localhost/php-self-projects/lms_draft/";
    }
}

if (!function_exists("is_admin_login"))
{
    function is_admin_login()
    {
        if (isset($_SESSION['admin_id']))
        {
            return true;
        }
        return false;
    }
}