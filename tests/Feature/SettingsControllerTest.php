<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

use Tests\TestCase;

class SettingControllerTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test that updating returns a valid response
     *
     * @return void
     */
    public function testUpdateValid()
    {
        Http::fake([
            'api.mailerlite.com/*' => Http::response([
                'account' => [
                  'email' => 'dummy@mailerlite.com',
                  'from' => 'dummy@mailerlite.com',
                  'id' => 1,
                  'name' => 'Dummy',
                  'subdomain' => 'dummy',
                  'timezone' => [
                    'gmt' => '+02:00',
                    'id' => 101,
                    'time' => 120,
                    'timezone' => '',
                    'title' => '',
                  ],
                ],
              ], 200),
        ]);
        
        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            "accept"=> "application/json"
        ])->put('/api/settings', ['api_key' => 'some key that will be valid']);

        $response->assertValid(['api_key']);

        $response->assertStatus(200);
    }


     /**
     * Test that updating an invalid key returns invalid response
     *
     * @return void
     */
    public function testUpdateInvalidKey()
    {
        Http::fake([
            'error' => [
              'code' => 302,
              'message' => 'API-Key Unauthorized',
            ],
          ]);

        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            "accept"=> "application/json"
        ])->put('/api/settings', ['api_key' => 'blarg']);

        $response->assertInvalid(['api_key']);

        $response->assertStatus(422);
    }

     /**
     * Test that updating an invalid per_page returns invalid response
     *
     * @return void
     */
    public function testUpdateInvalidPerPage()
    {
        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            "accept"=> "application/json"
        ])->put('/api/settings', ['per_page'=>'bla']);

        $response->assertInvalid(['per_page']);
        $response->assertValid(['api_key']);

        $response->assertStatus(422);
    }
}
