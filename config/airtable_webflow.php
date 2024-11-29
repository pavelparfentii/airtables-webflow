<?php


use Illuminate\Support\Str;

return [
    'Universities' => [
        'airtable_table' => 'Universities',
        'webflow_collection' => '66b5d2eb8c401c42d7d853f0',
        'fields_mapping' => [
            'Name' =>               'name',
            'Description' =>        'description-2',
            'Logotype' =>           'logo',
            'Living and Accommodation' => 'living',
            'Hero Banner' =>        'hero-banner',
            'Global Ranking URL' => 'global-ranking',
            'City'=>                'location-city',
            'Education/Programms'=> 'university-programs',
            'Local Ranking'=>       'locar-ranking',
            'About University'=>    'overview'
        ],
        'transformations' => [
            'slug' => fn($fields) => Str::slug($fields['Name'] ?? ''),
            'living' => fn($fields, $converter) => isset($fields['Living and Accommodation'])
                ? $converter->convert($fields['Living and Accommodation'])->getContent()
                : null,
            'hero-banner'=>fn($fields) => $fields['Hero Banner'][0]['url'] ?? null,
            'logo'=>fn($fields) => $fields['Logotype'][0]['url'] ?? null,
            'university-programs'=>fn($fields, $converter) => isset($fields['Education/Programms'])
                ? $converter->convert($fields['Education/Programms'])->getContent()
                : null,
            'overview'=>fn($fields, $converter) => isset($fields['About University'])
                ? $converter->convert($fields['About University'])->getContent()
                : null,
        ],
    ],
    'Courses' => [
        'airtable_table' => 'Courses',
        'webflow_collection' => '66ebddafef241d4ff8b308d1', // Example Webflow collection ID
        'fields_mapping' => [
            'Name' => 'name',
            'What you will learn' => 'what-you-will-learn',
            'Instructors' => 'instructors',
            'Level' => 'level',
            'Course content'=>'course-content',
            'Benefits & Outcomes'=>'benefits-outcomes',
            'About the course' => 'about-the-course',
            'Duration'=>'duration',
            'Slug'=>'slug',
            'Brochure'=>'brochure',
            'Target Audience'=>'target-audience',
            'Introduction'=>'introduction',
            'Banner'=>'banner',
            "Course materials Visibility"=>'course-materials-visibility',
            'Subcategory'=>'category',
            'Training Centres'=>'training-center-relation'

        ],
        'transformations' => [
            'slug' => fn($fields) => Str::slug($fields['Slug'] ?? ''),
            'what-you-will-learn' => fn($fields, $converter) => $fields['What you will learn']
                ? $converter->convert($fields['What you will learn'])->getContent()
                : null,
            'course-content' => fn($fields, $converter) => $fields['What you will learn']
                ? $converter->convert($fields['What you will learn'])->getContent()
                : null,
            'benefits-outcomes' =>fn($fields, $converter) => $fields['Benefits & Outcomes']
                ? $converter->convert($fields['Benefits & Outcomes'])->getContent()
                : null,
            'about-the-course'=>fn($fields, $converter) => $fields['About the course']
                ? $converter->convert($fields['About the course'])->getContent()
                : null,
            'brochure'=>fn($fields) => $fields['Brochure'][0]['url'] ?? null,
            'target-audience'=>fn($fields, $converter) => $fields['Target Audience']
                ? $converter->convert($fields['Target Audience'])->getContent()
                : null,
            'banner'=>fn($fields) => $fields['Brochure'][0]['url'] ?? null,
            'course-materials-visibility'=>fn($fields)=> filter_var($fields['Course materials Visibility'], FILTER_VALIDATE_BOOLEAN) ?? false,
            'category'=>fn($fields, $categoryMapper) => $categoryMapper->mapCategory($fields['Subcategory'] ?? null, 'course-categories'),
            'training-center-relation'=>fn($fields, $categoryMapper) => $categoryMapper->mapCategory($fields['Training Centres'] ?? null, 'training-center-courses'),
//            'training-center-relation'=>fn($fields, $categoryMapper) => var_dump($fields['Training Centres'])
        ],
    ],
];
