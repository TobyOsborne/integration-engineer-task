<?php

namespace Tests\Unit;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class SettingModelTest extends TestCase
{
    use RefreshDatabase;


    /**
     * Validate that getPerPage returns a number.
     *
     * @return void
     */
    public function testGetPerPageInt()
    {
        $setting = Setting::factory()->create(['key'=>'per_page','value'=>10]);

        $this->assertIsInt($setting::getSubscribersPerPage());
    }

    /**
     * Validate that we can get the api key if it exists.
     *
     * @return void
     */
    public function testGetApiKeyTrue()
    {
        $setting = Setting::factory()->create(['key'=>'api_key','value'=>'yep']);

        $this->assertEquals('yep', $setting::getAPIKey());
    }

    /**
     * Validate that we can get false if the api key doesn't exists.
     *
     * @return void
     */
    public function testGetApiKeyFalse()
    {
        $setting = Setting::factory()->create(['key'=>'api_key','value'=>'']);

        $this->assertFalse($setting::getAPIKey());
    }
    
    /**
     * Validate that the has key function returns true when key exists
     *
     * @return void
     */
    public function testHasApiKeyTrue()
    {
        $setting = Setting::factory()->create(['key'=>'api_key','value'=>'yep']);
        $this->assertTrue($setting::hasAPIKey());
    }

    /**
     * Validate that the has key function returns false when no key
     *
     * @return void
     */
    public function testHasApiKeyFalse()
    {
        // when no key exists in the db at all
        $setting = Setting::factory()->create(['key'=>'per_page','value'=>10]);
        $this->assertFalse($setting->hasAPIKey());


        // when the db entry is there but the value is empty
        $setting = Setting::factory()->create(['key'=>'api_key','value'=>'']);
        $this->assertFalse($setting->hasAPIKey());
    }
}
