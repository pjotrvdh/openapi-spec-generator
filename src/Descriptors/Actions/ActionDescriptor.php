<?php


namespace LaravelJsonApi\OpenApiSpec\Descriptors\Actions;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use LaravelJsonApi\Contracts\Server\Server;
use LaravelJsonApi\OpenApiSpec\Builders\Paths\Operation\ParameterBuilder;
use LaravelJsonApi\OpenApiSpec\Builders\Paths\Operation\RequestBodyBuilder;
use LaravelJsonApi\OpenApiSpec\Builders\Paths\Operation\ResponseBuilder;
use LaravelJsonApi\OpenApiSpec\Contracts\Descriptors\ActionDescriptor as ActionDescriptorContract;
use LaravelJsonApi\OpenApiSpec\Generator;
use LaravelJsonApi\OpenApiSpec\Route;

abstract class ActionDescriptor implements ActionDescriptorContract
{

    protected ParameterBuilder $parameterBuilder;

    protected RequestBodyBuilder $requestBodyBuilder;

    protected ResponseBuilder $responseBuilder;

    protected Server $server;

    protected Route $route;

    public function __construct(
      ParameterBuilder $parameterBuilder,
      RequestBodyBuilder $requestBodyBuilder,
      ResponseBuilder $responseBuilder,
      Generator $generator,
      Route $route,
    ) {
        $this->parameterBuilder = $parameterBuilder;
        $this->requestBodyBuilder = $requestBodyBuilder;
        $this->responseBuilder = $responseBuilder;

        $this->server = $generator->server();
        $this->route = $route;
    }

    /**
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     */
    public function action(): Operation
    {
        $operation = match ($this->route->method()) {
            'GET' => Operation::get(),
            'POST' => Operation::post(),
            'PATCH' => Operation::patch(),
            'DELETE' => Operation::delete(),
        };

        return $operation
          ->operationId($this->route->id())
          ->responses(...$this->responses())
          ->parameters(...$this->parameters())
          ->requestBody($this->requestBody())
          ->description($this->description())
          ->summary($this->summary())
          ->tags(...$this->tags());
    }

    /**
     * @return string
     */
    protected function description(): string
    {
        return '';
    }

    /**
     * @return string
     */
    protected function summary(): string{
        return '';
    }

    /**
     * @return string[]
     */
    protected function tags(): array{
        return [ucfirst($this->route->name())];
    }

    /**
     * @return Parameter[]
     */
    protected function parameters(): array{
        return $this->parameterBuilder->build($this->route);
    }

    /**
     * @return Response[]
     */
    protected function responses(): array{
        return $this->responseBuilder->build($this->route);
    }

    /**
     * @return RequestBody|null
     */
    protected function requestBody(): ?RequestBody{
        return $this->requestBodyBuilder->build($this->route);
    }

}