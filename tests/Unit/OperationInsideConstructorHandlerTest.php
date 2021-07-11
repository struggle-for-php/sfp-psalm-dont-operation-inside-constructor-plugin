<?php

declare(strict_types=1);

namespace SfpTest\Psalm\DontOperationInsideConstructor\Unit;

use Psalm\Context;
use Psalm\IssueBuffer;

use function trim;

final class OperationInsideConstructorHandlerTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function operationMethodCall(): void
    {
        $this->addFile(
            __METHOD__,
            <<<'CODE'
<?php
namespace  SfpTest\Psalm\DontOperationInsideConstructor\Unit;

use PDO;
use DateTime as AliasDateTime;

class QueryInvoker
{
    /** @var PDO */
    private $db;

    public function __construct(PDO $db, string $query, \DateTimeImmutable $date)
    {
        $this->db = $db;
        $db->getAttribute(\PDO::ATTR_ERRMODE); // should be allow.
        $this->db->query($query);
                
        AliasDateTime::createFromImmutable($date);
        
        (new \SplFileInfo(__FILE__))->openFile();
    }
    
    public function __invoke(PDO $db, string $query) : void
    {
        $db->query($query);
    }
}
CODE
        );
        $this->analyzeFile(__METHOD__, new Context());

        $issues = IssueBuffer::getIssuesData()[__METHOD__];
        $this->assertCount(2, $issues);

        $this->assertSame('MethodOperationInsideConstructorIssue', $issues[0]->type);
        $this->assertSame('$this->db->query($query);', trim($issues[0]->snippet));

        $this->assertSame('MethodOperationInsideConstructorIssue', $issues[1]->type);
        $this->assertSame('(new \SplFileInfo(__FILE__))->openFile();', trim($issues[1]->snippet));
    }

    /** @test */
    public function operationFunctionCall() : void
    {
        $this->addFile(
            __METHOD__,
            <<<'CODE'
<?php
namespace  SfpTest\Psalm\DontOperationInsideConstructor\Unit;

use function opendir as builtinOpenDir;
function opendir() : void {}

class FileManager
{
    public function __construct()
    {
        $dh = \opendir(__DIR__);
        $dh = builtinOpenDir(__DIR__);
        $ret = readdir($dh);
        opendir();

        closedir($dh);
    }
    
    public function __invoke() : void
    {
        \opendir(__DIR__);
    }
}
CODE
        );
        $this->analyzeFile(__METHOD__, new Context());

        $issues = IssueBuffer::getIssuesData()[__METHOD__];

        $this->assertCount(4, $issues);
        $this->assertSame('$dh = \opendir(__DIR__);', trim($issues[0]->snippet));
        $this->assertSame('$dh = builtinOpenDir(__DIR__);', trim($issues[1]->snippet));
        $this->assertSame('$ret = readdir($dh);', trim($issues[2]->snippet));
        $this->assertSame('closedir($dh);', trim($issues[3]->snippet));
    }
}
