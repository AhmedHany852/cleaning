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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_logo' => '',
            'site_name' => '',
            'info_email' => '',
            'mobile' => '',
            'tiktok' => '',
            'instagram' => '',
            'maintenance_mode' => '',
            'siteMaintenanceMsg' => '',
            'tax_added_value' => '',
            'site_name_en' => '',
            'cr' => '',
            'vat' => '',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $settings = [];

        foreach ($validator->validated() as $key => $input) {
            if (request()->hasFile('site_logo') && $request->file('site_logo')->isValid()) {

                $avatar = $request->file('site_logo');
                $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
                $avatar->move(public_path('uploads/settings'), $avatarName);
                $input =  $avatarName;
            }


            Setting::updateOrCreate(['key' => $key], ['value' => $input]);
        }



        $settings = Setting::pluck('value', 'key')
            ->toArray();
        $image = asset('uploads/settings/' .  $settings['site_logo']);
        $settings['site_logo'] =    $image;
        return response()->json(['isSuccess' => true, 'data' =>    $settings], 200);



    }


}
