<?php 

    class SlugGenerator
    {
        public static function generateSlug($title)
        {
            // Remove special characters and spaces
            $slug = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);

            // Convert spaces to hyphens
            $slug = str_replace(' ', '-', $slug);

            // Convert to lowercase
            $slug = strtolower($slug);

            // Remove repeated hyphens
            $slug = preg_replace('/-+/', '-', $slug);

            // Remove hyphens at beginning and end
            $slug = trim($slug, '-');

            return $slug;
        }
    }