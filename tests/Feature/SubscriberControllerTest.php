<?php

namespace Tests\Feature;

use App\Models\Setting;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

use Tests\TestCase;

class SubscriberControllerTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test listing subscribers
     *
     * @return void
     */
    public function testListValid()
    {
        Http::fake([
            'api.mailerlite.com/*' => Http::response([
                [
                  'id' => 1343965485,
                  'name' => 'John',
                  'email' => 'demo@mailerlite.com',
                  'sent' => 0,
                  'opened' => 0,
                  'clicked' => 0,
                  'type' => 'active',
                  'fields' => [
                    [
                      'key' => 'email',
                      'value' => 'demo@mailerlite.com',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'name',
                      'value' => 'John',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'last_name',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'company',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'country',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'city',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'phone',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'state',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'zip',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                  ],
                  'date_subscribe' => null,
                  'date_unsubscribe' => null,
                  'date_created' => '2016-04-04',
                  'date_updated' => null,
                ],
              ], 200),
        ]);

        $setting = Setting::factory()->create(['key'=>'api_key','value'=>'yep']);

        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            "accept"=> "application/json"
        ])->get('/api/subscribers');


        $response->assertJson(fn($json)=>$json->hasAll(['data','draw','recordsTotal','recordsFiltered']));
        $response->assertStatus(200);
    }

    /**
     * Test listing subscribers - invalid
     *
     * @return void
     */
    public function testListInvalid()
    {
        Http::fake([
            'api.mailerlite.com/*' => Http::response(null, 404),
        ]);

        $setting = Setting::factory()->create(['key'=>'api_key','value'=>'yep']);

        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            "accept"=> "application/json"
        ])->get('/api/subscribers');

        $response->assertStatus(404);
    }

    /**
     * Test updating subscribers
     *
     * @return void
     */
    public function testUpdateValid()
    {
        Http::fake([
            'api.mailerlite.com/*' => Http::response([
                  'id' => 1343965485,
                  'name' => 'John',
                  'email' => 'demo@mailerlite.com',
                  'sent' => 0,
                  'opened' => 0,
                  'clicked' => 0,
                  'type' => 'active',
                  'fields' => [
                    [
                      'key' => 'email',
                      'value' => 'demo@mailerlite.com',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'name',
                      'value' => 'John',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'last_name',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'company',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'country',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'city',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'phone',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'state',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'zip',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                  ],
                  'date_subscribe' => null,
                  'date_unsubscribe' => null,
                  'date_created' => '2016-04-04',
                  'date_updated' => null,
                ], 200),
        ]);

        // prepare the api key
        $setting = Setting::factory()->create(['key'=>'api_key','value'=>'yep']);

        // send the request
        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            "accept"=> "application/json"
        ])->put('/api/subscribers/demo@mailerlite.com', ['name'=>'hello','fields'=>['country'=>'UK']]);

        // assert the response.
        $response->assertJson(['email'=>true]);

        $response->assertStatus(200);
    }

    /**
     * Test that the update subscribers local validation works.
     *
     * @return void
     */
    public function testUpdateInvalidSubmit()
    {
        // send the request
        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            "accept"=> "application/json"
        ])->put('/api/subscribers/demo@mailerlite.com', []);

        // assert the response.
        $response->assertInvalid(['name','fields.country']);
    }

    /**
     * Test creating subscribers
     *
     * @return void
     */
    public function testCreateValid()
    {
        Http::fake([
            'api.mailerlite.com/*' => Http::response([
                  'id' => 1343965485,
                  'name' => 'John',
                  'email' => 'demo@mailerlite.com',
                  'sent' => 0,
                  'opened' => 0,
                  'clicked' => 0,
                  'type' => 'active',
                  'fields' => [
                    [
                      'key' => 'email',
                      'value' => 'demo@mailerlite.com',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'name',
                      'value' => 'John',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'last_name',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'company',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'country',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'city',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'phone',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'state',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                    [
                      'key' => 'zip',
                      'value' => '',
                      'type' => 'TEXT',
                    ],
                  ],
                  'date_subscribe' => null,
                  'date_unsubscribe' => null,
                  'date_created' => '2016-04-04',
                  'date_updated' => null,
            ], 200),
        ]);

        // prepare the api key
        $setting = Setting::factory()->create(['key'=>'api_key','value'=>'yep']);

        // send the request
        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            "accept"=> "application/json"
        ])->post(
            '/api/subscribers',
            ['email'=>'demo@mailerlite.com','name'=>'hello','fields'=>['country'=>'UK']]
        );

        // assert the response.
        $response->assertJson(['email'=>true]);

        $response->assertStatus(200);
    }

    /**
     * Test that the creating subscribers local validation works.
     *
     * @return void
     */
    public function testCreateInvalidSubmit()
    {
        // send the request
        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            "accept"=> "application/json"
        ])->post('/api/subscribers', ['email'=>'blarg']);

        // assert the response.
        $response->assertInvalid(['name','fields.country','email']);
    }

    /**
     * Test that the delete subscriber works
     *
     * @return void
     */
    public function testDeleteSubscriberValid()
    {
        Http::fake([
            'api.mailerlite.com/*' => Http::response(null, 204),
        ]);
        
        // send the request
        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            "accept"=> "application/json"
        ])->delete('/api/subscribers/demo@mailerlite.com');

        // assert the response.
        $response->assertJson(['message'=>true]);
    }

    /**
     * Test that the delete subscriber works
     *
     * @return void
     */
    public function testDeleteSubscriberInvalid()
    {
        Http::fake([
            'api.mailerlite.com/*' => Http::response([
                "error"=>[
                    "code"=> 404,
                    "message"=> "Not found"
                ],
                "error_details"=> [
                    "message"=> "Resource not found."
                ]
            ], 404),
        ]);

        // send the request
        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            "accept"=> "application/json"
        ])->delete('/api/subscribers/');

        // assert the response.
        $response->assertJson(['message'=>true]);
    }
}
