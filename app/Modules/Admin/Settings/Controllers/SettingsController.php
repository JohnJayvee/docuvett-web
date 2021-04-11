<?php

namespace App\Modules\Admin\Settings\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact\Contact;
use App\Models\Correspondence\Correspondence;
use App\Models\EventRegistration\EventRegistration;
use App\Models\Setting\Setting;
use App\Models\Action\Action;
use App\Models\Result\Result;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    const REQUEST_PARAM_SETTINGS = 'settings';

    /**
     * Returns hidden and not hidden fields which are set in settings_fields.php
     * for Settings page
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $result = [];

        if ($request->filled(self::REQUEST_PARAM_SETTINGS)) {
            $settings = (array) $request->input(self::REQUEST_PARAM_SETTINGS, null);

            $fields = collect();
            foreach ($settings as $setting) {
                $fields = $fields->merge(Setting::getDefinedSettingFields($setting));
            }

            $result = Setting::getAllSettings()->filter(function ($value) use ($fields) {
                $field = $fields->filter(function ($v) use ($value) {
                    return $v['name'] == $value->name;
                })->first();

                return $field ? true : false;
            });
        }
        return response()->json($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $result = [];

        if ($request->filled(self::REQUEST_PARAM_SETTINGS)) {
            $settings = $request->input(self::REQUEST_PARAM_SETTINGS, null);

            $fields = Setting::getDefinedSettingFields($settings);
            $rules = Setting::getValidationRules($settings);
            $data = $this->validate($request, $rules);

            $validSettings = array_keys($rules);

            foreach ($data as $key => $val) {
                if (in_array($key, $validSettings)) {
                    $field = $fields->filter(function ($value) use ($key) {
                        return $value['name'] == $key;
                    })->first();

                    Setting::set(
                        $key,
                        ($val ? $val : ''),
                        Setting::getDataType($settings, $key),
                        ($field ? $field['is_hidden'] : false)
                    );
                }
            }
            $result = Setting::getAllSettings(false);
        }

        return response()->json($result);
    }

    /**
     * Returns settings by setting name
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $result = Setting::where(Setting::COLUMN_NAME, $request->settings_name)->first()[Setting::COLUMN_VALUE] ?? '';

        return response()->json($result);
    }

    public function refactorRegistrations(Request $request){
        $eventRegistrations = EventRegistration::whereNull('portfolio_id')->orWhereNull('agent_id')->get();

        foreach($eventRegistrations as $eventRegistration){
            $registrationContact = Contact::find($eventRegistration->contact_id);
            $contactPortfolios = $registrationContact->portfolios;
            $portfolioID = null;
            foreach($contactPortfolios as $contactPortfolio){
                $portfolioID = $contactPortfolio->id;
            }
            $eventRegistration->portfolio_id = $portfolioID;
            if(isset($registrationContact->referred_by)){
                $eventRegistration->agent_id = $registrationContact->referred_by;
            }
            $eventRegistration->save();
        }

        return $eventRegistrations;
    }

    public function refactorCorrs(Request $request){
        $results = Result::all();
        $actions = Action::all();
        $toRefactor = Correspondence::where('action', '!=', null)->orWhere('result', '!=', null)->get();

        foreach($toRefactor as $corr){
            foreach($results as $result){
                if($corr->result == $result->name){
                    $corr->result_id = $result->id;
                }
            }

            $corr->save();

            foreach($actions as $action){
                if($corr->action == $action->name){
                    $corr->action_id = $action->id;
                }
            }

            $corr->save();
        }

        return 'Done';
    }

    public function splitNames(Request $request){
        $response = \Artisan::call('system:split-contact-names');
        return $response;
    }
}
