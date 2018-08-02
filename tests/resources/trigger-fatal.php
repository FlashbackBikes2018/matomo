<?php

define('PIWIK_PRINT_ERROR_BACKTRACE', true);
define('PIWIK_ENABLE_DISPATCH', false);

require_once __DIR__ . '/../../index.php';

$environment = new \Piwik\Application\Environment(null);
$environment->init();

\Piwik\Access::getInstance()->setSuperUserAccess(true);

class MyClass
{
    public function triggerError()
    {
        try {
            \Piwik\ErrorHandler::pushFatalErrorBreadcrumb(static::class);

            $val = str_repeat(" ", 1024 * 1024 * 1024 * 1024 * 1024);
            print "here?\n";@ob_flush();
        } finally {
            \Piwik\ErrorHandler::popFatalErrorBreadcrumb();
        }
    }

    public static function staticMethod()
    {
        try {
            \Piwik\ErrorHandler::pushFatalErrorBreadcrumb(static::class);

            $instance = new MyClass();
            $instance->triggerError();
        } finally {
            \Piwik\ErrorHandler::popFatalErrorBreadcrumb();
        }
    }
}

class MyDerivedClass extends MyClass
{
}

function myFunction()
{
    try {
        \Piwik\ErrorHandler::pushFatalErrorBreadcrumb();

        MyDerivedClass::staticMethod();
    } finally {
        \Piwik\ErrorHandler::popFatalErrorBreadcrumb();
    }
}

myFunction();
