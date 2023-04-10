<?php

/**
 * The settings controller, used to create functions that routes can connect to,
 * I used a controller to keep functions out of the routes, and to allow extensibility later.
 */

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Http\Requests\UpdateSettingsRequest;

class SettingsController extends Controller
{
    /**
     * Display the settings resource.
     * This is on index, because theres no reason to have a page to display settings,
     * without te ability to change it, in this use-case.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('settings')->with(['title'=>__('views.settings')]);
    }

    /**
     * Update the specified settings in storage.
     *
     * @param  \App\Http\Requests\UpdateSettingsRequest  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSettingsRequest $request, Setting $setting)
    {
        // get the values for the only settings we support.
        $validated = $request->safe()->only(['api_key', 'per_page']);

        // if there are fields to update the do it.
        if (!empty($validated)) {
            // loop the data and prepare restructure it for database entry.
            $data = array_map(
                fn($key, $value) => ['key'=>$key,'value'=>$value],
                array_keys($validated),
                $validated,
            );

            // update the values in the database.
            $setting::upsert($data, ['key'], ['value']);
        }

        // Return the response.
        return response()->json([
            'data'=>Setting::pluck('value', 'key')->toArray()
        ]);
    }
}
