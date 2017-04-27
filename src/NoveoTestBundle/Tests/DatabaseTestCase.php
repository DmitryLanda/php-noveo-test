<?php
namespace NoveoTestBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class DatabaseTestCase extends WebTestCase
{
    protected function setUp()
    {
        $this->environment = 'test';

        parent::setUp();
    }

    protected function tearDown()
    {
        $annotations = $this->getAnnotations();
        if (array_key_exists('resetDatabase', $annotations['method'])) {
            shell_exec(sprintf('php %s/console doctrine:schema:drop --force -e=test', __DIR__ . '/../../../bin'));
            shell_exec(sprintf('php %s/console doctrine:schema:update --force -e=test', __DIR__ . '/../../../bin'));
            shell_exec(sprintf('php %s/console doctrine:fixtures:load -n -e=test', __DIR__ . '/../../../bin'));
//            $this->loadFixtureFiles([
//                '@NoveoTestBundle/DataFixtures/ORM/fixtures.yml'
//            ]);
        }

        parent::tearDown();
    }

    public static function setUpBeforeClass()
    {
        shell_exec(sprintf('php %s/console doctrine:schema:drop --force -e=test', __DIR__ . '/../../../bin'));
        shell_exec(sprintf('php %s/console doctrine:schema:update --force -e=test', __DIR__ . '/../../../bin'));
        shell_exec(sprintf('php %s/console doctrine:fixtures:load -n -e=test', __DIR__ . '/../../../bin'));
    }

    public function makeClient($authentication = false, array $params = array())
    {
        return parent::makeClient($authentication, ['HTTP_HOST' => 'noveo-test']);
    }

    public function assertJsonEquals($expectedJson, $actualJson, $skip = [])
    {
        $expected = json_decode($expectedJson, true);
        $actual = json_decode($actualJson, true);

        $this->assertCount(count($expected), $actual);
        $this->assertArrayEquals($expected, $actual, $skip);
    }

    public function assertArrayEquals(array $expected, array $actual, $skip = [])
    {
        foreach (array_keys($expected) as $key) {
            //ignore check if key in skip list
            if (array_search($key, $skip) !== false) {
                return;
            }

            $this->assertArrayHasKey($key, $actual);
            //run check recursively to walk through all subsets
            if (is_array($expected[$key])) {
                $this->assertArrayEquals($expected[$key], $actual[$key], $skip);
            } else {
                $this->assertEquals($expected[$key], $actual[$key]);
            }
        }
    }
}