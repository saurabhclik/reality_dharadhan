<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Flasher\Laravel\Facade\Flasher;

class LeadsImport implements ToCollection, WithHeadingRow, WithValidation
{
    private $rows = 0;
    private $duplicates = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Check for duplicate phone number
            $exists = Lead::where('phone', $this->formatPhone($row['phone']))->exists();
            
            if ($exists) {
                $this->duplicates++;
                continue;
            }

            $user = $this->getUser($row['user_email'] ?? null);
            
            Lead::create([
                'name' => $row['name'] ?? null,
                'email' => $row['email'] ?? null,
                'phone' => $this->formatPhone($row['phone']),
                'whatsapp_no' => $this->formatPhone($row['whatsapp'] ?? $row['phone']),
                'type' => $row['type'] ?? 'Residential',
                'catg_id' => $this->getCategoryId($row['category'] ?? null),
                'sub_catg_id' => $this->getSubCategoryId($row['sub_category'] ?? null),
                'source' => $row['source'] ?? 'Import',
                'campaign' => $row['campaign'] ?? null,
                'classification' => $this->getClassification($row['classification'] ?? null),
                'project_id' => $this->getProjectId($row['project'] ?? null),
                'state' => $row['state'] ?? null,
                'city' => $row['city'] ?? null,
                'address' => $row['address'] ?? null,
                'budget' => $row['budget'] ?? null,
                'status' => 'NEW LEAD',
                'last_comment' => $row['comment'] ?? 'Imported from bulk upload',
                'user_id' => $user->id ?? Auth::id(),
                'lead_date' => now(),
                'updated_date' => now(),
            ]);

            $this->rows++;
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|numeric|digits:10',
            'whatsapp' => 'nullable|numeric|digits:10',
            'type' => 'nullable|in:Residential,Commercial',
            'category' => 'nullable|string',
            'sub_category' => 'nullable|string',
            'source' => 'nullable|string',
            'campaign' => 'nullable|string',
            'classification' => 'nullable|in:hot,cold,warm',
            'project' => 'nullable|string',
            'state' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'budget' => 'nullable|numeric',
            'comment' => 'nullable|string',
            'user_email' => 'nullable|email|exists:users,email',
        ];
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function getDuplicateCount(): int
    {
        return $this->duplicates;
    }

    private function formatPhone($phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return substr($phone, -10); // Get last 10 digits
    }

    private function getCategoryId($categoryName)
    {
        if (!$categoryName) return null;
        
        $category = DB::table('inv_catg')
            ->where('name', 'like', "%$categoryName%")
            ->first();
            
        return $category->id ?? null;
    }

    private function getSubCategoryId($subCategoryName)
    {
        if (!$subCategoryName) return null;
        
        $subCategory = DB::table('inv_subcatg')
            ->where('name', 'like', "%$subCategoryName%")
            ->first();
            
        return $subCategory->id ?? null;
    }

    private function getProjectId($projectName)
    {
        if (!$projectName) return null;
        
        $project = DB::table('projects')
            ->where('project_name', 'like', "%$projectName%")
            ->first();
            
        return $project->id ?? null;
    }

    private function getClassification($classification)
    {
        if (!$classification) return 'cold';
        
        return in_array(strtolower($classification), ['hot', 'cold', 'warm']) 
            ? strtolower($classification) 
            : 'cold';
    }

    private function getUser($email)
    {
        if (!$email) return null;
        
        return User::where('email', $email)->first();
    }
}