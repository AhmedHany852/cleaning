<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')
            ->toArray();
            $image = asset('uploads/settings/' .  $settings['site_logo']);
            $settings['site_logo'] =    $image;
        return  $settings;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_logo' => '',
            'site_name' => '',
         
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        foreach ($validator->validated() as $key => $input) {

            if (request()->hasFile('site_logo') && $request->file('site_logo')->isValid()) {

                $avatar = $request->file('photo');
                $avatar->store('uploads/settings/', 'public');
                $site_logo = $avatar->hashName();
            } else {
                $site_logo = null;
            }
            

            $settings =  Setting::updateOrCreate(
                [
                    'key' => $key,
                ],
                [
                    'value' => $input,
                ]
            );
        }
        $storedSettings  = Setting::pluck('value', 'key')
            ->toArray();

            if (isset($storedSettings['site_logo'])) {
                $storedSettings['site_logo'] = asset('storage/' . $storedSettings['site_logo']);
            }
        return response()->json(['isSuccess' => true, 'data' =>    $settings], 200);
    }
}
