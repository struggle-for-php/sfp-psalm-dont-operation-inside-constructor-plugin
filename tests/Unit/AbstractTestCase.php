<?php
namespace SfpTest\Psalm\DontOperationInsideConstructor\Unit;

use function define;
use function defined;
use const DIRECTORY_SEPARATOR;
use function getcwd;
use function ini_set;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psalm\Config;
use Psalm\Internal\Analyzer\FileAnalyzer;
use Psalm\Internal\Analyzer\ProjectAnalyzer;
use Psalm\Internal\Provider\Providers;
use Psalm\Internal\RuntimeCaches;
use SfpTest\Psalm\DontOperationInsideConstructor\Unit\Internal\Provider;
use RuntimeException;

class AbstractTestCase extends BaseTestCase
{
    /** @var string */
    protected static $src_dir_path;

    protected ProjectAnalyzer $project_analyzer;

    protected Provider\FakeFileProvider $file_provider;

    public static function setUpBeforeClass() : void
    {
        ini_set('memory_limit', '-1');

        if (!defined('PSALM_VERSION')) {
            define('PSALM_VERSION', '2.0.0');
        }

        if (!defined('PHP_PARSER_VERSION')) {
            define('PHP_PARSER_VERSION', '4.0.0');
        }

        parent::setUpBeforeClass();
        self::$src_dir_path = getcwd() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
    }

    protected function makeConfig() : Config
    {
        return new TestConfig();
    }

    public function setUp() : void
    {
        parent::setUp();

        RuntimeCaches::clearAll();

        $this->file_provider = new Provider\FakeFileProvider();

        $config = $this->makeConfig();

        $providers = new Providers(
            $this->file_provider,
            new Provider\FakeParserCacheProvider($config)
        );

        $this->project_analyzer = new ProjectAnalyzer(
            $config,
            $providers
        );

        $this->project_analyzer->setPhpVersion('7.4', 'tests');
        $config->initializePlugins($this->project_analyzer);
    }

    public function tearDown() : void
    {
        RuntimeCaches::clearAll();
    }

    public function addFile(string $file_path, string $contents): void
    {
        $this->file_provider->registerFile($file_path, $contents);
        $this->project_analyzer->getCodebase()->scanner->addFileToShallowScan($file_path);
    }

    public function analyzeFile(string $file_path, \Psalm\Context $context, bool $track_unused_suppressions = true, bool $taint_flow_tracking = false): void
    {
        $codebase = $this->project_analyzer->getCodebase();

        if ($taint_flow_tracking) {
            $this->project_analyzer->trackTaintedInputs();
        }

        $codebase->addFilesToAnalyze([$file_path => $file_path]);

        $codebase->scanFiles();

        $codebase->config->visitStubFiles($codebase);

        if ($codebase->alter_code) {
            $this->project_analyzer->interpretRefactors();
        }

        $this->project_analyzer->trackUnusedSuppressions();

        $file_analyzer = new FileAnalyzer(
            $this->project_analyzer,
            $file_path,
            $codebase->config->shortenFileName($file_path)
        );
        $file_analyzer->analyze($context);

        if ($codebase->taint_flow_graph) {
            $codebase->taint_flow_graph->connectSinksAndSources();
        }

        if ($track_unused_suppressions) {
            \Psalm\IssueBuffer::processUnusedSuppressions($codebase->file_provider);
        }
    }

    protected function getTestName(bool $withDataSet = true): string
    {
        $name = parent::getName($withDataSet);
        /**
         * @psalm-suppress TypeDoesNotContainNull PHPUnit 8.2 made it non-nullable again
         */
        if (null === $name) {
            throw new RuntimeException('anonymous test - shouldn\'t happen');
        }

        return $name;
    }
}
