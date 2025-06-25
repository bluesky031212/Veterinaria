<?php
session_start();
session_destroy();
header("Location: /Veterinaria/index.php");
exit();