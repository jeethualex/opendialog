<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenDialogAi\Webchat\WebchatSetting;

class WebchatSettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return WebchatSetting::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return WebchatSetting::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($setting = WebchatSetting::find($id)) {
            $value = $request->get('value');

            if ($error = $this->validateValue($setting, $value)) {
                return response($error, 400);
            }

            $setting->update(['value' => $value]);

            return response()->noContent(200);
        }

        return response()->noContent(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param WebchatSetting $setting
     * @param string $newValue
     * @return string
     */
    private function validateValue(WebchatSetting $setting, $newValue)
    {
        switch ($setting->type) {
            case 'string':
                if (strlen($newValue) > 8192) {
                    return 'The maximum length for a string value is 8192.';
                }
                break;
            case 'number':
                if ($newValue && !is_numeric($newValue)) {
                    return 'This is not a valid number.';
                }
                break;
            case 'colour':
                if ($newValue && !preg_match('/#([a-f0-9]{3}){1,2}\b/i', $newValue)) {
                    return 'This is not a valid hex colour.';
                }
                break;
            case 'map':
                if ($newValue && json_decode($newValue) == null) {
                    return 'This is not a valid json value.';
                }
                break;
            case 'object':
                return 'Cannot update object value';
                break;
            case 'boolean':
                if ($newValue != '0' && $newValue != '1' && $newValue != 'false' && $newValue != 'true') {
                    return 'This is not a valid boolean value.';
                }
                break;
        }

        return null;
    }
}
