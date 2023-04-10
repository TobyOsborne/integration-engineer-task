<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

use \Illuminate\Support\Facades\Redirect;

class MailerLiteKeyNotSet extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request, Exception $error)
    {
        if ($request->is('api/*')) {
            // if an API request, send back an error.
            return response()->json([
                'message' => $error->getMessage()
            ], 400);
        }

        // if the request is coming from a view (which should never happen), then redirect from here.
        return redirect()->route('settings')->withErrors(['message' => $error->getMessage()]);
    }
}
