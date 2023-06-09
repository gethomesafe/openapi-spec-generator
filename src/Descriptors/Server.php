<?php

namespace LaravelJsonApi\OpenApiSpec\Descriptors;

use GoldSpecDigital\ObjectOrientedOAS\Objects;
use Illuminate\Support\Arr;
use LaravelJsonApi\OpenApiSpec\Descriptors\Descriptor as BaseDescriptor;

class Server extends BaseDescriptor
{
    /**
     * @return \GoldSpecDigital\ObjectOrientedOAS\Objects\Info
     */
    public function info(): Objects\Info
    {
        return Objects\Info::create()
            ->title(config("openapi.servers.{$this->generator->key()}.info.title"))
            ->description(config("openapi.servers.{$this->generator->key()}.info.description"))
            ->version(config("openapi.servers.{$this->generator->key()}.info.version"))
            ->termsOfService(config("openapi.servers.{$this->generator->key()}.info.termsOfService"))
            ->license(
                Arr::has(config("openapi.servers.{$this->generator->key()}.info"), 'license')
                    ? Objects\License::create()
                        ->name(config("openapi.servers.{$this->generator->key()}.info.license.name"))
                        ->url(config("openapi.servers.{$this->generator->key()}.info.license.url"))
                    : null
            )
            ->contact(
                Arr::has(config("openapi.servers.{$this->generator->key()}.info"), 'contact')
                    ? Objects\Contact::create()
                        ->name(config("openapi.servers.{$this->generator->key()}.info.contact.name"))
                        ->email(config("openapi.servers.{$this->generator->key()}.info.contact.email"))
                        ->url(config("openapi.servers.{$this->generator->key()}.info.contact.url"))
                    : null
            );
    }

    /**
     * @return \LaravelJsonApi\Core\Server\Server[]
     *
     * @todo Allow Configuration
     * @todo Use for enums?
     * @todo Extract only URI Server Prefix and let domain be set separately
     */
    public function servers(): array
    {
        return [
            Objects\Server::create()
                ->url('{serverUrl}')
                ->variables(
                    Objects\ServerVariable::create('serverUrl')
                        ->default($this->generator->server()->url())
                ),
        ];
    }

    /**
     * @return \GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityScheme[]
     */
    public function securitySchemes(): array
    {
        return collect(config("openapi.servers.{$this->generator->key()}.securitySchemes") ?? [])
            ->map(function ($scheme, $key) {
                return Objects\SecurityScheme::create()
                    ->objectId($key)
                    ->type($scheme['type'])
                    ->scheme($scheme['scheme'] ?? null)
                    ->bearerFormat($scheme['bearerFormat'] ?? null)
                    ->description($scheme['description'] ?? null)
                    ->name($scheme['name'] ?? null)
                    ->in($scheme['in'] ?? null)
                    ->openIdConnectUrl($scheme['openIdConnectUrl'] ?? null)
                    ->flows(
                        ...collect($scheme['flows'] ?? [])
                            ->map(function ($flow, $key) {
                                return Objects\OAuthFlow::create()
                                    ->flow($key)
                                    ->authorizationUrl($flow['authorizationUrl'] ?? null)
                                    ->tokenUrl($flow['tokenUrl'] ?? null)
                                    ->refreshUrl($flow['refreshUrl'] ?? null)
                                    ->scopes($flow['scopes'] ?? []);
                            })
                            ->toArray()
                    );
            })
            ->toArray();
    }

    /**
     * @return \GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityRequirement[]
     */
    public function security(): array
    {
        return collect(config("openapi.servers.{$this->generator->key()}.security") ?? [])
            ->map(function ($security, $key) {
                return Objects\SecurityRequirement::create()
                    ->securityScheme(
                        is_string($key) ? $key : $security
                    )
                    ->scopes(
                        ...(is_array($security) ? $security : [])
                    );
            })
            ->toArray();
    }
}
