<?php
namespace Tests\Unit\Middleware;
use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Middleware\EnsureCorrectAPIHeaders;

class EnsureCorrectAPIHeadersTest extends TestCase
{
    /**
    * @test
    * @watch
    */
    public function it_aborts_get_request_if_accept_header_does_not_adhere_to_json_api_specification(){
        $request = Request::create('/test', 'GET');
        $middleware = new EnsureCorrectAPIHeaders;
        
        /** @var Response $response */
        $response = $middleware->handle($request, function($request){
            $this->fail('Did not abort request because of invalid Accept header');
        });
        
        $this->assertEquals(406, $response->status());
    }

    /**
    * @test
    * @watch
    */
    public function it_accepts_get_request_if_accept_header_adheres_to_json_api_spec()
    {
        $request = Request::create('/test', 'GET');
        $request->headers->set('accept', 'application/vnd.api+json');

        $middleware = new EnsureCorrectAPIHeaders;

        /** @var Response $response */
        $response = $middleware->handle($request, function($request){
            return new Response();
        });

        $this->assertEquals(200, $response->status());
    }

    /**
    * @test
    * @watch
    */
    public function it_aborts_post_request_if_content_type_header_does_not_adhere_to_json_api_specification()
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('accept', 'application/vnd.api+json');
        
        $middleware = new EnsureCorrectAPIHeaders;
        /** @var Response $response */
        $response = $middleware->handle($request, function($request){        
            $this->fail('Did not abort request because of invalid Content-Type header');
        });

        $this->assertEquals(415, $response->status());
    }

    /**
    * @test
    * @watch
    */
    public function it_aborts_patch_request_if_content_type_header_does_not_adhere_to_json_api_specification()
    {
        $request = Request::create('/test', 'PATCH');
        $request->headers->set('accept', 'application/vnd.api+json');
        
        $middleware = new EnsureCorrectAPIHeaders;
        /** @var Response $response */
        $response = $middleware->handle($request, function($request){
            $this->fail('Did not abort request because of invalid Content-Type header');
        });

        $this->assertEquals(415, $response->status());
    }

    /**
    * @test
    * @watch
    */
    public function it_accepts_post_request_if_content_type_header_adheres_to_json_api_specification()
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('accept', 'application/vnd.api+json');
        $request->headers->set('content-type', 'application/vnd.api+json');

        $middleware = new EnsureCorrectAPIHeaders;
        /** @var Response $response */
        $response = $middleware->handle($request, function($request){
            return new Response();
        });

        $this->assertEquals(200, $response->status());
    }

    /**
    * @test
    * @watch
    */
    public function it_accepts_patch_request_if_content_type_header_adheres_to_json_api_specification()
    {
        $request = Request::create('/test', 'PATCH');
        $request->headers->set('accept', 'application/vnd.api+json');
        $request->headers->set('content-type', 'application/vnd.api+json');

        $middleware = new EnsureCorrectAPIHeaders;
        /** @var Response $response */
        $response = $middleware->handle($request, function($request) {
            return new Response();
        });

        $this->assertEquals(200, $response->status());
    }

    /**
    * @test
    * @watch
    */    
    public function it_ensures_that_a_content_type_header_adhering_to_json_api_specification_is_on_response()
    {
        $request = Request::create('/test', 'GET');
        $request->headers->set('accept', 'application/vnd.api+json');
        $request->headers->set('content-type', 'application/vnd.api+json');
        
        $middleware = new EnsureCorrectAPIHeaders;
        /** @var Response $response */
        $response = $middleware->handle($request, function($request){
            return new Response();
        });
        
        $this->assertEquals(200, $response->status());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('content-type'));
    }

    public function when_aborting_for_a_missing_accept_header_the_correct_content_type_header_is_set()
    {
        $request = Request::create('/test', 'GET');
        $middleware = new EnsureCorrectAPIHeaders;
        /** @var Response $response */
        $response = $middleware->handle($request, function($request){
            return new Response();
        });

        $this->assertEquals($response->status(), 406);
        $this->assertEquals('application/vnd.api+json', $response->headers->get('content-type'));
    }

    public function when_aborting_for_a_missing_content_type_header_the_correct_content_type_header_is_set()
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('accept', 'application/vnd.api+json');

        $middleware = new EnsureCorrectAPIHeaders;
        /** @var Response $response */
        $response = $middleware->handle($request, function($request){
            return new Response();
        });

        $this->assertEquals(415, $response->status());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('content-type'));
    }
}