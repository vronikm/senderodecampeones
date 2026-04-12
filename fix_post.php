<?php
$content = file_get_contents('c:/wamp64/www/soccereasy/app/controllers/alumnoController.php');

// Fix cemer fields - use simple str_replace on the actual strings
$searches = [
    "\$_POST['cemer_nombre'])",
    "\$_POST['cemer_celular'])",
    "\$_POST['cemer_parentesco'])",
    "\$_POST['horarioid'])"
];
$replaces = [
    "\$_POST['cemer_nombre'] ?? '')",
    "\$_POST['cemer_celular'] ?? '')",
    "\$_POST['cemer_parentesco'] ?? '')",
    "\$_POST['horarioid'] ?? '')"
];

$result = str_replace($searches, $replaces, $content, $counts);
echo 'Total replacements: ' . array_sum($counts) . PHP_EOL;
print_r($counts);
file_put_contents('c:/wamp64/www/soccereasy/app/controllers/alumnoController.php', $result);
echo 'Done' . PHP_EOL;
