<?php

namespace Tests\Unit\Http\Middleware;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\FeatureFlags\FeatureFlags;
use App\FeatureFlags\Http\Middleware\Authorize;

class AuthorizeTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFlags::$auth = null;
    }

    public function tearDown()
    {
        parent::tearDown();

        FeatureFlags::$auth = null;
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testAbortsWhenUserIsNotAuthorized()
    {
        $request = new Request();
        $next = function () {
            $this->fail('Must not call next.');
        };

        FeatureFlags::authUsing(function () {
            return false;
        });

        (new Authorize())->handle($request, $next);
    }

    public function testPassesWhenCallbackReturnsTrue()
    {
        $request = new Request();
        $response = new Response();
        $next = function ($request) use ($response) {
            return $response;
        };

        FeatureFlags::authUsing(function () {
            return true;
        });

        $result = (new Authorize())->handle($request, $next);

        $this->assertSame($response, $result);
    }

    public function testEnsureTheSameRequestIsPassedToTheNextCaller()
    {
        $request = new Request();
        $next = function ($givenRequest) use ($request) {
            $this->assertSame($request, $givenRequest);
        };

        FeatureFlags::authUsing(function () {
            return true;
        });

        (new Authorize())->handle($request, $next);
    }
}