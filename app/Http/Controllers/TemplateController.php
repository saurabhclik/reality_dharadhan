<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Flasher\Laravel\Facade\Flasher;

class TemplateController extends Controller
{
    public function create(Request $request)
    {
        try 
        {
            $channel = $request->query('channel');
            
            if (!$channel) 
            {
                return view('messaging.select-channel');
            }
            if (!in_array($channel, ['whatsapp', 'email', 'sms'])) 
            {
                Flasher::addError('Invalid channel selected');
                return redirect()->route('messaging.create-template');
            }
            
            return view('messaging.create-template', compact('channel'));
            
        } 
        catch (\Exception $e) 
        {
            Flasher::addError('Error loading template creation form');
            return redirect()->route('messaging.index');
        }
    }

    public function store(Request $request)
    {
        try 
        {
            $validationRules = [
                'channel' => 'required|in:whatsapp,email,sms',
                'name' => 'required|string|max:255',
                'body' => 'required|string',
                'variables' => 'nullable|array',
                'variables.*.name' => 'required|string',
                'variables.*.type' => 'required|in:text,number,date,phone,email',
                'variables.*.example' => 'nullable|string',
                'variables.*.required' => 'boolean',
            ];
            if ($request->channel === 'email') 
            {
                $validationRules['subject'] = 'required|string|max:255';
            }
            
            if ($request->channel === 'whatsapp') 
            {
                $validationRules['footer'] = 'nullable|string';
                $validationRules['language'] = 'nullable|string|max:10';
                $validationRules['header_type'] = 'nullable|in:TEXT,IMAGE,DOCUMENT,VIDEO';
                $validationRules['header_text'] = 'nullable|string|max:255';
                $validationRules['buttons'] = 'nullable|string';
                $validationRules['category'] = 'required|in:UTILITY,MARKETING,AUTHENTICATION';
                $validationRules['uploaded_files'] = 'nullable|array';
                $validationRules['uploaded_files.*'] = 'max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,mp4,avi,mov';
            }

            $validator = Validator::make($request->all(), $validationRules);

            if ($validator->fails()) 
            {
                foreach ($validator->errors()->all() as $error) 
                {
                    Flasher::addError($error);
                }
                return redirect()->back()->withInput();
            }

            DB::beginTransaction();
            $tableName = $request->channel . '_templates';
            if (!\Schema::hasTable($tableName)) 
            {
                throw new \Exception("Table {$tableName} does not exist");
            }
            $templateData = [
                'name' => $request->name,
                'body' => $request->body,
                'status' => 'pending',
                'created_by' => session('user_id'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            switch ($request->channel) 
            {
                case 'email':
                    $templateData['subject'] = $request->subject;
                    $templateData['category'] = $request->category ?? 'General';
                    break;
                    
                case 'whatsapp':
                    $templateData['footer'] = $request->footer ?? null;
                    $templateData['language'] = $request->language ?? 'en_US';
                    $templateData['header_type'] = $request->header_type ?? 'TEXT';
                    $templateData['header_text'] = $request->header_text ?? null;
                    $templateData['category'] = $request->category ?? 'UTILITY';
                    $templateData['template_id'] = 'TEMP_' . strtoupper(uniqid());
                    if ($request->filled('buttons')) 
                    {
                        $buttons = $request->buttons;
                        json_decode($buttons);
                        if (json_last_error() === JSON_ERROR_NONE) 
                        {
                            $templateData['buttons'] = $buttons;
                        } 
                        else 
                        {
                            $templateData['buttons'] = json_encode([['type' => 'quick_reply', 'text' => $buttons]]);
                        }
                    } 
                    else 
                    {
                        $templateData['buttons'] = null;
                    }
                    $attachments = [];
                    if ($request->hasFile('uploaded_files')) 
                    {
                        foreach ($request->file('uploaded_files') as $file) 
                        {
                            $filename = 'attachment_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            $path = $file->storeAs('whatsapp-attachments', $filename, 'public');
                            
                            if ($path) 
                            {
                                $attachments[] = $filename;
                            }
                        }
                    }
                    if ($request->has('existing_attachments')) 
                    {
                        $existingAttachments = array_filter($request->existing_attachments);
                        $attachments = array_merge($attachments, $existingAttachments);
                    }
                    if (!empty($attachments)) 
                    {
                        $templateData['attachments'] = implode(',', $attachments);
                    }
                    break;
                    
                case 'sms':
                    $templateData['category'] = $request->category ?? 'General';
                    break;
            }
            
            $templateId = DB::table($tableName)->insertGetId($templateData);
            if ($request->has('variables')) 
            {
                $variableTable = $request->channel . '_template_variables';
                if (!\Schema::hasTable($variableTable)) 
                {
                    throw new \Exception("Table {$variableTable} does not exist");
                }
                
                $position = 1;
                foreach ($request->variables as $variable) 
                {
                    $variableData = [
                        'template_id' => $templateId,
                        'variable_name' => $variable['name'],
                        'variable_type' => $variable['type'],
                        'position' => $position++,
                        'is_required' => $variable['required'] ?? 1,
                        'example_value' => $variable['example'] ?? null,
                    ];
                    
                    if ($request->channel === 'email' || $request->channel === 'sms') 
                    {
                        $variableData['created_at'] = now();
                        $variableData['updated_at'] = now();
                    }
                    
                    DB::table($variableTable)->insert($variableData);
                }
            }

            DB::commit();
            
            Flasher::addSuccess('Template created successfully!');
            return redirect()->route('messaging.index');
            
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Flasher::addError('Error creating template: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit(Request $request, $templateId)
    {
        try 
        {
            $channel = $request->query('channel', 'whatsapp');
            
            if (!in_array($channel, ['whatsapp', 'email', 'sms'])) 
            {
                Flasher::addError('Invalid channel');
                return redirect()->route('messaging.index');
            }
            
            $tableName = $channel . '_templates';
            
            if (!\Schema::hasTable($tableName)) 
            {
                throw new \Exception("Table {$tableName} does not exist");
            }
            
            $template = DB::table($tableName)->find($templateId);
            
            if (!$template) 
            {
                Flasher::addError('Template not found');
                return redirect()->route('messaging.index');
            }
            $variables = [];
            $variableTable = $channel . '_template_variables';
            if (\Schema::hasTable($variableTable)) 
            {
                $variables = DB::table($variableTable)
                    ->where('template_id', $templateId)
                    ->orderBy('position')
                    ->get();
            }
            
            return view('messaging.create-template', compact('channel', 'template', 'variables', 'templateId'));
            
        } 
        catch (\Exception $e)
        {
            Flasher::addError('Error loading template: ' . $e->getMessage());
            return redirect()->route('messaging.index');
        }
    }

    public function update(Request $request, $templateId)
    {
        try 
        {
            $channel = $request->input('channel');
            
            if (!in_array($channel, ['whatsapp', 'email', 'sms'])) 
            {
                Flasher::addError('Invalid channel');
                return redirect()->back()->withInput();
            }
            $validationRules = [
                'channel' => 'required|in:whatsapp,email,sms',
                'name' => 'required|string|max:255',
                'body' => 'required|string',
                'variables' => 'nullable|array',
                'variables.*.name' => 'required|string',
                'variables.*.type' => 'required|in:text,number,date,phone,email',
                'variables.*.example' => 'nullable|string',
                'variables.*.required' => 'boolean',
            ];
            if ($channel === 'email') 
            {
                $validationRules['subject'] = 'required|string|max:255';
            }
            
            if ($channel === 'whatsapp') 
            {
                $validationRules['footer'] = 'nullable|string';
                $validationRules['language'] = 'nullable|string|max:10';
                $validationRules['header_type'] = 'nullable|in:TEXT,IMAGE,DOCUMENT,VIDEO';
                $validationRules['header_text'] = 'nullable|string|max:255';
                $validationRules['buttons'] = 'nullable|string';
                $validationRules['category'] = 'required|in:UTILITY,MARKETING,AUTHENTICATION';
                $validationRules['uploaded_files'] = 'nullable|array';
                $validationRules['uploaded_files.*'] = 'max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,mp4,avi,mov';
            }
            $validator = Validator::make($request->all(), $validationRules);
            if ($validator->fails()) 
            {
                foreach ($validator->errors()->all() as $error) 
                {
                    Flasher::addError($error);
                }
                return redirect()->back()->withInput();
            }
            DB::beginTransaction();
            $tableName = $channel . '_templates';
            if (!\Schema::hasTable($tableName)) 
            {
                throw new \Exception("Table {$tableName} does not exist");
            }
            $existingTemplate = DB::table($tableName)->find($templateId);
            if (!$existingTemplate) 
            {
                Flasher::addError('Template not found');
                return redirect()->back()->withInput();
            }
            $updateData = [
                'name' => $request->name,
                'body' => $request->body,
                'updated_at' => now(),
            ];
            switch ($channel) 
            {
                case 'email':
                    $updateData['subject'] = $request->subject;
                    if ($request->has('category')) 
                    {
                        $updateData['category'] = $request->category;
                    }
                    break;
                    
                case 'whatsapp':
                    $updateData['footer'] = $request->footer ?? null;
                    $updateData['language'] = $request->language ?? 'en_US';
                    $updateData['header_type'] = $request->header_type ?? 'TEXT';
                    $updateData['header_text'] = $request->header_text ?? null;
                    $updateData['category'] = $request->category ?? 'UTILITY';
                    if ($request->filled('buttons')) 
                    {
                        $buttons = $request->buttons;
                        json_decode($buttons);
                        if (json_last_error() === JSON_ERROR_NONE) 
                        {
                            $updateData['buttons'] = $buttons;
                        } 
                        else 
                        {
                            $updateData['buttons'] = json_encode([['type' => 'quick_reply', 'text' => $buttons]]);
                        }
                    } 
                    else 
                    {
                        $updateData['buttons'] = null;
                    }
                    $attachments = [];
                    if ($request->has('existing_attachments')) 
                    {
                        $existingAttachments = array_filter($request->existing_attachments);
                        $attachments = array_merge($attachments, $existingAttachments);
                    }
                    if ($request->hasFile('uploaded_files')) 
                    {
                        foreach ($request->file('uploaded_files') as $file) 
                        {
                            $filename = 'attachment_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            $path = $file->storeAs('whatsapp-attachments', $filename, 'public');
                            
                            if ($path) 
                            {
                                $attachments[] = $filename;
                            }
                        }
                    }
                    if (!empty($attachments)) 
                    {
                        $updateData['attachments'] = implode(',', $attachments);
                    }
                    else 
                    {
                        $updateData['attachments'] = null;
                    }
                    break;
                    
                case 'sms':
                    if ($request->has('category')) 
                    {
                        $updateData['category'] = $request->category;
                    }
                    break;
            }
            DB::table($tableName)->where('id', $templateId)->update($updateData);
            if ($request->has('variables')) 
            {
                $variableTable = $channel . '_template_variables';
                if (\Schema::hasTable($variableTable)) 
                {
                    DB::table($variableTable)->where('template_id', $templateId)->delete();
                    $position = 1;
                    foreach ($request->variables as $variable) 
                    {
                        $variableData = [
                            'template_id' => $templateId,
                            'variable_name' => $variable['name'],
                            'variable_type' => $variable['type'],
                            'position' => $position++,
                            'is_required' => $variable['required'] ?? 1,
                            'example_value' => $variable['example'] ?? null,
                        ];
                        
                        if ($channel === 'email' || $channel === 'sms') 
                        {
                            $variableData['created_at'] = now();
                            $variableData['updated_at'] = now();
                        }
                        
                        DB::table($variableTable)->insert($variableData);
                    }
                }
            } 
            else 
            {
                $variableTable = $channel . '_template_variables';
                if (\Schema::hasTable($variableTable)) 
                {
                    DB::table($variableTable)->where('template_id', $templateId)->delete();
                }
            }

            DB::commit();
            
            Flasher::addSuccess('Template updated successfully!');
            return redirect()->route('messaging.index');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Template update error: ' . $e->getMessage());
            Flasher::addError('Error updating template: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy(Request $request, $templateId)
    {
        try 
        {
            $channel = $request->query('channel', 'whatsapp');
            
            if (!in_array($channel, ['whatsapp', 'email', 'sms'])) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid channel'
                ], 400);
            }
            
            DB::beginTransaction();
            
            $tableName = $channel . '_templates';
            
            if (!\Schema::hasTable($tableName)) 
            {
                throw new \Exception("Table {$tableName} does not exist");
            }
            $template = null;
            if ($channel === 'whatsapp') 
            {
                $template = DB::table($tableName)->where('id', $templateId)->first();
            }
            $deleted = DB::table($tableName)->where('id', $templateId)->delete();
            
            if ($deleted) 
            {
                $variableTable = $channel . '_template_variables';
                if (\Schema::hasTable($variableTable)) 
                {
                    DB::table($variableTable)->where('template_id', $templateId)->delete();
                }
                if ($channel === 'whatsapp' && $template && !empty($template->attachments)) 
                {
                    $attachments = explode(',', $template->attachments);
                    foreach ($attachments as $attachment) 
                    {
                        if ($attachment) 
                        {
                            Storage::disk('public')->delete('whatsapp-attachments/' . $attachment);
                        }
                    }
                }
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Template deleted successfully'
                ]);
            } 
            else 
            {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found'
                ], 404);
            }
            
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting template: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTemplateVariables($channel, $templateId)
    {
        try 
        {
            $variableTable = $channel . '_template_variables';
            
            if (!\Schema::hasTable($variableTable)) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found'
                ], 404);
            }
            
            $variables = DB::table($variableTable)
                ->where('template_id', $templateId)
                ->orderBy('position')
                ->get();
                
            return response()->json([
                'success' => true,
                'variables' => $variables
            ]);
            
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getTemplates($channel)
    {
        try 
        {
            $tableName = $channel . '_templates';
            
            if (!\Schema::hasTable($tableName)) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found'
                ], 404);
            }
            
            $templates = DB::table($tableName)
                ->orderBy('name')
                ->get();
                
            return response()->json([
                'success' => true,
                'templates' => $templates
            ]);
            
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getTemplatePreview($channel, $templateId)
    {
        try 
        {
            $tableName = $channel . '_templates';
            
            if (!\Schema::hasTable($tableName)) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found'
                ], 404);
            }
            
            $template = DB::table($tableName)->where('id', $templateId)->first();
            
            if (!$template) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found'
                ], 404);
            }
            
            $preview = '';
            if ($channel === 'email') 
            {
                $preview = $template->body;
            } 
            elseif ($channel === 'whatsapp') 
            {
                $preview = nl2br(e($template->body));
                if ($template->footer) 
                {
                    $preview .= '<br><br><small class="text-muted">' . e($template->footer) . '</small>';
                }
            } 
            else 
            {
                $preview = nl2br(e($template->body));
            }
            
            return response()->json([
                'success' => true,
                'preview' => $preview
            ]);
            
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function selectChannel()
    {
        try 
        {
            return view('messaging.select-channel');
        } 
        catch (\Exception $e) 
        {
            Flasher::addError('Error loading page');
            return redirect()->route('messaging.index');
        }
    }
}