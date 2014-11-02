<?php

namespace Neoxygen\NeoClient\Tests;

use Neoxygen\NeoClient\ClientBuilder;

/**
 * @group functional
 */
class CoreCommandsTest extends \PHPUnit_Framework_TestCase
{
    /* var \Neoxygen\NeoClient\Client */
    protected $client;

    public function setUp()
    {
        $client = ClientBuilder::create()
            ->addDefaultLocalConnection()
            ->build();

        $this->client = $client;
    }

    public function testGetRoot()
    {
        $response = $this->client->getRoot();
        $root = $response->getBody();

        $this->assertArrayHasKey('data', $root);
        $this->assertArrayHasKey('management', $root);
    }

    public function testGetLabels()
    {
        $response = $this->client->getLabels();
        $labels = $response->getBody();

        $this->assertInternalType('array', $labels);
    }

    public function testCreateAndListIndex()
    {
        $this->client->createIndex('User', 'email');
        $response = $this->client->listIndex('User');
        $indexes = $response->getBody();

        $this->assertContains('email', $indexes);
    }

    public function testDropIndex()
    {
        $this->client->createIndex('Drop', 'user');
        $this->assertTrue($this->client->isIndexed('Drop', 'user'));
        $this->client->dropIndex('Drop', 'user');
        $this->assertFalse($this->client->isIndexed('Drop', 'user'));
    }

    public function testListIndexes()
    {
        $this->client->createIndex('List1', 'property');
        $this->client->createIndex('List2', 'property');
        $response = $this->client->listIndexes();
        $indexes = $response->getBody();

        $this->assertArrayHasKey('List1', $indexes);
        $this->assertArrayHasKey('List2', $indexes);
    }

    public function testCreateUniqueConstraint()
    {
        $this->client->createUniqueConstraint('Label', 'uniqueProperty');
        $constraints = $this->client->getUniqueConstraints()->getBody();

        $this->assertArrayHasKey('Label', $constraints);
        $this->assertContains('uniqueProperty', $constraints['Label']);
    }

    public function testDropUniqueConstraint()
    {
        $this->client->createUniqueConstraint('ToDrop', 'username');
        $this->assertArrayHasKey('ToDrop', $this->client->getUniqueConstraints()->getBody());
        $this->client->dropUniqueConstraint('ToDrop', 'username');
        $this->assertArrayNotHasKey('ToDrop', $this->client->getUniqueConstraints()->getBody());
    }


}