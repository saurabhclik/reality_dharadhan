<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index()
    {
        $userType = Session::get('user_type');
        $userId = Session::get('user_id');
        $userName = Session::get('user_name');
        $projects = DB::table('project_inventories')->paginate(10);
        $category = DB::table('category')->get();
        return view('project.index', compact('projects', 'userType', 'userName', 'category'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:Residential,Commercial',
            'category_id' => 'required|exists:inv_catg,id',
            'sub_category_id' => 'required|exists:inv_subcatg,id',
            'project_name' => 'required|string|max:255',
            'description' => 'required|string|min:160',
            'full_address' => 'required|string',
            'price' => 'required|numeric',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'brochure' => 'nullable|mimes:pdf,jpeg,png,jpg|max:1024',
            'floor_plan' => 'nullable|mimes:pdf,jpeg,png,jpg|max:1024',
            'site_map' => 'nullable|mimes:pdf,jpeg,png,jpg|max:1024',
            'price_list' => 'nullable|mimes:pdf,jpeg,png,jpg|max:1024',
            'instagram_link' => 'nullable|url',
            'facebook_link' => 'nullable|url',
            'twitter_link' => 'nullable|url',
            'linkedin_link' => 'nullable|url',
            'contact_number_1' => 'nullable|string|max:20',
            'contact_number_2' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'form_fields' => 'nullable|array',
            'form_fields.*' => 'in:name,budget,email,phone,address,state,city',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try 
        {
            $styleSettings = $request->input('style_settings', [
                'primary_color' => '#1a1a1a',
                'secondary_color' => '#2d2d2d',
                'accent_color' => '#c8a97e',
                'text_color' => '#f8f8f8',
                'text_secondary' => '#cccccc',
                'font_heading' => 'DM Serif Display',
                'font_body' => 'Montserrat',
                'button_style' => 'rounded',
                'nav_style' => 'solid',
                'card_radius' => '12',
                'banner_size' => 'cover',
                'banner_position' => 'center',
                'banner_repeat' => 'no-repeat',
                'thumb_width' => '300',
                'thumb_height' => '200',
                'thumb_crop' => 'fill',
                'thumb_quality' => '80',
                'custom_css' => ''
            ]);

            $nearbyLocations = [];
            if ($request->nearby_locations) 
            {
                foreach ($request->nearby_locations as $location) 
                {
                    if (!empty($location['building']) && !empty($location['location'])) 
                    {
                        $nearbyLocations[] = $location;
                    }
                }
            }

            $specifications = [
                'beds' => $request->beds,
                'baths' => $request->baths,
                'balcony' => $request->balcony,
                'terrace' => $request->terrace,
                'super_carpet' => $request->super_carpet,
                'carpet_lobby' => $request->carpet_lobby,
                'size' => $request->size,
            ];

            $amenities = $request->amenities ?? [];
            $customAmenities = $request->custom_amenities ?? [];
            $allAmenities = array_unique(array_filter(array_merge($amenities, $customAmenities)));
            $formFields = $request->form_fields ?? [];
            if (!in_array('name', $formFields)) 
            {
                $formFields[] = 'name';
            }
            if (!in_array('phone', $formFields)) 
            {
                $formFields[] = 'phone';
            }
            $formFields = array_unique($formFields); 

            $data = [
                'type' => $request->type,
                'category' => $request->category_id,
                'sub_category' => $request->sub_category_id,
                'name' => $request->project_name,
                'slug' => Str::slug($request->project_name),
                'user_id' => Session::get('user_id'),
                'description' => $request->description,
                'short_description' => $request->short_description,
                'location' => $request->full_address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country ?? 'India',
                'pin_code' => $request->pin_code,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'price' => $request->price,
                'price_unit' => $request->price_unit ?? 'sqft',
                'price_display' => $request->price_display,
                'specifications' => json_encode($specifications),
                'amenities' => json_encode($allAmenities),
                'nearby_locations' => json_encode($nearbyLocations),
                'video_link' => $request->video_link,
                'contact_number_1' => $request->contact_number_1,
                'contact_number_2' => $request->contact_number_2,
                'whatsapp_number' => $request->whatsapp_number,
                'email' => $request->email,
                'instagram_link' => $request->instagram_link,
                'facebook_link' => $request->facebook_link,
                'twitter_link' => $request->twitter_link,
                'linkedin_link' => $request->linkedin_link,
                'style_settings' => json_encode($styleSettings),
                'form_fields' => json_encode($formFields),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ];

            $fileFields = [
                'logo' => 'logo_path',
                'cover_image' => 'cover_image_path',
                'brochure' => 'brochure_path',
                'floor_plan' => 'floor_plan_path',
                'site_map' => 'site_map_path',
                'price_list' => 'price_list_path'
            ];

            foreach ($fileFields as $field => $dbField) 
            {
                if ($request->hasFile($field)) 
                {
                    $path = $request->file($field)->store('project_files', 'public');
                    $data[$dbField] = $path;
                }
            }

            $projectId = DB::table('project_inventories')->insertGetId($data);

            if ($request->hasFile('gallery_images')) 
            {
                $galleryPaths = [];
                foreach ($request->file('gallery_images') as $file) 
                {
                    $path = $file->store('project_gallery', 'public');
                    $galleryPaths[] = $path;
                }

                DB::table('project_inventories')
                    ->where('id', $projectId)
                    ->update(['gallery_images' => json_encode($galleryPaths)]);
            }

            return response()->json(['success' => true, 'message' => 'Project added successfully']);
        } 
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $project = DB::table('project_inventories')->where('id', $id)->first();
        if (!$project) 
        {
            return response()->json(['success' => false, 'message' => 'Project not found'], 404);
        }
        return response()->json([
            'success' => true,
            'project' => $project
        ]);
    }

    public function edit($id)
    {
        $project = DB::table('project_inventories')->where('id', $id)->first();
        if (!$project) 
        {
            return response()->json(['success' => false, 'message' => 'Project not found'], 404);
        }
        return response()->json([
            'success' => true,
            'project' => $project
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:Residential,Commercial',
            'category_id' => 'required|exists:inv_catg,id',
            'sub_category_id' => 'required|exists:inv_subcatg,id',
            'project_name' => 'required|string|max:255',
            'description' => 'required|string|min:160',
            'full_address' => 'required|string',
            'price' => 'required|numeric',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'brochure' => 'nullable|mimes:pdf,jpeg,png,jpg|max:1024',
            'floor_plan' => 'nullable|mimes:pdf,jpeg,png,jpg|max:1024',
            'site_map' => 'nullable|mimes:pdf,jpeg,png,jpg|max:1024',
            'price_list' => 'nullable|mimes:pdf,jpeg,png,jpg|max:1024',
            'instagram_link' => 'nullable|url',
            'facebook_link' => 'nullable|url',
            'twitter_link' => 'nullable|url',
            'linkedin_link' => 'nullable|url',
            'contact_number_1' => 'nullable|string|max:20',
            'contact_number_2' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'form_fields' => 'nullable|array',
            'form_fields.*' => 'in:name,budget,email,phone,address,state,city',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try 
        {
            $styleSettings = $request->input('style_settings', [
                'primary_color' => '#1a1a1a',
                'secondary_color' => '#2d2d2d',
                'accent_color' => '#c8a97e',
                'text_color' => '#f8f8f8',
                'text_secondary' => '#cccccc',
                'font_heading' => 'DM Serif Display',
                'font_body' => 'Montserrat',
                'button_style' => 'rounded',
                'nav_style' => 'solid',
                'card_radius' => '12',
                'banner_size' => 'cover',
                'banner_position' => 'center',
                'banner_repeat' => 'no-repeat',
                'thumb_width' => '300',
                'thumb_height' => '200',
                'thumb_crop' => 'fill',
                'thumb_quality' => '80',
                'custom_css' => ''
            ]);

            $nearbyLocations = [];
            if ($request->nearby_locations) 
            {
                foreach ($request->nearby_locations as $location) 
                {
                    if (!empty($location['building']) && !empty($location['location'])) 
                    {
                        $nearbyLocations[] = $location;
                    }
                }
            }

            $specifications = [
                'beds' => $request->beds,
                'baths' => $request->baths,
                'balcony' => $request->balcony,
                'terrace' => $request->terrace,
                'super_carpet' => $request->super_carpet,
                'carpet_lobby' => $request->carpet_lobby,
                'size' => $request->size,
            ];

            $amenities = $request->amenities ?? [];
            $customAmenities = $request->custom_amenities ?? [];
            $allAmenities = array_unique(array_filter(array_merge($amenities, $customAmenities)));
            $formFields = $request->form_fields ?? [];
            if (!in_array('name', $formFields)) 
            {
                $formFields[] = 'name';
            }
            if (!in_array('phone', $formFields)) 
            {
                $formFields[] = 'phone';
            }
            $formFields = array_unique($formFields);

            $data = [
                'type' => $request->type,
                'category' => $request->category_id,
                'sub_category' => $request->sub_category_id,
                'name' => $request->project_name,
                'slug' => Str::slug($request->project_name),
                'user_id' => Session::get('user_id'),
                'description' => $request->description,
                'short_description' => $request->short_description,
                'location' => $request->full_address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country ?? 'India',
                'pin_code' => $request->pin_code,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'price' => $request->price,
                'price_unit' => $request->price_unit ?? 'sqft',
                'price_display' => $request->price_display,
                'specifications' => json_encode($specifications),
                'amenities' => json_encode($allAmenities),
                'nearby_locations' => json_encode($nearbyLocations),
                'video_link' => $request->video_link,
                'contact_number_1' => $request->contact_number_1,
                'contact_number_2' => $request->contact_number_2,
                'whatsapp_number' => $request->whatsapp_number,
                'email' => $request->email,
                'instagram_link' => $request->instagram_link,
                'facebook_link' => $request->facebook_link,
                'twitter_link' => $request->twitter_link,
                'linkedin_link' => $request->linkedin_link,
                'style_settings' => json_encode($styleSettings),
                'form_fields' => json_encode($formFields),
                'status' => 'active',
                'updated_at' => now()
            ];

            $fileFields = [
                'logo' => 'logo_path',
                'cover_image' => 'cover_image_path',
                'brochure' => 'brochure_path',
                'floor_plan' => 'floor_plan_path',
                'site_map' => 'site_map_path',
                'price_list' => 'price_list_path'
            ];

            foreach ($fileFields as $field => $dbField) 
            {
                if ($request->hasFile($field)) 
                {
                    $path = $request->file($field)->store('project_files', 'public');
                    $data[$dbField] = $path;
                }
            }

            DB::table('project_inventories')->where('id', $id)->update($data);

            if ($request->hasFile('gallery_images')) 
            {
                $galleryPaths = [];
                foreach ($request->file('gallery_images') as $file) 
                {
                    $path = $file->store('project_gallery', 'public');
                    $galleryPaths[] = $path;
                }

                DB::table('project_inventories')
                    ->where('id', $id)
                    ->update(['gallery_images' => json_encode($galleryPaths)]);
            }

            return response()->json(['success' => true, 'message' => 'Project updated successfully']);
        } 
        catch (\Exception $e) 
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try 
        {
            $project = DB::table('project_inventories')->where('id', $id)->first();
            if (!$project) 
            {
                return response()->json(['success' => false, 'message' => 'Project not found'], 404);
            }
            $fileFields = [
                'logo_path', 'cover_image_path', 'brochure_path',
                'floor_plan_path', 'site_map_path', 'price_list_path'
            ];

            foreach ($fileFields as $field) 
            {
                if ($project->{$field}) 
                {
                    Storage::disk('public')->delete($project->{$field});
                }
            }

            if ($project->gallery_images) 
            {
                $galleryImages = json_decode($project->gallery_images, true);
                foreach ($galleryImages as $image) 
                {
                    Storage::disk('public')->delete($image);
                }
            }

            DB::table('project_inventories')->where('id', $id)->delete();

            return response()->json(['success' => true, 'message' => 'Project deleted successfully']);
        } 
        catch (\Exception $e) 
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getCategories($type)
    {
        $categories = DB::table('inv_catg')->where('type', $type)->get();
        return response()->json($categories);
    }

    public function getSubcategories($categoryId)
    {
        $subcategories = DB::table('inv_subcatg')->where('catg_id', $categoryId)->get();
        return response()->json($subcategories);
    }

    public function showPublic($slug)
    {
        $project = DB::table('project_inventories')
            ->leftJoin('inv_catg', 'project_inventories.category', '=', 'inv_catg.id')
            ->leftJoin('inv_subcatg', 'project_inventories.sub_category', '=', 'inv_subcatg.id')
            ->where('project_inventories.slug', $slug)
            ->select(
                'project_inventories.*',
                'inv_catg.name as category_name',
                'inv_subcatg.name as sub_category_name'
            )
            ->first();

        if (!$project) 
        {
            abort(404);
        }

        $formFields = json_decode($project->form_fields, true) ?? [];

        $styleSettings = array_merge([
            'primary_color' => '#1a1a1a',
            'secondary_color' => '#2d2d2d',
            'accent_color' => '#c8a97e',
            'text_color' => '#f8f8f8',
            'text_secondary' => '#cccccc',
            'font_heading' => 'DM Serif Display',
            'font_body' => 'Montserrat',
            'button_style' => 'rounded',
            'nav_style' => 'solid',
            'card_radius' => '12',
            'banner_size' => 'cover',
            'banner_position' => 'center',
            'banner_repeat' => 'no-repeat',
            'thumb_width' => '300',
            'thumb_height' => '200',
            'thumb_crop' => 'fill',
            'thumb_quality' => '80',
            'custom_css' => ''
        ], json_decode($project->style_settings, true) ?? []);

        $inquiryQuestions = DB::table('inquiry_questions')->where('is_active', 1)->get();
        $states = DB::table('state_district')->select('state')->distinct()->orderBy('state', 'asc')->get();
        return view('project.show', [
            'project' => $project,
            'styleSettings' => $styleSettings,
            'inquiryQuestions' => $inquiryQuestions,
            'states' => $states,
            'formFields' => $formFields,
        ]);
    }

    public function removeFile(Request $request, $id)
    {
        try 
        {
            $project = DB::table('project_inventories')->where('id', $id)->first();
            if (!$project) 
            {
                return response()->json(['success' => false, 'message' => 'Project not found'], 404);
            }

            $field = $request->field;
            $path = $request->path;

            if ($project->{$field} !== $path) 
            {
                return response()->json(['success' => false, 'message' => 'File not associated with this project'], 400);
            }

            Storage::disk('public')->delete($path);
            DB::table('project_inventories')->where('id', $id)->update([$field => null]);

            return response()->json(['success' => true, 'message' => 'File deleted successfully']);
        } 
        catch (\Exception $e) 
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}