<?php

use Haijin\Debugger;
use Haijin\FilePath;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Html\Facade;

$specs->beforeAll(function () {
    (new FilePath("tests/tmp"))->delete();

    $this->coverage = initializeCoverageReport();
});

$specs->afterAll(function () {
    (new FilePath("tests/tmp"))->delete();

    generateCoverageReport($this->coverage);
});

function initializeCoverageReport()
{
    $coverage = new CodeCoverage;
    $coverage->filter()->addDirectoryToWhitelist('src/');
    $coverage->start('specsCoverage');

    return $coverage;
}

;

function generateCoverageReport($coverage)
{
    $coverage->stop();
    $writer = new Facade;
    $writer->process($coverage, 'coverage-report/');
}

;

function inspect($object)
{
    Debugger::inspect($object);
}
