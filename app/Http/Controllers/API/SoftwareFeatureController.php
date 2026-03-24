<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SoftwareFeatureController extends Controller
{
    private function softwareExists($softwareName)
    {
        return DB::table('software_features')
            ->where('software_name', $softwareName)
            ->exists();
    }

    private function softwareDetailsExists($softwareName)
    {
        return DB::table('software_details')
            ->where('software_name', $softwareName)
            ->exists();
    }

    public function getSoftwareRequests(Request $request)
    {
        $software = $request->query('software');
        if(!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $software = DB::table('software_requests')->where('software_name',$software)->get();
        return response()->json(['success'=>200,'message'=>'Software requests fetched','data'=>$software]);
    }

    public function getSoftwareRequest(Request $request, $id)
    {
        $software = $request->query('software');
        if(!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $software = DB::table('software_requests')->where('id',$id)->where('software_name',$software)->first();
        if(!$software) return response()->json(['success'=>404,'message'=>'Software request not found','data'=>'']);

        return response()->json(['success'=>200,'message'=>'Software request fetched','data'=>$software]);
    }

    public function createSoftwareRequest(Request $request)
    {
        $software = $request->query('software');
        if(!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $validator = Validator::make($request->all(), [
            'client_name'=>'required|string',
            'email'=>'required|email',
            'phone'=>'required|string',
            'requested_date'=>'nullable|date',
            'message'=>'nullable|string',
            'status'=>'nullable|in:pending,approved,rejected'
        ]);

        if($validator->fails()) return response()->json(['success'=>422,'message'=>$validator->errors(),'data'=>'']);

        DB::beginTransaction();
        try
        {
            $id = DB::table('software_requests')->insertGetId([
                'software_name'=>$software,
                'client_name'=>$request->client_name,
                'email'=>$request->email,
                'phone'=>$request->phone,
                'requested_date'=>$request->requested_date,
                'message'=>$request->message,
                'status'=>$request->status ?? 'pending',
                'created_at'=>now(),
                'updated_at'=>now()
            ]);

            $software = DB::table('software_requests')->where('id',$id)->first();
            DB::commit();

            return response()->json(['success'=>200,'message'=>'Software request created','data'=>$software]);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json(['success'=>500,'message'=>'Failed to create software request','error'=>$e->getMessage()]);
        }
    }

    public function updateSoftwareRequest(Request $request, $id)
    {
        $softwareName = $request->query('software');
        if (!$softwareName || !$this->softwareExists($softwareName)) 
        {
            return response()->json(['success' => 404, 'message' => 'Invalid software', 'data' => '']);
        }

        $softwareRequest = DB::table('software_requests')
            ->where('id', $id)
            ->where('software_name', $softwareName)
            ->first();

        if (!$softwareRequest) 
        {
            return response()->json(['success' => 404, 'message' => 'Software request not found', 'data' => '']);
        }

        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'requested_date' => 'nullable|date',
            'message' => 'nullable|string',
            'status' => 'required|in:approved,rejected'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['success' => 422, 'message' => $validator->errors(), 'data' => '']);
        }

        DB::beginTransaction();
        try
        {
            $newStatus = $request->status;
            $featureStatus = $newStatus === 'approved' ? 'active' : 'inactive';
            DB::table('software_requests')->where('id', $id)->update([
                'client_name' => $request->client_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'requested_date' => $request->requested_date,
                'message' => $request->message,
                'status' => $newStatus,
                'updated_at' => now(),
            ]);

            DB::table('software_features')
                ->where('id', $softwareRequest->software_id)
                ->update([
                    'status' => $featureStatus,
                    'updated_at' => now(),
                ]);

            DB::commit();

            $updated = DB::table('software_requests')->where('id', $id)->first();

            return response()->json([
                'success' => 200,
                'message' => 'Software request updated successfully',
                'data' => $updated,
            ]);

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => 500,
                'message' => 'Failed to update software request',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function deleteSoftwareRequest(Request $request, $id)
    {
        $software = $request->query('software');
        if(!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $software = DB::table('software_requests')->where('id',$id)->where('software_name',$software)->first();
        if(!$software) return response()->json(['success'=>404,'message'=>'Software request not found','data'=>'']);

        DB::beginTransaction();
        try
        {
            DB::table('software_requests')->where('id',$id)->delete();
            DB::commit();
            return response()->json(['success'=>200,'message'=>'Software request deleted','data'=>'']);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json(['success'=>500,'message'=>'Failed to delete Software request','error'=>$e->getMessage()]);
        }
    }

    public function getFeatures(Request $request)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success' => 404, 'message' => 'Invalid software', 'data' => '']);
        }

        $features = DB::table('software_features')->where('software_name', $software)->get();
        return response()->json(['success' => 200, 'message' => 'Features fetched', 'data' => $features]);
    }

    public function getFeature(Request $request, $id)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success' => 404, 'message' => 'Invalid software', 'data' => '']);
        }

        $feature = DB::table('software_features')->where('id', $id)->where('software_name', $software)->first();
        if (!$feature) return response()->json(['success' => 404, 'message' => 'Feature not found', 'data' => '']);

        return response()->json(['success' => 200, 'message' => 'Feature fetched', 'data' => $feature]);
    }

    public function createFeature(Request $request)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success' => 404, 'message' => 'Invalid software', 'data' => '']);
        }

        $validator = Validator::make($request->all(), [
            'feature_name' => 'required|string|unique:software_features,feature_name',
            'price' => 'required|numeric',
            'meta.description' => 'nullable|string',
            'meta.key_benefits' => 'nullable|string',
            'meta.analytics' => 'nullable|string',
            'video_url' => 'nullable|url',
            'status' => 'nullable|in:active,inactive',
            'activate_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:activate_at'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['success' => 422, 'message' => $validator->errors(), 'data' => '']);
        }

        $metaData = $request->meta ?? [];
        if (!empty($metaData['key_benefits'])) 
        {
            $metaData['key_benefits'] = nl2br($metaData['key_benefits']);
        }
        if (!empty($metaData['analytics'])) 
        {
            $metaData['analytics'] = nl2br($metaData['analytics']);
        }

        DB::beginTransaction();
        try 
        {
            $id = DB::table('software_features')->insertGetId([
                'software_name' => $software,
                'feature_name' => $request->feature_name,
                'price' => $request->price,
                'meta' => json_encode($metaData, JSON_UNESCAPED_UNICODE),
                'status' => $request->status ?? 'inactive',
                'activate_at' => $request->activate_at,
                'expires_at' => $request->expires_at,
                'video_url' => $request->video_url,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $feature = DB::table('software_features')->where('id', $id)->first();
            DB::commit();

            return response()->json(['success' => 200, 'message' => 'Feature created', 'data' => $feature]);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success' => 500, 'message' => 'Failed to create feature', 'error' => $e->getMessage()]);
        }
    }

    public function updateFeature(Request $request, $id)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success' => 404, 'message' => 'Invalid software', 'data' => '']);
        }

        $feature = DB::table('software_features')->where('id', $id)->where('software_name', $software)->first();
        if (!$feature) 
        {
            return response()->json(['success' => 404, 'message' => 'Feature not found', 'data' => '']);
        }

        $validator = Validator::make($request->all(), [
            'feature_name' => 'required|string|unique:software_features,feature_name,' . $id,
            'price' => 'required|numeric',
            'meta.description' => 'nullable|string',
            'meta.key_benefits' => 'nullable|string',
            'meta.analytics' => 'nullable|string',
            'video_url' => 'nullable|url',
            'status' => 'nullable|in:active,inactive',
            'activate_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:activate_at'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['success' => 422, 'message' => $validator->errors(), 'data' => '']);
        }

        $metaData = $request->meta ?? json_decode($feature->meta, true) ?? [];

        if (!empty($metaData['key_benefits'])) 
        {
            $metaData['key_benefits'] = nl2br($metaData['key_benefits']);
        }
        if (!empty($metaData['analytics'])) 
        {
            $metaData['analytics'] = nl2br($metaData['analytics']);
        }

        DB::beginTransaction();
        try 
        {
            DB::table('software_features')->where('id', $id)->update([
                'feature_name' => $request->feature_name,
                'price' => $request->price,
                'meta' => json_encode($metaData, JSON_UNESCAPED_UNICODE),
                'status' => $request->status ?? $feature->status,
                'activate_at' => $request->activate_at,
                'expires_at' => $request->expires_at,
                'video_url' => $request->video_url,
                'updated_at' => now()
            ]);

            $updatedFeature = DB::table('software_features')->where('id', $id)->first();
            DB::commit();

            return response()->json(['success' => 200, 'message' => 'Feature updated', 'data' => $updatedFeature]);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success' => 500, 'message' => 'Failed to update feature', 'error' => $e->getMessage()]);
        }
    }

    public function deleteFeature(Request $request, $id)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success' => 404, 'message' => 'Invalid software', 'data' => '']);
        }

        $feature = DB::table('software_features')->where('id', $id)->where('software_name', $software)->first();
        if (!$feature) return response()->json(['success' => 404, 'message' => 'Feature not found', 'data' => '']);

        DB::beginTransaction();
        try 
        {
            DB::table('software_features')->where('id', $id)->delete();
            DB::commit();
            return response()->json(['success' => 200, 'message' => 'Feature deleted', 'data' => '']);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success' => 500, 'message' => 'Failed to delete feature', 'error' => $e->getMessage()]);
        }
    }

    public function getTrials(Request $request)
    {
        $software = $request->query('software');
        if(!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $trials = DB::table('trials')->where('software_name', $software)->get();
        return response()->json(['success'=>200,'message'=>'Trials fetched','data'=>$trials]);
    }

    public function getTrial(Request $request, $id)
    {
        $software = $request->query('software');
        if(!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $trial = DB::table('trials')->where('id', $id)->where('software_name', $software)->first();
        if(!$trial) return response()->json(['success'=>404,'message'=>'Trial not found','data'=>'']);

        return response()->json(['success'=>200,'message'=>'Trial fetched','data'=>$trial]);
    }

    public function createTrial(Request $request)
    {
        $software = $request->query('software');
        if(!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $validator = Validator::make($request->all(), [
            'feature_id' => 'required|integer|exists:software_features,id', 
            'client_name'=>'required|string',
            'email'=>'required|email',
            'phone'=>'required|string',
            'start_date'=>'required|date',
            'end_date'=>'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:active,inactive,expired,cancelled'
        ]);

        if($validator->fails()) return response()->json(['success'=>422,'message'=>$validator->errors(),'data'=>'']);

        DB::beginTransaction();
        try 
        {
            $id = DB::table('trials')->insertGetId([
                'software_name'=>$software,
                'feature_id'=>$request->feature_id,
                'client_name'=>$request->client_name,
                'email'=>$request->email,
                'phone'=>$request->phone,
                'start_date'=>$request->start_date,
                'end_date'=>$request->end_date,
                'status'=>$request->status ?? 'active',
                'created_at'=>now(),
                'updated_at'=>now()
            ]);

            $trial = DB::table('trials')->where('id', $id)->first();
            DB::commit();
            return response()->json(['success'=>200,'message'=>'Trial created','data'=>$trial]);
        } 
        catch(\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success'=>500,'message'=>'Failed to create trial','error'=>$e->getMessage()]);
        }
    }

    public function updateTrial(Request $request, $id)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success' => 404, 'message' => 'Invalid software', 'data' => '']);
        }

        $trial = DB::table('trials')->where('id', $id)->where('software_name', $software)->first();
        if (!$trial) 
        {
            return response()->json(['success' => 404, 'message' => 'Trial not found', 'data' => '']);
        }

        $validator = Validator::make($request->all(), [
            'feature_id' => 'required|integer|exists:software_features,id',
            'client_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:active,inactive,expired,cancelled'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['success' => 422, 'message' => $validator->errors(), 'data' => '']);
        }

        DB::beginTransaction();
        try 
        {
            DB::table('trials')->where('id', $id)->update([
                'feature_id' => $request->feature_id,
                'client_name' => $request->client_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status ?? $trial->status,
                'updated_at' => now()
            ]);
            if ($request->status === 'active') 
            {
                DB::table('software_features')
                    ->where('id', $request->feature_id)
                    ->update([
                        'status' => 'active',
                        'activate_at' => now(),
                        'expires_at' => $request->end_date,
                        'updated_at' => now()
                    ]);
            }

            $updatedTrial = DB::table('trials')->where('id', $id)->first();
            DB::commit();

            return response()->json(['success' => 200, 'message' => 'Trial updated successfully', 'data' => $updatedTrial]);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success' => 500, 'message' => 'Failed to update trial', 'error' => $e->getMessage()]);
        }
    }

    public function deleteTrial(Request $request, $id)
    {
        $software = $request->query('software');
        if(!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $trial = DB::table('trials')->where('id', $id)->where('software_name', $software)->first();
        if(!$trial) return response()->json(['success'=>404,'message'=>'Trial not found','data'=>'']);
 
        DB::beginTransaction();
        try 
        {
            DB::table('trials')->where('id', $id)->delete();
            DB::commit();
            return response()->json(['success'=>200,'message'=>'Trial deleted','data'=>'']);
        } 
        catch(\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success'=>500,'message'=>'Failed to delete trial','error'=>$e->getMessage()]);
        }
    }

    public function getFaqs(Request $request)
    {
        $software = $request->query('software');
        if(!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $faqs = DB::table('faqs')->where('software_name', $software)->get();
        return response()->json(['success'=>200,'message'=>'FAQs fetched','data'=>$faqs]);
    }

    public function getFaq(Request $request, $id)
    {
        $software = $request->query('software');
        if(!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $faq = DB::table('faqs')->where('id', $id)->where('software_name', $software)->first();
        if(!$faq) return response()->json(['success'=>404,'message'=>'FAQ not found','data'=>'']);

        return response()->json(['success'=>200,'message'=>'FAQ fetched','data'=>$faq]);
    }

    public function createFaq(Request $request)
    {
        $software = $request->query('software');
        if(!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $validator = Validator::make($request->all(), [
            'question'=>'required|string',
            'answer'=>'required|string'
        ]);

        if($validator->fails()) return response()->json(['success'=>422,'message'=>$validator->errors(),'data'=>'']);

        DB::beginTransaction();
        try 
        {
            $id = DB::table('faqs')->insertGetId([
                'software_name'=>$software,
                'question'=>$request->question,
                'answer'=>$request->answer,
                'created_at'=>now(),
                'updated_at'=>now()
            ]);

            $faq = DB::table('faqs')->where('id', $id)->first();
            DB::commit();
            return response()->json(['success'=>200,'message'=>'FAQ created','data'=>$faq]);
        } 
        catch(\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success'=>500,'message'=>'Failed to create FAQ','error'=>$e->getMessage()]);
        }
    }

    public function updateFaq(Request $request, $id)
    {
        $software = $request->query('software');
        if(!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $faq = DB::table('faqs')->where('id', $id)->where('software_name', $software)->first();
        if(!$faq) return response()->json(['success'=>404,'message'=>'FAQ not found','data'=>'']);

        $validator = Validator::make($request->all(), [
            'question'=>'required|string',
            'answer'=>'required|string'
        ]);

        if($validator->fails()) return response()->json(['success'=>422,'message'=>$validator->errors(),'data'=>'']);

        DB::beginTransaction();
        try 
        {
            DB::table('faqs')->where('id', $id)->update([
                'question'=>$request->question,
                'answer'=>$request->answer,
                'updated_at'=>now()
            ]);

            $updatedFaq = DB::table('faqs')->where('id', $id)->first();
            DB::commit();
            return response()->json(['success'=>200,'message'=>'FAQ updated','data'=>$updatedFaq]);
        } 
        catch(\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success'=>500,'message'=>'Failed to update FAQ','error'=>$e->getMessage()]);
        }
    }

    public function deleteFaq(Request $request, $id)
    {
        $software = $request->query('software');
        if(!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $faq = DB::table('faqs')->where('id', $id)->where('software_name', $software)->first();
        if(!$faq) return response()->json(['success'=>404,'message'=>'FAQ not found','data'=>'']);

        DB::beginTransaction();
        try 
        {
            DB::table('faqs')->where('id', $id)->delete();
            DB::commit();
            return response()->json(['success'=>200,'message'=>'FAQ deleted','data'=>'']);
        } 
        catch(\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success'=>500,'message'=>'Failed to delete FAQ','error'=>$e->getMessage()]);
        }
    }
    public function getAdvertisements(Request $request)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $ads = DB::table('advertisements')->where('software_name', $software)->get();
        return response()->json(['success'=>200,'message'=>'Advertisements fetched','data'=>$ads]);
    }

    public function getAdvertisement(Request $request, $id)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $ad = DB::table('advertisements')->where('id', $id)->where('software_name', $software)->first();
        if (!$ad) return response()->json(['success'=>404,'message'=>'Advertisement not found','data'=>'']);

        return response()->json(['success'=>200,'message'=>'Advertisement fetched','data'=>$ad]);
    }

    public function createAdvertisement(Request $request)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }
        $existingAd = DB::table('advertisements')->where('software_name', $software)->first();
        if ($existingAd) 
        {
            return response()->json([
                'success' => 409,
                'message' => 'Advertisement already exists for this software. Only one allowed.',
                'data' => $existingAd
            ]);
        }

        $validator = Validator::make($request->all(), [
            'title'=>'required|string',
            'subtitle'=>'nullable|string',
            'badge_text'=>'nullable|string',
            'media'=>'nullable|string',
            'description'=>'nullable|string',
            'features'=>'nullable|string',
            'pricing'=>'nullable|string',
            'button'=>'nullable|string',
            'footer_note'=>'nullable|string',
            'is_active'=>'nullable|in:0,1',
            'start_date'=>'nullable|date',
            'end_date'=>'nullable|date|after_or_equal:start_date'
        ]);

        if($validator->fails()) return response()->json(['success'=>422,'message'=>$validator->errors(),'data'=>'']);

        DB::beginTransaction();
        try
        {
            $id = DB::table('advertisements')->insertGetId([
                'software_name'=>$software,
                'title'=>$request->title,
                'subtitle'=>$request->subtitle,
                'badge_text'=>$request->badge_text,
                'media'=>$request->media,
                'description'=>$request->description,
                'features'=>$request->features,
                'pricing'=>$request->pricing,
                'button'=>$request->button,
                'footer_note'=>$request->footer_note,
                'is_active'=>$request->is_active ?? 1,
                'start_date'=>$request->start_date,
                'end_date'=>$request->end_date,
                'created_at'=>now(),
                'updated_at'=>now()
            ]);

            $ad = DB::table('advertisements')->where('id', $id)->first();
            DB::commit();
            return response()->json(['success'=>200,'message'=>'Advertisement created','data'=>$ad]);
        } 
        catch (\Exception $e)
        {
            DB::rollBack();
            return response()->json(['success'=>500,'message'=>'Failed to create advertisement','error'=>$e->getMessage()]);
        }
    }

    public function updateAdvertisement(Request $request, $id)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $ad = DB::table('advertisements')->where('id', $id)->where('software_name', $software)->first();
        if (!$ad) return response()->json(['success'=>404,'message'=>'Advertisement not found','data'=>'']);

        $validator = Validator::make($request->all(), [
            'title'=>'required|string',
            'subtitle'=>'nullable|string',
            'badge_text'=>'nullable|string',
            'media'=>'nullable|string',
            'description'=>'nullable|string',
            'features'=>'nullable|string',
            'pricing'=>'nullable|string',
            'button'=>'nullable|string',
            'footer_note'=>'nullable|string',
            'is_active'=>'nullable|in:0,1',
            'start_date'=>'nullable|date',
            'end_date'=>'nullable|date|after_or_equal:start_date'
        ]);

        if($validator->fails()) return response()->json(['success'=>422,'message'=>$validator->errors(),'data'=>'']);

        DB::beginTransaction();
        try 
        {
            $updateData = [
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'badge_text' => $request->badge_text,
                'description' => $request->description,
                'features' => $request->features,
                'pricing' => $request->pricing,
                'button' => $request->button,
                'footer_note' => $request->footer_note,
                'is_active' => $request->is_active ?? $ad->is_active,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'updated_at' => now()
            ];
            $updateData['media'] = !empty(trim($request->media)) ? $request->media : null;

            DB::table('advertisements')->where('id', $id)->update($updateData);

            $updatedAd = DB::table('advertisements')->where('id', $id)->first();
            DB::commit();
            return response()->json(['success'=>200,'message'=>'Advertisement updated','data'=>$updatedAd]);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success'=>500,'message'=>'Failed to update advertisement','error'=>$e->getMessage()]);
        }
    }

    public function deleteAdvertisement(Request $request, $id)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        $ad = DB::table('advertisements')->where('id', $id)->where('software_name', $software)->first();
        if (!$ad) return response()->json(['success'=>404,'message'=>'Advertisement not found','data'=>'']);

        DB::beginTransaction();
        try 
        {
            DB::table('advertisements')->where('id', $id)->delete();
            DB::commit();
            return response()->json(['success'=>200,'message'=>'Advertisement deleted','data'=>'']);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success'=>500,'message'=>'Failed to delete advertisement','error'=>$e->getMessage()]);
        }
    }

    public function getTickets(Request $request)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        try 
        {
            $tickets = DB::table('support_tickets')
                ->where('software_name', $software)
                ->orderBy('created_at', 'desc')
                ->get();

            $tickets->transform(function($ticket) 
            {
                try 
                {
                    $attachments = $ticket->attachments ? json_decode($ticket->attachments, true) : [];
                    if (is_array($attachments)) 
                    {
                        $ticket->attachments = array_map(function($attachment) 
                        {
                            if (Str::startsWith($attachment, 'storage/')) 
                            {
                                return asset($attachment);
                            }
                            if (Str::startsWith($attachment, '/storage/')) 
                            {
                                return asset($attachment);
                            }
                            return $attachment;
                        }, $attachments);
                    } 
                    else 
                    {
                        $ticket->attachments = [];
                    }
                } 
                catch (\Exception $e) 
                {
                    $ticket->attachments = [];
                }
                return $ticket;
            });

            return response()->json([
                'success' => 200,
                'message' => 'Tickets fetched successfully',
                'data' => $tickets
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => 500,
                'message' => 'Failed to fetch tickets: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function getTicket(Request $request, $id)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        try 
        {
            $ticket = DB::table('support_tickets')
                ->where('id', $id)
                ->where('software_name', $software)
                ->first();

            if (!$ticket) 
            {
                return response()->json([
                    'success' => 404,
                    'message' => 'Ticket not found for this software',
                    'data' => []
                ]);
            }
            try 
            {
                $attachments = $ticket->attachments ? json_decode($ticket->attachments, true) : [];
                if (is_array($attachments)) 
                {
                    $ticket->attachments = array_map(function($attachment) 
                    {
                        if (Str::startsWith($attachment, 'storage/')) 
                        {
                            return asset($attachment);
                        }
                        if (Str::startsWith($attachment, '/storage/'))
                        {
                            return asset($attachment);
                        }
                        return $attachment;
                    }, $attachments);
                } 
                else 
                {
                    $ticket->attachments = [];
                }
            } 
            catch (\Exception $e) 
            {
                $ticket->attachments = [];
            }

            return response()->json([
                'success' => 200,
                'message' => 'Ticket fetched successfully',
                'data' => $ticket
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => 500,
                'message' => 'Failed to fetch ticket: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function updateTicket(Request $request, $id)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        try 
        {
            $ticket = DB::table('support_tickets')
                ->where('id', $id)
                ->where('software_name', $software)
                ->first();

            if (!$ticket) 
            {
                return response()->json([
                    'success' => 404,
                    'message' => 'Ticket not found',
                    'data' => []
                ]);
            }

            $validator = Validator::make($request->all(), [
                'remarks' => 'nullable|string',
                'status' => 'nullable|in:open,in_progress,resolved,closed'
            ]);

            if ($validator->fails()) 
            {
                return response()->json([
                    'success' => 422,
                    'message' => $validator->errors(),
                    'data' => []
                ]);
            }

            $updateData = [
                'updated_at' => now()
            ];

            if ($request->has('remarks')) 
            {
                $updateData['remarks'] = $request->remarks;
            }

            if ($request->has('status')) 
            {
                $updateData['status'] = $request->status;
            }

            DB::table('support_tickets')
                ->where('id', $id)
                ->update($updateData);

            $updatedTicket = DB::table('support_tickets')->where('id', $id)->first();
            try 
            {
                $attachments = $updatedTicket->attachments ? json_decode($updatedTicket->attachments, true) : [];
                if (is_array($attachments)) 
                {
                    $updatedTicket->attachments = array_map(function($attachment)
                    {
                        if (Str::startsWith($attachment, 'storage/')) 
                        {
                            return asset($attachment);
                        }
                        if (Str::startsWith($attachment, '/storage/')) 
                        {
                            return asset($attachment);
                        }
                        return $attachment;
                    }, $attachments);
                } 
                else 
                {
                    $updatedTicket->attachments = [];
                }
            } 
            catch (\Exception $e) 
            {
                $updatedTicket->attachments = [];
            }

            return response()->json([
                'success' => 200,
                'message' => 'Ticket updated successfully',
                'data' => $updatedTicket
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => 500,
                'message' => 'Failed to update ticket: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function reopenTicket(Request $request, $id)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        try 
        {
            $ticket = DB::table('support_tickets')
                ->where('id', $id)
                ->where('software_name', $software)
                ->first();

            if (!$ticket) 
            {
                return response()->json([
                    'success' => 404,
                    'message' => 'Ticket not found',
                    'data' => []
                ]);
            }
            DB::table('support_tickets')
                ->where('id', $id)
                ->update([
                    'status' => 'open',
                    'updated_at' => now()
                ]);

            $updatedTicket = DB::table('support_tickets')->where('id', $id)->first();

            return response()->json([
                'success' => 200,
                'message' => 'Ticket reopened successfully',
                'data' => $updatedTicket
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => 500,
                'message' => 'Failed to reopen ticket: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function exportTickets(Request $request)
    {
        $software = $request->query('software');
        if (!$software || !$this->softwareExists($software)) 
        {
            return response()->json(['success'=>404,'message'=>'Invalid software','data'=>'']);
        }

        try 
        {
            $tickets = DB::table('support_tickets')
                ->where('software_name', $software)
                ->orderBy('created_at', 'desc')
                ->get();

            $csv = "ID,Ticket ID,Subject,Description,Priority,Status,User ID,Software Name,Created At,Updated At\n";
            
            foreach ($tickets as $ticket) 
            {
                $subject = str_replace('"', '""', $ticket->subject ?? 'No Subject');
                $description = str_replace('"', '""', $ticket->description ?? 'No Description');
                $priority = $ticket->priority ?? 'Normal';
                $status = $ticket->status ?? 'Open';
                $userId = $ticket->user_id ?? 'Guest';
                $softwareName = $ticket->software_name ?? 'Unknown';
                $createdAt = $ticket->created_at ?? 'Unknown';
                $updatedAt = $ticket->updated_at ?? 'Unknown';
                
                $csv .= "{$ticket->id},{$ticket->ticket_id},\"{$subject}\",\"{$description}\",{$priority},{$status},{$userId},{$softwareName},{$createdAt},{$updatedAt}\n";
            }

            $filename = "support_tickets_" . date('Y-m-d_H-i-s') . ".csv";
            
            return response($csv, 200)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => 500,
                'message' => 'Failed to export tickets: ' . $e->getMessage()
            ]);
        }
    }

    public function uploadApk(Request $request, $softwareName)
    {
        $validator = Validator::make($request->all(), [
            'apk_file' => [
                'required',
                'file',
                'max:102400',
                function ($attribute, $value, $fail) 
                {
                    $allowedExtensions = ['apk', 'zip'];
                    $extension = strtolower($value->getClientOriginalExtension());
                    
                    if (!in_array($extension, $allowedExtensions)) 
                    {
                        $fail('The APK file must be a file of type: ' . implode(', ', $allowedExtensions));
                    }
                }
            ],
            'version' => 'nullable|string|max:50',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'success' => 422,
                'message' => $validator->errors(),
                'data' => ''
            ]);
        }

        DB::beginTransaction();
        try 
        {
            $software = DB::table('software_details')
                ->where('software_name', $softwareName)
                ->first();

            if (!$software) 
            {
                return response()->json([
                    'success' => 404,
                    'message' => 'Software not found: ' . $softwareName,
                    'data' => ''
                ]);
            }
            if ($software->apk) 
            {
                $oldApkPath = public_path($software->apk);
                if (file_exists($oldApkPath)) 
                {
                    unlink($oldApkPath);
                }
            }
            $file = $request->file('apk_file');
            $fileName = 'apk_' . time() . '_' . str_replace(' ', '_', $softwareName) . '_v' . $request->version . '.apk';
            $filePath = 'apk_files/' . $fileName;
            $file->move(public_path('apk_files'), $fileName);
            DB::table('software_details')
                ->where('software_name', $softwareName)
                ->update([
                    'apk' => $filePath,
                    'version' => $request->version,
                    'description' => $request->description ?? $software->description,
                    'updated_at' => now()
                ]);

            DB::commit();

            $updatedSoftware = DB::table('software_details')
                ->where('software_name', $softwareName)
                ->first();

            return response()->json([
                'success' => 200,
                'message' => 'APK uploaded successfully',
                'data' => $updatedSoftware
            ]);

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => 500,
                'message' => 'Failed to upload APK' . $e->getMessage(),
                'error' => $e->getMessage()
            ]);
        }
    }

    public function deleteApk(Request $request, $softwareName)
    {
        DB::beginTransaction();
        try 
        {
            $software = DB::table('software_details')
                ->where('software_name', $softwareName)
                ->first();
            
            if (!$software) 
            {
                return response()->json([
                    'success' => 404,
                    'message' => 'Software not found: ' . $softwareName,
                    'data' => ''
                ]);
            }

            if (!$software->apk) 
            {
                return response()->json([
                    'success' => 404,
                    'message' => 'No APK file found',
                    'data' => ''
                ]);
            }

            $apkPath = public_path($software->apk);
            if (file_exists($apkPath)) 
            {
                unlink($apkPath);
            }

            DB::table('software_details')
                ->where('software_name', $softwareName)
                ->update([
                    'apk' => null,
                    'version' => null,
                    'updated_at' => now()
                ]);

            DB::commit();

            $updatedSoftware = DB::table('software_details')
                ->where('software_name', $softwareName)
                ->first();

            return response()->json([
                'success' => 200,
                'message' => 'APK deleted successfully',
                'data' => $updatedSoftware
            ]);

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => 500,
                'message' => 'Failed to delete APK',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function downloadApk(Request $request, $softwareName)
    {
        try 
        {
            $software = DB::table('software_details')
                ->where('software_name', $softwareName)
                ->first();
            
            if (!$software) 
            {
                return response()->json([
                    'success' => 404,
                    'message' => 'Software not found: ' . $softwareName,
                    'data' => ''
                ]);
            }

            if (!$software->apk) 
            {
                return response()->json([
                    'success' => 404,
                    'message' => 'No APK file available',
                    'data' => ''
                ]);
            }

            $apkPath = public_path($software->apk);
            
            if (!file_exists($apkPath)) 
            {
                return response()->json([
                    'success' => 404,
                    'message' => 'APK file not found on server',
                    'data' => ''
                ]);
            }

            return response()->json([
                'success' => 200,
                'message' => 'APK file available',
                'data' => [
                    'file_path' => asset($software->apk),
                    'file_name' => basename($software->apk),
                    'software_name' => $software->software_name,
                    'version' => $software->version
                ]
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => 500,
                'message' => 'Failed to download APK',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getApkInfo(Request $request, $softwareName)
    {
        try 
        {
            $software = DB::table('software_details')
                ->where('software_name', $softwareName)
                ->first();
            
            if (!$software) 
            {
                return response()->json([
                    'success' => 404,
                    'message' => 'Software not found: ' . $softwareName,
                    'data' => ''
                ]);
            }

            $apkInfo = [
                'has_apk' => !empty($software->apk),
                'apk_path' => $software->apk ? asset($software->apk) : null,
                'version' => $software->version,
                'file_name' => $software->apk ? basename($software->apk) : null,
                'file_size' => $software->apk ? $this->getFileSize(public_path($software->apk)) : null,
                'uploaded_at' => $software->updated_at
            ];

            return response()->json([
                'success' => 200,
                'message' => 'APK info fetched',
                'data' => $apkInfo
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => 500,
                'message' => 'Failed to fetch APK info',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getFileSize($filePath)
    {
        if (!file_exists($filePath))
        {
            return null;
        }

        $bytes = filesize($filePath);
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) 
        {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getSoftwareInfo(Request $request)
    {
        try 
        {
            $software = $request->query('software');
            
            if (!$software) 
            {
                return response()->json([
                    'success' => 400,
                    'message' => 'Software name is required',
                    'data' => ''
                ]);
            }

            $softwareInfo = DB::table('software_details')
                ->where('software_name', $software)
                ->first();

            if (!$softwareInfo) 
            {
                return response()->json([
                    'success' => 404,
                    'message' => 'Software not found in details: ' . $software,
                    'data' => ''
                ]);
            }

            $responseData = [
                'id' => $softwareInfo->id,
                'software_name' => $softwareInfo->software_name,
                'software_type' => $softwareInfo->software_type,
                'client_name' => $softwareInfo->client_name,
                'description' => $softwareInfo->description,
                'version' => $softwareInfo->version,
                'status' => $softwareInfo->status,
                'user_limit' => $softwareInfo->user_limit,
                'created_at' => $softwareInfo->created_at,
                'updated_at' => $softwareInfo->updated_at
            ];

            return response()->json([
                'success' => 200,
                'message' => 'Software information fetched successfully',
                'data' => $responseData
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => 500,
                'message' => 'Failed to fetch software information',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function updateSoftwareType(Request $request)
    {
        try 
        {
            $software = $request->query('software');
            
            if (!$software) 
            {
                return response()->json([
                    'success' => 400,
                    'message' => 'Software name is required',
                    'data' => ''
                ]);
            }

            $existingSoftware = DB::table('software_details')
                ->where('software_name', $software)
                ->first();

            if (!$existingSoftware) 
            {
                return response()->json([
                    'success' => 404,
                    'message' => 'Software not found: ' . $software,
                    'data' => ''
                ]);
            }
            $validator = Validator::make($request->all(), [
                'software_type' => 'nullable|string|in:real_state,lead_management,task_management,mis_management',
                'user_limit' => 'nullable|min:1',
            ]);

            if ($validator->fails()) 
            {
                return response()->json([
                    'success' => 422,
                    'message' => $validator->errors(),
                    'data' => ''
                ]);
            }
            if (is_null($request->software_type) && is_null($request->user_limit)) 
            {
                return response()->json([
                    'success' => 422,
                    'message' => 'Please provide at least one value to update: software_type or user_limit',
                    'data' => ''
                ]);
            }
            $softwareType = $request->software_type ?? $existingSoftware->software_type;
            $userLimit = $request->user_limit ?? $existingSoftware->user_limit;

            DB::beginTransaction();
            try 
            {
                DB::table('software_details')
                    ->where('software_name', $software)
                    ->update([
                        'software_type' => $softwareType,
                        'user_limit' => $userLimit,
                        'updated_at' => now()
                    ]);

                DB::commit();

                return response()->json([
                    'success' => 200,
                    'message' => 'Software updated successfully',
                    'data' => [
                        'software_name' => $software,
                        'software_type' => $softwareType,
                        'user_limit' => $userLimit,
                        'updated_at' => now()->toDateTimeString()
                    ]
                ]);

            } 
            catch (\Exception $e) 
            {
                DB::rollBack();
                return response()->json([
                    'success' => 500,
                    'message' => 'Failed to update software',
                    'error' => $e->getMessage()
                ]);
            }

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => 500,
                'message' => 'Failed to update software',
                'error' => $e->getMessage()
            ]);
        }
    }
}