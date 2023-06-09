<?php

namespace LaravelJsonApi\OpenApiSpec\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelJsonApi\OpenApiSpec\Facades\GeneratorFacade;
use LaravelJsonApi\OpenApiSpec\Tests\Support\Database\Seeders\DatabaseSeeder;
use LaravelJsonApi\OpenApiSpec\Tests\TestCase;

class OpenApiSchemaTest extends TestCase
{
    use RefreshDatabase;

    private array $spec;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $output = GeneratorFacade::generate('v1', 'json');
        $this->spec = json_decode($output, true);
    }

    public function testHasManyShouldHaveArrayAsType(): void
    {
        $this->assertEquals('array', $this->spec['components']['schemas']['resources.posts.relationship.tags.update']['type']);
        $this->assertEquals('array', $this->spec['components']['schemas']['resources.posts.relationship.tags.attach']['type']);
        $this->assertEquals('array', $this->spec['components']['schemas']['resources.posts.relationship.tags.detach']['type']);
    }

    public function testItUsesTheDescriptionFromTheSchema()
    {
        $this->assertEquals('This is an example show all description', $this->spec['paths']['/posts']['get']['description']);
        $this->assertEquals('This is an example show one description', $this->spec['paths']['/posts/{post}']['get']['description']);
        $this->assertEquals('This is an example show posts author description', $this->spec['paths']['/posts/{post}/author']['get']['description']);
    }

    public function testItCreatesAnEmptyDescriptionIfASchemaDoesNotImplementTheDescribesActionsInterface()
    {
        $this->assertEquals('', $this->spec['paths']['/videos']['get']['description']);
    }


    public function testItUsesInfoConfig()
    {
        $this->assertEquals('My JSON:API', $this->spec['info']['title']);
        $this->assertEquals('JSON:API built using Laravel', $this->spec['info']['description']);
        $this->assertEquals('1.0.0', $this->spec['info']['version']);
        $this->assertEquals('https://example.com/terms-of-service', $this->spec['info']['termsOfService']);
        $this->assertEquals('MIT', $this->spec['info']['license']['name']);
        $this->assertEquals('https://opensource.org/licenses/MIT', $this->spec['info']['license']['url']);
        $this->assertEquals('API Support', $this->spec['info']['contact']['name']);
        $this->assertEquals('https://www.example.com/support', $this->spec['info']['contact']['url']);
        $this->assertEquals('support@example.com', $this->spec['info']['contact']['email']);
    }

    public function testItUsesAttributeToParseCustomActions()
    {
        $this->assertEquals('Publish one post', $this->spec['paths']['/posts/{post}/-actions/publish']['post']['summary']);
    }

    public function testItCreatesSecuritySchemes()
    {
        $this->assertEquals('http', $this->spec['components']['securitySchemes']['Bearer']['type']);
        $this->assertEquals('bearer', $this->spec['components']['securitySchemes']['Bearer']['scheme']);
        $this->assertEquals('JWT', $this->spec['components']['securitySchemes']['Bearer']['bearerFormat']);
        $this->assertEquals('Test Bearer description', $this->spec['components']['securitySchemes']['Bearer']['description']);
    }

    public function testItCreatesSecurityEntries()
    {
        $this->assertArrayHasKey('Bearer', $this->spec['security'][0]);
        $this->assertIsArray($this->spec['security'][0]['Bearer']);
    }
}
