<?php

namespace Clockwork\Gulp\Test;

use Clockwork\Gulp\Exception\MultiplePackageAliasesException;
use Clockwork\Gulp\Exception\PackageAndPackagePropertyImportedException;
use Clockwork\Gulp\GulpFileContentGenerator;
use Clockwork\Gulp\PipelineInterface;
use Exception;
use PHPUnit\Framework\TestCase;

class GulpFileContentGeneratorTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetFileContents()
    {
        $pipeline1 = $this->createMock(PipelineInterface::class);
        $pipeline1->method('getName')->willReturn('foo');
        $pipeline1->method('includeInDefault')->willReturn(true);
        $pipeline1->method('getRequiredCommands')->willReturn(['gulp:src', 'gulp:dest', 'gulp-foo|foo']);
        $pipeline1->method('getFunctionBody')->willReturn("return src('input').pipe(foo()).pipe(dest('output'));");

        $pipeline2 = $this->createMock(PipelineInterface::class);
        $pipeline2->method('getName')->willReturn('bar');
        $pipeline2->method('includeInDefault')->willReturn(true);
        $pipeline2->method('getRequiredCommands')->willReturn(['gulp:src', 'gulp:dest', 'bar', 'baz:meh']);
        $pipeline2->method('getFunctionBody')->willReturn("return src('input').pipe(bar()).pipe(meh()).pipe(dest('output'));");

        $generator = new GulpFileContentGenerator();
        $generator->addPipeline($pipeline1);
        $generator->addPipeline($pipeline2);

        $this->assertEquals(<<<STRING
const bar = require('bar'),
    { meh } = require('baz'),
    { dest, parallel, src } = require('gulp'),
    foo = require('gulp-foo');
STRING
            , $generator->renderImports());

        $this->assertEquals(<<<STRING
function foo() {
    return src('input').pipe(foo()).pipe(dest('output'));
}

function bar() {
    return src('input').pipe(bar()).pipe(meh()).pipe(dest('output'));
}
STRING
            , $generator->renderFunctions());

        $this->assertEquals(<<<STRING
exports.foo = foo;
exports.bar = bar;
exports.default = parallel(foo, bar);
STRING
            , $generator->renderExports());

        $this->assertEquals(<<<STRING
const bar = require('bar'),
    { meh } = require('baz'),
    { dest, parallel, src } = require('gulp'),
    foo = require('gulp-foo');

function foo() {
    return src('input').pipe(foo()).pipe(dest('output'));
}

function bar() {
    return src('input').pipe(bar()).pipe(meh()).pipe(dest('output'));
}

exports.foo = foo;
exports.bar = bar;
exports.default = parallel(foo, bar);

STRING
            , $generator->getFileContents());
    }

    public function testConflictingPackageAndPropertyImported()
    {
        $pipeline1 = $this->createMock(PipelineInterface::class);
        $pipeline1->method('getName')->willReturn('foo');
        $pipeline1->method('getRequiredCommands')->willReturn(['foo']);

        $pipeline2 = $this->createMock(PipelineInterface::class);
        $pipeline2->method('getName')->willReturn('bar');
        $pipeline2->method('getRequiredCommands')->willReturn(['foo:bar']);

        $generator = new GulpFileContentGenerator();
        $generator->addPipeline($pipeline1);
        $generator->addPipeline($pipeline2);

        try {
            $generator->renderImports();
            $this->fail('Expected Exception');
        } catch (Exception $e) {
            $this->assertInstanceOf(PackageAndPackagePropertyImportedException::class, $e);
        }

        $generator = new GulpFileContentGenerator();
        $generator->addPipeline($pipeline2);
        $generator->addPipeline($pipeline1);

        try {
            $generator->renderImports();
            $this->fail('Expected Exception');
        } catch (Exception $e) {
            $this->assertInstanceOf(PackageAndPackagePropertyImportedException::class, $e);
        }
    }

    public function testMultipleAliasesImported()
    {
        $pipeline1 = $this->createMock(PipelineInterface::class);
        $pipeline1->method('getName')->willReturn('foo');
        $pipeline1->method('getRequiredCommands')->willReturn(['gulp-foo|foo']);

        $pipeline2 = $this->createMock(PipelineInterface::class);
        $pipeline2->method('getName')->willReturn('foo2');
        $pipeline2->method('getRequiredCommands')->willReturn(['gulp-foo|foo2']);

        $generator = new GulpFileContentGenerator();
        $generator->addPipeline($pipeline1);
        $generator->addPipeline($pipeline2);

        try {
            $generator->renderImports();
            $this->fail('Expected Exception');
        } catch(Exception $e) {
            $this->assertInstanceOf(MultiplePackageAliasesException::class, $e);
        }
    }
}
