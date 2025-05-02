<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait SeoHelpers
{
    /**
     * Generate a unique slug based on the given string
     */
    public function generateUniqueSlug(string $value, string $field = 'slug'): string
    {
        $slug = Str::slug($value);
        $originalSlug = $slug;
        $count = 1;

        // Check if a model with this slug already exists
        while ($this->where($field, $slug)->where('id', '!=', $this->id ?? null)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Generate basic meta tags if they're empty
     */
    public function generateMetaTags(): void
    {
        $nameField = $this->nameField ?? 'title'; // Default to 'title' if not set
        
        if (empty($this->meta_title)) {
            $this->meta_title = Str::limit($this->$nameField, 60);
        }
        
        if (empty($this->meta_description)) {
            $this->meta_description = Str::limit($this->$nameField, 160);
        }
        
        if (empty($this->meta_keywords)) {
            // You might want to customize this further
            $this->meta_keywords = Str::lower($this->$nameField);
        }
        
        if (empty($this->slug)) {
            $this->slug = $this->generateUniqueSlug($this->$nameField);
        }
    }
}