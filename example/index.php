<?php

use Compolomus\Template\Template;

require __DIR__ . '/../vendor/autoload.php';

// Pseudo Dependency Injection

$config = [
    'template' => [
        'templateDirectory' => 'tpl', // realpath('./tpl')
        'templateExtension' => 'tpl',
        'templateFunctions' => [
            'upper' => static function (string $string) {
                return strtoupper($string);
            },
            'escape' => static function (string $string) {
                return htmlentities($string, ENT_QUOTES, 'UTF-8');
            }
        ]
    ],
];

function getTemplate(array $config)
{
    return new Template(
        $config['template']['templateDirectory'],
        $config['template']['templateExtension'],
        $config['template']['templateFunctions']
    );
}

//

$tpl = getTemplate($config);

$tpl->setTest2('tester');
$tpl->setTest3('<script>alert("Hellow world")</script>');
$tpl->render('test', 'test');
$tpl->render('index', 'content');
echo $tpl->render('layout');
