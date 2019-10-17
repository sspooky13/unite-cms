<?php


namespace UniteCMS\CoreBundle\Tests\GraphQL;

use GraphQL\Utils\BuildSchema;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Utils\SchemaPrinter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use UniteCMS\CoreBundle\Domain\Domain;
use UniteCMS\CoreBundle\Domain\DomainManager;
use UniteCMS\CoreBundle\GraphQL\SchemaManager;

class SchemaManagerTest extends KernelTestCase
{
    /**
     * @expectedException \GraphQL\Error\InvariantViolation
     */
    public function testBasicInValidSchemaCreation()
    {
        $this->assertValidSchema('
            type Article implements UniteContent {
                foo: String
            }
        ');
        $this->addToAssertionCount(1);
    }

    public function testBasicValidSchemaCreation()
    {
        $this->assertValidSchema('
            type Article implements UniteContent {
                id: ID
                title: String @field(type: "text")
            }
        ');
        $this->addToAssertionCount(1);
    }

    public function setUp() {
        static::bootKernel();
        static::$container->get('security.token_storage')->setToken(new AnonymousToken('', ''));
    }

    /**
     * @param string $schema
     *
     * @return \GraphQL\Language\AST\DocumentNode
     * @throws \GraphQL\Error\Error
     * @throws \GraphQL\Error\SyntaxError
     */
    protected function buildSchema(string $schema = '') : DocumentNode {

        $schemaManager = static::$container->get(SchemaManager::class);
        $domainManager = static::$container->get(DomainManager::class);
        $domain = $domainManager->current();

        $domainManager->setCurrentDomain(new Domain(
            'test',
            $domain->getContentManager(),
            $domain->getUserManager(),
            array_merge($domain->getSchema(), [$schema])
        ));

        return $schemaManager->buildCacheableSchema();
    }

    /**
     * @param string $schema
     * @throws \GraphQL\Error\Error
     * @throws \GraphQL\Error\SyntaxError
     */
    protected function assertValidSchema(string $schema = '') : void {
        BuildSchema::build($this->buildSchema($schema))->assertValid();
    }
}