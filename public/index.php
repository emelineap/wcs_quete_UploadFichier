<?php

require_once '../vendor/autoload.php';

use UploadFichier\Controllers\DefaultController;

$defaultController = new DefaultController();

if (empty($_GET)) {
    echo $defaultController->indexAction();
}

elseif ($_GET['section'] === 'form') {
    echo $defaultController->formAction();
}

elseif ($_GET['section'] === 'success') {
    echo $defaultController->successAction();
}

elseif ($_GET['section'] === 'unlink') {
    echo $defaultController->unlinkAction();
}