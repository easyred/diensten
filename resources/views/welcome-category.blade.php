@php
    // Categories that are problems/services (not people) - need "specialist" appended
    $problemCategories = [
        'computerhulp',
        'dakwerken',
        'gaslek',
        'ithulp',
        'klusjes',
        'koeling',
        'ontstopping',
        'opkuis',
        'verhuis',
    ];
    
    // Get category data
    $cat = $category ?? null;
    
    // If category not found, create default values based on domain
    if (!$cat) {
        $host = request()->getHost();
        // Remove www. from host for cleaner domain
        $cleanHost = preg_replace('/^www\./', '', $host);
        $siteName = str_replace(['.pro', '.app', '.com'], '', $cleanHost);
        $siteNameCapitalized = ucwords(str_replace(['-', '_'], ' ', $siteName));
        $categoryCode = strtolower(str_replace(['-', '_', ' '], '', $siteName));
        
        // Check if this is a problem category
        $isProblemCategoryDefault = in_array($categoryCode, $problemCategories);
        $siteNameForMeta = $isProblemCategoryDefault ? strtolower($siteNameCapitalized) . ' specialist' : strtolower($siteNameCapitalized);
        
        // Create a default category object-like structure
        $cat = (object) [
            'id' => null,
            'code' => $categoryCode,
            'name' => $siteNameCapitalized,
            'domain' => $cleanHost,
            'primary_color' => '#059669',
            'secondary_color' => '#047857',
            'meta_title' => $siteNameCapitalized . ' - Professionele Dienst',
            'meta_description' => 'Vind professionele ' . $siteNameForMeta . ' in uw buurt.',
            'site_description' => 'Vind professionele ' . $siteNameForMeta . ' in uw buurt.',
            'favicon_url' => null,
            'logo_url' => null,
            'meta_keywords' => null,
            'og_image_url' => null,
            'welcome_content' => [],
        ];
    }
    
    // Get category branding and content
    $primaryColor = $cat->primary_color ?: '#059669';
    $secondaryColor = $cat->secondary_color ?: '#047857';
    $logo = $cat->logo_url ? (str_starts_with($cat->logo_url, 'http') ? $cat->logo_url : asset($cat->logo_url)) : null;
    $brandName = $cat->name;
    $brandDomain = $cat->domain;
    // Remove www. and extract site name from domain
    $cleanDomain = preg_replace('/^www\./', '', $brandDomain);
    $siteName = str_replace(['.pro', '.app', '.com'], '', $cleanDomain);
    $siteNameCapitalized = ucfirst($siteName);
    
    // Helper function to add "specialist" for problem categories
    $isProblemCategory = in_array($cat->code, $problemCategories);
    $categoryNameForContent = $isProblemCategory ? $brandName . ' specialist' : $brandName;
    $categoryNameLowerForContent = $isProblemCategory ? strtolower($brandName) . ' specialist' : strtolower($brandName);
    $siteNameForContent = $isProblemCategory ? $siteNameCapitalized . ' Specialist' : $siteNameCapitalized;
    
    $metaTitle = $cat->meta_title ?: ($cat->name . ' - ' . $cat->domain);
    $metaDescription = $cat->meta_description ?: ($cat->site_description ?: 'Vind professionele ' . $categoryNameLowerForContent . ' in uw buurt.');
    
    // Icon mapping for categories
    $categoryIcons = [
        'plumber' => 'fa-wrench',
        'loodgieter' => 'fa-wrench',
        'computerhulp' => 'fa-laptop',
        'dakwerken' => 'fa-home',
        'dierenarts' => 'fa-paw',
        'elektrieker' => 'fa-bolt',
        'gaslek' => 'fa-fire',
        'glazenmaker' => 'fa-window-maximize',
        'ithulp' => 'fa-server',
        'klusjes' => 'fa-hammer',
        'koeling' => 'fa-snowflake',
        'kuisvrouw' => 'fa-broom',
        'ontstopping' => 'fa-tint',
        'opkuis' => 'fa-broom',
        'slotenmaker' => 'fa-key',
        'thuiszorg' => 'fa-heart',
        'tuinman' => 'fa-leaf',
        'gardener' => 'fa-leaf',
        'verhuis' => 'fa-truck',
    ];
    $categoryIcon = $categoryIcons[$cat->code] ?? 'fa-tools';
    
    // Favicon: use category favicon if set from admin, otherwise create SVG from category icon
    if ($cat->favicon_url) {
        $favicon = str_starts_with($cat->favicon_url, 'http') ? $cat->favicon_url : asset($cat->favicon_url);
    } else {
        // Create SVG favicon from category icon
        // Map FontAwesome icons to SVG paths (simplified versions)
        $iconPaths = [
            'fa-wrench' => '<path d="M352 96c0-17.7-14.3-32-32-32s-32 14.3-32 32c0 17.7 14.3 32 32 32s32-14.3 32-32zm-64 32c-17.7 0-32 14.3-32 32s14.3 32 32 32l192 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-192 0zM224 352c0 17.7 14.3 32 32 32l192 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-192 0c-17.7 0-32 14.3-32 32z"/><path d="M352 128c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32 14.3-32 32l0 192c0 17.7 14.3 32 32 32l64 0c17.7 0 32-14.3 32-32l0-192z"/>',
            'fa-laptop' => '<path d="M64 96c-35.3 0-64 28.7-64 64L0 352c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-192c0-35.3-28.7-64-64-64L64 96zM448 352c0 17.7-14.3 32-32 32L96 384c-17.7 0-32-14.3-32-32l0-192c0-17.7 14.3-32 32-32l320 0c17.7 0 32 14.3 32 32l0 192z"/>',
            'fa-home' => '<path d="M575.8 255.5c0 18-15 32.1-32 32.1h-32l.7 160.2c0 2.7-.2 5.4-.5 8.1V472c0 22.1-17.9 40-40 40H456c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1H416 392c-22.1 0-40-17.9-40-40V448 384c0-17.7-14.3-32-32-32H256c-17.7 0-32 14.3-32 32v64 24c0 22.1-17.9 40-40 40H160 128.1c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2H104c-22.1 0-40-17.9-40-40V360c0-.9 0-1.9 .1-2.8V287.6H32c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z"/>',
            'fa-paw' => '<path d="M320 0c17.7 0 32 14.3 32 32s-14.3 32-32 32s-32-14.3-32-32S302.3 0 320 0zM195.3 51.3c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3s32.8 12.5 45.3 0s12.5-32.8 0-45.3zm281.4 0c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3s32.8 12.5 45.3 0s12.5-32.8 0-45.3zM416 160c0-17.7-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32s32-14.3 32-32zM160 192c0-17.7-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32s32-14.3 32-32zm352 0c0-17.7-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32s32-14.3 32-32zM384 320c0-17.7-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32s32-14.3 32-32zM128 352c0-17.7-14.3-32-32-32S64 334.3 64 352s14.3 32 32 32s32-14.3 32-32zm320 0c0-17.7-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32s32-14.3 32-32z"/>',
            'fa-bolt' => '<path d="M349.4 44.6c5.9-13.7 1.5-29.7-10.6-38.5s-28.6-8.7-42.3-1.4L52.9 115.2c-13.2 7.3-21.9 20.9-22.8 36l-2.1 42.3c-.9 15.1 7.4 29.3 21.5 35.8l144.1 66.2c14.1 6.5 30.9 4.2 43.1-6.1s17.8-25.9 14.1-40.5L195.2 192l144.1-66.2c14.1-6.5 23.3-20.6 22.4-35.8l-2.1-42.3c-.9-15.1-9.6-28.7-22.8-36L349.4 44.6zm-89.1 171.3L116 282.1l2.1-42.3 144.1 66.2-2.1 42.3zM504 320c-9.2 0-18.1 3.2-25.1 9.1L402.2 384H344c-13.3 0-24 10.7-24 24s10.7 24 24 24h96c9.2 0 18.1-3.2 25.1-9.1l76.8-54.9c7.6-5.4 11.9-14.2 11.9-23.5s-4.3-18.1-11.9-23.5C522.1 323.2 513.2 320 504 320zM344 432H248c-9.2 0-18.1 3.2-25.1 9.1l-76.8 54.9c-7.6 5.4-11.9 14.2-11.9 23.5s4.3 18.1 11.9 23.5c7 5.9 15.9 9.1 25.1 9.1h96c13.3 0 24-10.7 24-24s-10.7-24-24-24z"/>',
            'fa-fire' => '<path d="M349.4 44.6c5.9-13.7 1.5-29.7-10.6-38.5s-28.6-8.7-42.3-1.4L52.9 115.2c-13.2 7.3-21.9 20.9-22.8 36l-2.1 42.3c-.9 15.1 7.4 29.3 21.5 35.8l144.1 66.2c14.1 6.5 30.9 4.2 43.1-6.1s17.8-25.9 14.1-40.5L195.2 192l144.1-66.2c14.1-6.5 23.3-20.6 22.4-35.8l-2.1-42.3c-.9-15.1-9.6-28.7-22.8-36L349.4 44.6zm-89.1 171.3L116 282.1l2.1-42.3 144.1 66.2-2.1 42.3zM504 320c-9.2 0-18.1 3.2-25.1 9.1L402.2 384H344c-13.3 0-24 10.7-24 24s10.7 24 24 24h96c9.2 0 18.1-3.2 25.1-9.1l76.8-54.9c7.6-5.4 11.9-14.2 11.9-23.5s-4.3-18.1-11.9-23.5C522.1 323.2 513.2 320 504 320zM344 432H248c-9.2 0-18.1 3.2-25.1 9.1l-76.8 54.9c-7.6 5.4-11.9 14.2-11.9 23.5s4.3 18.1 11.9 23.5c7 5.9 15.9 9.1 25.1 9.1h96c13.3 0 24-10.7 24-24s-10.7-24-24-24z"/>',
            'fa-hammer' => '<path d="M352 96c0-17.7-14.3-32-32-32s-32 14.3-32 32c0 17.7 14.3 32 32 32s32-14.3 32-32zm-64 32c-17.7 0-32 14.3-32 32s14.3 32 32 32l192 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-192 0zM224 352c0 17.7 14.3 32 32 32l192 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-192 0c-17.7 0-32 14.3-32 32z"/>',
            'fa-snowflake' => '<path d="M32 448c-17.7 0-32 14.3-32 32s14.3 32 32 32l96 0c12.4 0 23.6-7.1 29-18.2l20-40 20 40c5.4 11.1 16.6 18.2 29 18.2l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0c-12.4 0-23.6 7.1-29 18.2l-20 40-20-40c-5.4-11.1-16.6-18.2-29-18.2l-96 0zM32 0C14.3 0 0 14.3 0 32s14.3 32 32 32l96 0c12.4 0 23.6-7.1 29-18.2l20-40 20 40c5.4 11.1 16.6 18.2 29 18.2l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0c-12.4 0-23.6 7.1-29 18.2l-20 40-20-40C117.6 7.1 106.4 0 94 0L32 0zM256 192c0-17.7-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32s32-14.3 32-32zM416 192c0-17.7-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32s32-14.3 32-32zM352 320c17.7 0 32-14.3 32-32s-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32zM192 320c17.7 0 32-14.3 32-32s-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32z"/>',
            'fa-broom' => '<path d="M352 96c0-17.7-14.3-32-32-32s-32 14.3-32 32c0 17.7 14.3 32 32 32s32-14.3 32-32zm-64 32c-17.7 0-32 14.3-32 32s14.3 32 32 32l192 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-192 0zM224 352c0 17.7 14.3 32 32 32l192 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-192 0c-17.7 0-32 14.3-32 32z"/>',
            'fa-tint' => '<path d="M223.1 22.1c-7.8-11.7-22.4-17-35.9-12.9S169 20.4 169 34v190.7c0 10.2 4.6 19.9 12.7 26.2l52 40.7c6.1 4.8 9.3 12.1 9.3 19.7v176c0 17.7 14.3 32 32 32s32-14.3 32-32V312.3c0-10.2-4.6-19.9-12.7-26.2l-52-40.7c-6.1-4.8-9.3-12.1-9.3-19.7V34c0-13.6-8.3-25.9-20.8-30.7S230.9 10.4 223.1 22.1zM96 112c0-17.7-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32s32-14.3 32-32zm32 64c0-17.7-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32s32-14.3 32-32zm320 0c0-17.7-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32s32-14.3 32-32zM480 112c0-17.7-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32s32-14.3 32-32z"/>',
            'fa-key' => '<path d="M336 352c97.2 0 176-78.8 176-176S433.2 0 336 0S160 78.8 160 176c0 18.7 2.9 36.8 8.3 53.7L7 391c-4.5 4.5-7 10.6-7 17v80c0 13.3 10.7 24 24 24h80c13.3 0 24-10.7 24-24V448h40c13.3 0 24-10.7 24-24V384h40c6.4 0 12.5-2.5 17-7l33.3-33.3c16.9 5.4 35 8.3 53.7 8.3zM376 96a40 40 0 1 1 0 80 40 40 0 1 1 0-80z"/>',
            'fa-heart' => '<path d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/>',
            'fa-leaf' => '<path d="M272 96c-26.5 0-48 21.5-48 48 0 8.8-7.2 16-16 16s-16-7.2-16-16c0-44.2 35.8-80 80-80s80 35.8 80 80c0 8.8-7.2 16-16 16s-16-7.2-16-16c0-26.5-21.5-48-48-48zM272 0c-5.1 0-10.2 .3-15.2 .9C238.4 3.2 230.2 8.6 224 16L208 32 192 16c-6.2-7.4-14.4-12.8-32.8-15.1C144.2 .3 139.1 0 134 0C60 0 0 60 0 134c0 5.1 .3 10.2 .9 15.2C3.2 161.6 8.6 169.8 16 176l16 16L16 208c-7.4 6.2-12.8 14.4-15.1 32.8C.3 245.8 0 250.9 0 256c0 70.7 57.3 128 128 128c5.1 0 10.2-.3 15.2-.9c17.4-2.3 25.6-7.7 32.8-15.1l16-16 16 16c7.4 6.2 15.6 11.6 33 14.9c5 .6 10.1 .9 15.2 .9c70.7 0 128-57.3 128-128V128C512 57.3 454.7 0 384 0H272zM384 128v128c0 35.3-28.7 64-64 64H192V192c0-35.3 28.7-64 64-64H384z"/>',
            'fa-truck' => '<path d="M0 48C0 21.5 21.5 0 48 0H368c26.5 0 48 21.5 48 48V96h50.7c17 0 33.3 6.7 45.3 18.7L562.7 192c12 12 18.7 28.3 18.7 45.3V256v48 48c0 35.3-28.7 64-64 64H512c0 53-43 96-96 96s-96-43-96-96H256c0 53-43 96-96 96s-96-43-96-96H48c-26.5 0-48-21.5-48-48V48zM416 256H544V237.3L466.7 160H416v96zM160 464a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm288 0a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/>',
            'fa-tools' => '<path d="M352 96c0-17.7-14.3-32-32-32s-32 14.3-32 32c0 17.7 14.3 32 32 32s32-14.3 32-32zm-64 32c-17.7 0-32 14.3-32 32s14.3 32 32 32l192 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-192 0zM224 352c0 17.7 14.3 32 32 32l192 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-192 0c-17.7 0-32 14.3-32 32z"/>',
        ];
        
        // Get SVG path for the icon, or use default tools icon
        $iconPath = $iconPaths[$categoryIcon] ?? $iconPaths['fa-tools'];
        
        // Create SVG favicon
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="512" height="512"><path fill="' . htmlspecialchars($primaryColor, ENT_QUOTES, 'UTF-8') . '" d="M256 0C114.6 0 0 114.6 0 256s114.6 256 256 256s256-114.6 256-256S397.4 0 256 0z"/>' . $iconPath . '</svg>';
        $favicon = 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
    
    // Get welcome content if available
    $welcomeContent = $cat->welcome_content ?? [];
    
    // OG Image: use category og_image_url if set from admin, otherwise generate from icon
    if ($cat->og_image_url) {
        $ogImage = str_starts_with($cat->og_image_url, 'http') ? $cat->og_image_url : asset($cat->og_image_url);
    } else {
        // Generate OG image URL from category icon (1200x630 for OG standard)
        $ogImage = route('og-image', ['category' => $cat->code, 'color' => urlencode($primaryColor)]);
    }
@endphp
<!DOCTYPE html>
<html lang="nl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    @if($cat->meta_keywords)
        <meta name="keywords" content="{{ $cat->meta_keywords }}">
    @endif
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ $favicon }}">
    <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ $favicon }}">
    <link rel="apple-touch-icon" href="{{ $favicon }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/png">
    <meta property="og:site_name" content="{{ $brandName }}">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- intl-tel-input for country code selector -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '{{ $primaryColor }}10',
                            100: '{{ $primaryColor }}20',
                            500: '{{ $primaryColor }}',
                            600: '{{ $primaryColor }}',
                            700: '{{ $secondaryColor }}',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        :root {
            --nav-height: 4rem;
            --primary-color: {{ $primaryColor }};
            --secondary-color: {{ $secondaryColor }};
        }
        
        .nav-default {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.6);
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05);
        }
        
        .nav-scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.8);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
        
        .dark .nav-default,
        .dark .nav-scrolled {
            background: rgba(15, 23, 42, 0.95);
            border-bottom-color: rgba(51, 65, 85, 0.6);
        }
        
        .nav-link {
            position: relative;
            transition: all 0.2s ease;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        .btn-primary-gradient {
            background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
            transition: all 0.2s ease;
        }
        
        .btn-primary-gradient:hover {
            background: linear-gradient(135deg, {{ $secondaryColor }}, {{ $primaryColor }});
            box-shadow: 0 6px 16px rgba(5, 150, 105, 0.35);
            transform: translateY(-1px);
        }
        
        .service-card-hover {
            transition: all 0.3s ease;
        }
        
        .service-card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .faq-item {
            transition: all 0.3s ease;
        }
        
        .faq-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            opacity: 0;
        }
        
        .faq-item.active .faq-content {
            max-height: 500px;
            opacity: 1;
        }
        
        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }

        /* intl-tel-input styling */
        #whatsapp_number {
            padding-left: 60px !important;
        }
        .iti {
            width: 100%;
        }
        .iti__flag-container {
            position: absolute;
            left: 12px !important;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
        }
        .iti__selected-flag {
            padding: 0 8px 0 0;
            background: transparent !important;
            border: none !important;
        }
        .iti__selected-flag:hover {
            background: transparent !important;
        }
        .iti__arrow {
            margin-left: 4px;
            border-top-color: #64748b;
            opacity: 0.7;
        }
        .dark .iti__arrow {
            border-top-color: #9ca3af;
        }
        
        .dark .iti__country-list {
            background-color: #1f2937;
            border-color: #374151;
            color: #f9fafb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        .dark .iti__country {
            color: #f9fafb;
        }
        .dark .iti__country:hover,
        .dark .iti__country.iti__highlight {
            background-color: #374151;
        }
        .dark .iti__search-input {
            background-color: #1f2937;
            color: #f9fafb;
            border-color: #374151;
        }
    </style>
</head>

<body class="font-sans antialiased bg-white dark:bg-gray-900">
    <!-- Top Network Bar -->
    <div class="fixed top-0 left-0 right-0 z-[60] bg-slate-50 dark:bg-gray-900 border-b border-slate-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-center h-8">
                <a href="https://diensten.pro/" class="text-xs text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-gray-200 transition-colors">
                    Deze pagina behoort tot het netwerk diensten.pro
                </a>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav id="navbar" class="fixed top-8 left-0 right-0 z-50 nav-default dark:bg-gray-800/95 dark:border-gray-700 transition-all duration-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between" style="height: var(--nav-height);">
                <!-- Logo -->
                <div class="flex items-center space-x-3 group">
                    <div class="relative flex items-center justify-center w-10 h-10 rounded-lg shadow-md group-hover:shadow-lg transition-all duration-300 group-hover:scale-105 logo-container" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                        @if($logo)
                            <img src="{{ $logo }}" alt="{{ $brandName }}" class="w-10 h-10 rounded-lg object-cover logo-image" onerror="this.style.display='none'; this.parentElement.querySelector('.logo-icon-fallback').style.display='flex';">
                            <i class="fas {{ $categoryIcon }} text-white text-lg transform rotate-45 logo-icon-fallback" style="display: none;"></i>
                        @else
                            <i class="fas {{ $categoryIcon }} text-white text-lg transform rotate-45"></i>
                        @endif
                    </div>
                    <a href="{{ route('welcome') }}" class="text-2xl font-bold tracking-tight hover:scale-105 transition-transform duration-200">
                        <span class="text-gray-900 dark:text-white">{{ $siteNameCapitalized }}</span><span class="text-emerald-600 dark:text-emerald-400" style="color: {{ $primaryColor }};">.{{ str_replace($siteName . '.', '', $brandDomain) }}</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="#how-it-works" class="nav-link text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium text-sm tracking-wide" style="--hover-color: {{ $primaryColor }};">
                        Hoe het werkt
                    </a>
                    <a href="#services" class="nav-link text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium text-sm tracking-wide" style="--hover-color: {{ $primaryColor }};">
                        Diensten
                    </a>
                    <a href="#pricing" class="nav-link text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium text-sm tracking-wide" style="--hover-color: {{ $primaryColor }};">
                        Prijzen
                    </a>
                    <a href="#support" class="nav-link text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium text-sm tracking-wide" style="--hover-color: {{ $primaryColor }};">
                        Ondersteuning
                    </a>
                </div>

                <!-- Desktop Buttons -->
                <div class="hidden lg:flex items-center space-x-4">
                    <!-- Dark Mode Toggle -->
                    <x-dark-mode-toggle />

                    @guest
                        <a href="{{ env('HUB_URL', 'https://diensten.pro') }}/login" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:scale-105">
                            <i class="fas fa-sign-in-alt mr-2"></i>Inloggen
                        </a>
                        <a href="{{ route('client.register') }}" class="btn-primary-gradient text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition-all duration-200 hover:scale-105">
                            <i class="fas fa-search mr-2"></i>Vind een {{ $siteNameForContent }}
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="btn-primary-gradient text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition-all duration-200 hover:scale-105">
                                <i class="fas fa-sign-out-alt mr-2"></i>Uitloggen
                            </button>
                        </form>
                    @endguest
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="lg:hidden p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200">
                    <i id="menu-icon" class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="lg:hidden hidden border-t border-gray-200 dark:border-gray-700 bg-white/98 dark:bg-gray-800/98 backdrop-blur-xl shadow-lg">
                <div class="px-4 py-6 space-y-4">
                    <div class="space-y-2">
                        <a href="#how-it-works" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-gray-700 rounded-xl transition-all duration-200" style="--hover-color: {{ $primaryColor }};">
                            <i class="fas fa-cogs mr-3" style="color: {{ $primaryColor }};"></i>
                            <span class="font-medium">Hoe het werkt</span>
                        </a>
                        <a href="#services" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-gray-700 rounded-xl transition-all duration-200" style="--hover-color: {{ $primaryColor }};">
                            <i class="fas fa-tools mr-3" style="color: {{ $primaryColor }};"></i>
                            <span class="font-medium">Diensten</span>
                        </a>
                        <a href="#pricing" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-gray-700 rounded-xl transition-all duration-200" style="--hover-color: {{ $primaryColor }};">
                            <i class="fas fa-tags mr-3" style="color: {{ $primaryColor }};"></i>
                            <span class="font-medium">Prijzen</span>
                        </a>
                        <a href="#support" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-gray-700 rounded-xl transition-all duration-200" style="--hover-color: {{ $primaryColor }};">
                            <i class="fas fa-life-ring mr-3" style="color: {{ $primaryColor }};"></i>
                            <span class="font-medium">Ondersteuning</span>
                        </a>
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700 space-y-3">
                        <div class="flex justify-center">
                            <x-dark-mode-toggle />
                        </div>
                        
                        @guest
                            <a href="{{ env('HUB_URL', 'https://diensten.pro') }}/login" class="flex items-center justify-center w-full text-gray-700 hover:text-gray-900 hover:bg-gray-50 px-4 py-3 rounded-xl transition-all duration-200 border border-gray-200">
                                <i class="fas fa-sign-in-alt mr-3"></i>
                                <span class="font-medium">Inloggen</span>
                            </a>
                            <a href="{{ route('client.register') }}" class="flex items-center justify-center w-full btn-primary-gradient text-white font-semibold px-4 py-3 rounded-xl transition-all duration-200">
                                <i class="fas fa-search mr-3"></i>
                                <span>Vind een {{ $siteNameForContent }}</span>
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="flex items-center justify-center w-full text-gray-700 hover:text-gray-900 hover:bg-gray-50 px-4 py-3 rounded-xl transition-all duration-200 border border-gray-200">
                                <i class="fas fa-tachometer-alt mr-3"></i>
                                <span class="font-medium">Dashboard</span>
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

    <!-- Page Content -->
    <main class="flex-1 pt-24">
        <div class="min-h-screen bg-white dark:bg-black text-slate-900 dark:text-white relative">
            
            <!-- Hero Section -->
            @guest
            <section class="pt-24 pb-16 px-4 sm:px-6 lg:px-8 bg-white dark:bg-black">
                <div class="max-w-7xl mx-auto">
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <!-- Left Column - Content -->
                        <div class="space-y-8">
                            <div class="space-y-6">
                                <h1 class="text-4xl md:text-5xl lg:text-6xl font-semibold text-slate-900 dark:text-white leading-tight">
                                    {{ $welcomeContent['hero_title'] ?? 'Vind direct een' }}<br>
                                    {{ $welcomeContent['hero_title_highlight'] ?? 'professionele ' . $categoryNameLowerForContent }}<br>
                                    {{ $welcomeContent['hero_subtitle_second'] ?? 'bij jou in de buurt.' }}
                                </h1>
                                <p class="text-lg md:text-xl text-slate-600 dark:text-gray-300 leading-relaxed max-w-lg">
                                    {{ $welcomeContent['hero_subtitle'] ?? ($cat->site_description ?: 'Neem eenvoudig contact op via WhatsApp. Geverifieerde ' . $categoryNameLowerForContent) }}
                                </p>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-4 @auth justify-center @endauth">
                                @guest
                                    <a href="{{ route('client.register') }}" class="text-white px-8 py-4 text-lg font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 text-center" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                                        Vind een {{ $siteNameForContent }}
                                    </a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="text-white px-8 py-4 text-lg font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 text-center" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                                        Ga naar Dashboard
                                    </a>
                                @endguest
                            </div>

                            <!-- Trust indicators -->
                            @if(isset($welcomeContent['hero_features']) && is_array($welcomeContent['hero_features']))
                            <div class="flex flex-wrap items-center gap-6 text-sm text-slate-500 dark:text-gray-400 @auth justify-center @endauth">
                                @foreach($welcomeContent['hero_features'] as $feature)
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full animate-pulse" style="background-color: {{ $primaryColor }};"></div>
                                    <span>{{ $feature }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                                
                        <!-- Right Column - WhatsApp Login Form -->
                        <div class="relative">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-xl">
                                <div class="flex items-center gap-2 mb-4">
                                    <i class="fab fa-whatsapp text-2xl" style="color: {{ $primaryColor }};"></i>
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Log in met WhatsApp</h2>
                                </div>
                                
                                @if (!session('otp_sent') && !session('whatsapp_login_number'))
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        Voer uw WhatsApp-nummer in en ontvang een verificatiecode
                                    </p>
                                @else
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        <i class="fab fa-whatsapp mr-1"></i>Code verzonden naar: <strong>{{ format_phone_number(session('whatsapp_number') ?? session('whatsapp_login_number')) }}</strong>
                                    </p>
                                @endif
                                
                                @if (session('success'))
                                    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 px-4 py-3 rounded-lg text-sm mb-4">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg text-sm mb-4">
                                        @foreach ($errors->all() as $error)
                                            {!! $error !!}<br>
                                        @endforeach
                                    </div>
                                @endif

                                @if (!session('otp_sent') && !session('whatsapp_login_number'))
                                    <!-- WhatsApp Number Input Form -->
                                    <form method="POST" action="{{ route('whatsapp.login.send') }}" class="space-y-4" id="whatsappForm">
                                        @csrf
                                        
                                        <div>
                                            <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 dark:text-white mb-1">
                                                <i class="fab fa-whatsapp mr-1"></i>WhatsApp-nummer
                                            </label>
                                            <div class="relative">
                                                <input 
                                                    id="whatsapp_number" 
                                                    name="whatsapp_number" 
                                                    type="tel" 
                                                    autocomplete="tel" 
                                                    required 
                                                    class="w-full px-4 py-2.5 pl-16 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:border-transparent text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 transition-all"
                                                    style="focus:ring-color: {{ $primaryColor }};"
                                                    value="{{ old('whatsapp_number') }}"
                                                >
                                            </div>
                                            <input type="hidden" id="whatsapp_number_full" name="whatsapp_number_full">
                                            @error('whatsapp_number')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        
                                        <div>
                                            <button 
                                                type="submit" 
                                                class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 transition-all duration-200"
                                                style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});"
                                            >
                                                <i class="fab fa-whatsapp"></i>
                                                Verstuur verificatiecode
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <!-- OTP Verification Form -->
                                    <form method="POST" action="{{ route('otp.verify.submit') }}" class="space-y-4" id="otpForm">
                                        @csrf
                                        <input type="hidden" name="remember" value="true">
                                        
                                        <div>
                                            <label for="otp_code" class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                                                6-cijferige verificatiecode
                                            </label>
                                            <div class="flex gap-2 justify-center" id="otpContainer">
                                                <input type="text" class="otp-digit-input w-12 h-14 text-center text-2xl font-bold bg-gray-50 dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:border-transparent text-gray-900 dark:text-white transition-all" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="0" required style="focus:ring-color: {{ $primaryColor }};">
                                                <input type="text" class="otp-digit-input w-12 h-14 text-center text-2xl font-bold bg-gray-50 dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:border-transparent text-gray-900 dark:text-white transition-all" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="1" required style="focus:ring-color: {{ $primaryColor }};">
                                                <input type="text" class="otp-digit-input w-12 h-14 text-center text-2xl font-bold bg-gray-50 dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:border-transparent text-gray-900 dark:text-white transition-all" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="2" required style="focus:ring-color: {{ $primaryColor }};">
                                                <input type="text" class="otp-digit-input w-12 h-14 text-center text-2xl font-bold bg-gray-50 dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:border-transparent text-gray-900 dark:text-white transition-all" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="3" required style="focus:ring-color: {{ $primaryColor }};">
                                                <input type="text" class="otp-digit-input w-12 h-14 text-center text-2xl font-bold bg-gray-50 dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:border-transparent text-gray-900 dark:text-white transition-all" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="4" required style="focus:ring-color: {{ $primaryColor }};">
                                                <input type="text" class="otp-digit-input w-12 h-14 text-center text-2xl font-bold bg-gray-50 dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:border-transparent text-gray-900 dark:text-white transition-all" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="5" required style="focus:ring-color: {{ $primaryColor }};">
                                            </div>
                                            <input type="hidden" name="otp_code" id="otp_code" required>
                                            @error('otp_code')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{!! $message !!}</p>
                                            @enderror
                                        </div>
                                        
                                        <div>
                                            <button 
                                                type="submit" 
                                                class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 transition-all duration-200"
                                                style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});"
                                            >
                                                <i class="fas fa-check"></i>
                                                Verifieer en log in
                                            </button>
                                        </div>
                                    </form>

                                    <form method="POST" action="{{ route('otp.resend') }}" class="mt-2">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="w-full flex justify-center items-center gap-2 py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200"
                                        >
                                            <i class="fas fa-redo"></i>
                                            Code opnieuw verzenden
                                        </button>
                                    </form>
                                @endif
                                
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    @if (!session('otp_sent') && !session('whatsapp_login_number'))
                                        <div class="text-center text-sm text-gray-600 dark:text-gray-300 mb-2">
                                            Heb je nog geen account? 
                                            <a href="{{ route('client.register') }}" class="font-medium transition-colors" style="color: {{ $primaryColor }};">
                                                Registreer als klant
                                            </a>
                                        </div>
                                        <div class="text-center">
                                            <a href="{{ env('HUB_URL', 'https://diensten.pro') }}/login" class="text-xs text-gray-500 dark:text-gray-400 hover:transition-colors" style="hover:color: {{ $primaryColor }};">
                                                Liever inloggen met e-mail?
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <a href="{{ route('whatsapp.login') }}" class="text-xs text-gray-500 dark:text-gray-400 hover:transition-colors" style="hover:color: {{ $primaryColor }};">
                                                ← Ander nummer gebruiken
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @endguest

            <!-- How It Works Section -->
            <section id="how-it-works" class="py-16 px-4 sm:px-6 lg:px-8 bg-white dark:bg-gray-900">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-semibold mb-4" style="color: {{ $primaryColor }};">{{ $welcomeContent['how_it_works_title'] ?? 'Hoe Het Werkt' }}</h2>
                        <p class="text-lg text-gray-700 dark:text-gray-300 max-w-2xl mx-auto">
                            {{ $welcomeContent['how_it_works_subtitle'] ?? 'Maak verbinding met geverifieerde ' . $categoryNameLowerForContent . ' in drie eenvoudige stappen' }}
                        </p>
                    </div>
                        
                    <div class="grid md:grid-cols-3 gap-8">
                        @php
                            $steps = $welcomeContent['how_it_works_steps'] ?? [
                                ['title' => 'Aanmelden', 'description' => 'Maak in seconden een account als klant of ' . $categoryNameLowerForContent . ' aan.', 'icon' => 'fa-user'],
                                ['title' => 'Verbind via WhatsApp', 'description' => 'Chat direct om te bespreken en te boeken.', 'icon' => 'fa-whatsapp'],
                                ['title' => 'Verzoek indienen', 'description' => 'Beschrijf je probleem; wij verbinden je met beschikbare professionals.', 'icon' => 'fa-clipboard-list'],
                            ];
                        @endphp
                        @foreach($steps as $index => $step)
                        <div class="text-center group">
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 shadow-sm hover:shadow-lg transition-all duration-300 group-hover:-translate-y-1">
                                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:transition-colors duration-300" style="background-color: {{ $primaryColor }}20;">
                                    <i class="fas {{ $step['icon'] ?? 'fa-check' }} text-2xl" style="color: {{ $primaryColor }};"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">{{ $step['title'] ?? 'Stap ' . ($index + 1) }}</h3>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $step['description'] ?? '' }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- Services Section -->
            <section id="services" class="py-16 px-4 sm:px-6 lg:px-8 bg-white dark:bg-gray-900">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-semibold mb-4" style="color: {{ $primaryColor }};">{{ $welcomeContent['services_title'] ?? 'Onze Diensten' }}</h2>
                        <p class="text-lg text-gray-700 dark:text-gray-300 max-w-2xl mx-auto">{{ $welcomeContent['services_subtitle'] ?? 'Complete ' . $categoryNameLowerForContent . ' oplossingen voor al jouw behoeften' }}</p>
                    </div>
            
                    @php
                        $services = $welcomeContent['services'] ?? [
                            ['title' => 'Noodreparaties', 'description' => '24/7 nooddiensten voor urgente problemen', 'icon' => 'fa-exclamation-triangle', 'color' => 'red'],
                            ['title' => 'Installatie', 'description' => 'Professionele installatie van systemen', 'icon' => 'fa-tools', 'color' => 'blue'],
                            ['title' => 'Onderhoud', 'description' => 'Regulier onderhoud en preventieve diensten', 'icon' => 'fa-cog', 'color' => 'emerald'],
                        ];
                    @endphp
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($services as $service)
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer group hover:-translate-y-1 service-card-hover">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:transition-colors duration-300" style="background-color: {{ $primaryColor }}20;">
                                    <i class="fas {{ $service['icon'] ?? 'fa-check' }} text-xl" style="color: {{ $primaryColor }};"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $service['title'] ?? 'Service' }}</h3>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-3 leading-relaxed">{{ $service['description'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- Pricing Section -->
            <section id="pricing" class="py-16 px-4 sm:px-6 lg:px-8 bg-white dark:bg-gray-900">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center gap-2 text-white px-4 py-2 rounded-full text-sm font-medium mb-6" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                            <i class="fas fa-star"></i>
                            <span>Beta: Alle diensten momenteel beschikbaar zonder kosten</span>
                        </div>
                        <h2 class="text-3xl md:text-4xl font-semibold mb-4" style="color: {{ $primaryColor }};">{{ $welcomeContent['pricing_title'] ?? 'Eenvoudige, Transparante Prijzen' }}</h2>
                        <p class="text-lg text-gray-700 dark:text-gray-300 max-w-2xl mx-auto">
                            {{ $welcomeContent['pricing_subtitle'] ?? 'Tijdens onze bètafase zijn alle functies volledig beschikbaar. Betaalde abonnementen worden geïntroduceerd wanneer we officieel lanceren.' }}
                        </p>
                    </div>
                            
                    <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                        <!-- Pricing cards would go here - using same structure as provided -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                            <div class="text-center mb-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ $welcomeContent['pricing_plan_name'] ?? 'Eenmalige Dienst' }}</h3>
                                <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                                    <span class="line-through text-gray-500 dark:text-gray-400 text-xl mr-2">€25</span>
                                    €0
                                </div>
                                <p class="text-sm text-gray-800 dark:text-gray-300">Per dienstverzoek</p>
                            </div>
                            
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check" style="color: {{ $primaryColor }};"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Directe WhatsApp-verbinding</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check" style="color: {{ $primaryColor }};"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Geverifieerde matching</span>
                                </li>
                            </ul>

                            <button class="w-full py-3 px-4 rounded-xl font-medium transition-colors text-white" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                                Momenteel Beschikbaar
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Social Proof / Stats Section -->
            <section class="py-16 px-4 sm:px-6 lg:px-8 bg-slate-50 dark:bg-gray-800">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-semibold mb-4" style="color: {{ $primaryColor }};">
                            {{ $welcomeContent['stats_title'] ?? 'Vertrouwd door huishoudens en bedrijven in Vlaanderen' }}
                        </h2>
                        <p class="text-lg text-gray-700 dark:text-gray-300 max-w-2xl mx-auto">
                            {{ $welcomeContent['stats_subtitle'] ?? 'Sluit je aan bij duizenden tevreden klanten die betrouwbare ' . $categoryNameLowerForContent . ' via ons platform hebben gevonden' }}
                        </p>
                    </div>

                    <!-- Stats -->
                    @php
                        $stats = $welcomeContent['stats'] ?? [
                            ['number' => '2.500+', 'label' => 'Tevreden Klanten'],
                            ['number' => '850+', 'label' => 'Geverifieerde ' . $siteNameCapitalized],
                            ['number' => '4.9', 'label' => 'Gemiddelde Beoordeling'],
                            ['number' => '15min', 'label' => 'Gem. Reactietijd'],
                        ];
                    @endphp
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-16">
                        @foreach($stats as $stat)
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold mb-2" style="color: {{ $primaryColor }};">{{ $stat['number'] ?? '' }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $stat['label'] ?? '' }}</div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Testimonial -->
                    @php
                        $testimonial = $welcomeContent['testimonial'] ?? [
                            'quote' => 'Fantastische service! Ik vond binnen 10 minuten een ' . $categoryNameLowerForContent . ' en hij repareerde ons probleem dezelfde dag. De WhatsApp-verbinding maakte alles zo gemakkelijk.',
                            'author' => 'Marie V.',
                            'location' => 'Brugge • Noodreparatie',
                        ];
                    @endphp
                    <div class="relative max-w-4xl mx-auto">
                        <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-8 shadow-sm">
                            <div class="flex items-center justify-center mb-6">
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                            </div>

                            <blockquote class="text-lg md:text-xl text-slate-900 dark:text-white text-center mb-6 leading-relaxed">
                                "{{ $testimonial['quote'] ?? '' }}"
                            </blockquote>

                            <div class="text-center">
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $testimonial['author'] ?? '' }}</div>
                                <div class="text-sm text-slate-600 dark:text-gray-400">{{ $testimonial['location'] ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FAQ Section -->
            <section id="support" class="py-16 px-4 sm:px-6 lg:px-8 bg-white dark:bg-gray-900">
                <div class="max-w-4xl mx-auto">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-semibold mb-4" style="color: {{ $primaryColor }};">{{ $welcomeContent['faq_title'] ?? 'Veelgestelde Vragen' }}</h2>
                        <p class="text-lg text-gray-700 dark:text-gray-300 max-w-2xl mx-auto">
                            {{ $welcomeContent['faq_subtitle'] ?? 'Alles wat je moet weten over het vinden van betrouwbare ' . $categoryNameLowerForContent . ' via ons platform' }}
                        </p>
                    </div>

                    @php
                        $faqs = $welcomeContent['faqs'] ?? [
                            [
                                'question' => 'Hoe snel kan ik een ' . $categoryNameLowerForContent . ' vinden?',
                                'answer' => 'De meeste verzoeken worden binnen 15 minuten gekoppeld aan beschikbare ' . $categoryNameLowerForContent . '. Voor nooddiensten stellen we urgente verzoeken voorrang en verbinden we je vaak binnen 10 minuten. Reactietijden kunnen variëren op basis van je locatie en tijd van de dag.'
                            ],
                            [
                                'question' => 'Hoe werkt de integratie met WhatsApp?',
                                'answer' => 'Eenmaal gekoppeld aan een ' . $categoryNameLowerForContent . ', ontvang je een directe WhatsApp-link om direct te chatten. Je kunt foto\'s delen, prijzen bespreken, afspraken plannen en updates krijgen - allemaal via WhatsApp. Je privacy is beschermd omdat we je gesprekken niet opslaan.'
                            ],
                            [
                                'question' => 'Zijn alle ' . $categoryNameLowerForContent . ' geverifieerd?',
                                'answer' => 'Ja! Elke ' . $categoryNameLowerForContent . ' op ons platform ondergaat een grondig verificatieproces, inclusief controle van licenties, verzekering, achtergrondscreening en validatie van klantbeoordelingen. We werken alleen samen met gelicentieerde en verzekerde professionals.'
                            ],
                        ];
                    @endphp
                    <div class="space-y-4 mb-12">
                        @foreach($faqs as $faq)
                        <div class="faq-item w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden rounded-2xl transition-all duration-500 ease-out cursor-pointer shadow-sm hover:shadow-md">
                            <div class="w-full px-6 py-5 flex justify-between items-center gap-5 text-left transition-all duration-300 ease-out">
                                <div class="flex-1 text-gray-900 dark:text-white text-base font-medium leading-6 break-words">{{ $faq['question'] ?? '' }}</div>
                                <div class="flex justify-center items-center">
                                    <i class="fas fa-chevron-down faq-icon text-gray-600 dark:text-gray-400 transition-all duration-500 ease-out"></i>
                                </div>
                            </div>
                            <div class="faq-content">
                                <div class="px-6 transition-all duration-500 ease-out pb-5">
                                    <div class="text-gray-600 dark:text-gray-300 text-sm font-normal leading-6 break-words">{{ $faq['answer'] ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white dark:bg-gray-900 relative overflow-hidden">
                <div class="max-w-4xl mx-auto text-center relative z-10">
                    <div class="space-y-8">
                        <div class="space-y-6">
                            <h2 class="text-4xl md:text-5xl lg:text-6xl font-semibold leading-tight" style="color: {{ $primaryColor }};">
                                {{ $welcomeContent['cta_title'] ?? 'Sluit je aan bij een netwerk van actieve ' . $categoryNameLowerForContent }}
                            </h2>
                            <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300 leading-relaxed max-w-3xl mx-auto">
                                {{ $welcomeContent['cta_subtitle'] ?? $categoryNameForContent . ' brengt jou rechtstreeks in contact met mensen die nu een ' . $categoryNameLowerForContent . ' nodig hebben.' }}
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                            <a href="{{ route('register', ['category' => $cat->code]) }}" class="px-8 py-4 text-lg font-medium rounded-xl border-2 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200 bg-transparent flex items-center">
                                <i class="fas fa-wrench mr-2"></i>
                                Registreer als {{ $siteNameCapitalized }}
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer Section -->
            <footer class="bg-slate-100 dark:bg-gray-800 border-t border-slate-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <!-- Brand Section -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center footer-logo-container" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                                    @if($logo)
                                        <img src="{{ $logo }}" alt="{{ $brandName }}" class="w-8 h-8 rounded-lg object-cover footer-logo-image" onerror="this.style.display='none'; this.parentElement.querySelector('.footer-logo-icon-fallback').style.display='flex';">
                                        <i class="fas {{ $categoryIcon }} text-white text-sm footer-logo-icon-fallback" style="display: none;"></i>
                                    @else
                                        <i class="fas {{ $categoryIcon }} text-white text-sm"></i>
                                    @endif
                                </div>
                                <span class="text-xl font-semibold text-slate-900 dark:text-white">{{ $siteNameCapitalized }}<span style="color: {{ $primaryColor }};">.{{ str_replace($siteName . '.', '', $brandDomain) }}</span></span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-gray-400 leading-relaxed">
                                {{ $welcomeContent['footer_description'] ?? 'Dit platform maakt deel uit van het diensten.pro netwerk. Diensten.pro is een platform dat betrouwbare dienstverleners samenbrengt in verschillende sectoren. Via dit netwerk kunnen gebruikers eenvoudig professionele diensten vinden, vergelijken en contacteren.' }}
                            </p>
                        </div>
                
                        <!-- For Customers -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Voor Klanten</h3>
                            <div class="space-y-2">
                                <a href="{{ route('client.register') }}" class="block text-sm text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                                    Vind een {{ $siteNameForContent }}
                                </a>
                                <a href="#how-it-works" class="block text-sm text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                                    Hoe het werkt
                                </a>
                                <a href="#services" class="block text-sm text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                                    Diensten
                                </a>
                            </div>
                        </div>
            
                        <!-- For Service Providers -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Voor {{ $siteNameCapitalized }}</h3>
                            <div class="space-y-2">
                                <a href="{{ route('register', ['category' => $cat->code]) }}" class="block text-sm text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                                    Registreer als {{ $siteNameCapitalized }}
                                </a>
                                <a href="#how-it-works" class="block text-sm text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                                    Hoe het Werkt
                                </a>
                                <a href="#pricing" class="block text-sm text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                                    Prijzen
                                </a>
                            </div>
                        </div>

                        <!-- Company -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Bedrijf</h3>
                            <div class="space-y-2">
                                <a href="#" class="block text-sm text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                                    Over Ons
                                </a>
                                <a href="#" class="block text-sm text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                                    Contact
                                </a>
                                <a href="#" class="block text-sm text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                                    Privacybeleid
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 dark:border-gray-700 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="text-sm text-slate-600 dark:text-gray-400">© {{ date('Y') }} {{ $siteNameCapitalized }}.{{ str_replace($siteName . '.', '', $brandDomain) }}. Alle rechten voorbehouden.</div>
                    </div>
                </div>
            </footer>
        </div>
    </main>

    <script>
        // OTP Input Handling
        document.addEventListener('DOMContentLoaded', function() {
            const otpInputs = document.querySelectorAll('.otp-digit-input');
            const hiddenInput = document.getElementById('otp_code');
            const otpForm = document.getElementById('otpForm');

            if (otpInputs.length > 0 && hiddenInput) {
                otpInputs.forEach((input, index) => {
                    input.addEventListener('input', function(e) {
                        const value = e.target.value.replace(/\D/g, '');
                        if (value.length > 0) {
                            e.target.value = value[0];
                            if (index < otpInputs.length - 1) {
                                otpInputs[index + 1].focus();
                            }
                        }
                        updateHiddenInput();
                    });

                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Backspace' && !e.target.value && index > 0) {
                            otpInputs[index - 1].focus();
                        }
                    });

                    input.addEventListener('paste', function(e) {
                        e.preventDefault();
                        const paste = (e.clipboardData || window.clipboardData).getData('text');
                        const digits = paste.replace(/\D/g, '').substring(0, 6);
                        digits.split('').forEach((digit, i) => {
                            if (otpInputs[i]) {
                                otpInputs[i].value = digit;
                            }
                        });
                        updateHiddenInput();
                        if (digits.length === 6) {
                            otpInputs[5].focus();
                        } else if (digits.length > 0) {
                            otpInputs[digits.length].focus();
                        }
                    });
                });

                function updateHiddenInput() {
                    const code = Array.from(otpInputs).map(input => input.value).join('');
                    hiddenInput.value = code;
                }

                otpInputs[5].addEventListener('input', function() {
                    if (hiddenInput.value.length === 6) {
                        setTimeout(() => {
                            if (otpForm) {
                                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                                if (csrfToken) {
                                    const tokenInput = otpForm.querySelector('input[name="_token"]');
                                    if (tokenInput) {
                                        tokenInput.value = csrfToken.getAttribute('content');
                                    }
                                }
                                otpForm.submit();
                            }
                        }, 300);
                    }
                });
                
                if (otpForm) {
                    otpForm.addEventListener('submit', function(e) {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (csrfToken) {
                            const tokenInput = otpForm.querySelector('input[name="_token"]');
                            if (tokenInput) {
                                tokenInput.value = csrfToken.getAttribute('content');
                            } else {
                                const tokenInput = document.createElement('input');
                                tokenInput.type = 'hidden';
                                tokenInput.name = '_token';
                                tokenInput.value = csrfToken.getAttribute('content');
                                otpForm.appendChild(tokenInput);
                            }
                        }
                    });
                }

                otpInputs[0].focus();
            }

            // Initialize intl-tel-input
            const whatsappInput = document.getElementById('whatsapp_number');
            if (whatsappInput) {
                const iti = window.intlTelInput(whatsappInput, {
                    initialCountry: "be",
                    preferredCountries: ["be", "nl", "fr", "de", "uk"],
                    separateDialCode: false,
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js"
                });

                whatsappInput.addEventListener('input', function() {
                    const fullNumber = iti.getNumber();
                    const hiddenInput = document.getElementById('whatsapp_number_full');
                    if (hiddenInput) {
                        hiddenInput.value = fullNumber;
                    }
                });

                whatsappInput.addEventListener('countrychange', function() {
                    const fullNumber = iti.getNumber();
                    const hiddenInput = document.getElementById('whatsapp_number_full');
                    if (hiddenInput) {
                        hiddenInput.value = fullNumber;
                    }
                });

                const whatsappForm = document.getElementById('whatsappForm');
                if (whatsappForm) {
                    whatsappForm.addEventListener('submit', function(e) {
                        const fullNumber = iti.getNumber(intlTelInputUtils.numberFormat.E164);
                        if (fullNumber) {
                            whatsappInput.value = fullNumber;
                        }
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (csrfToken && !whatsappForm.querySelector('input[name="_token"]')) {
                            const tokenInput = document.createElement('input');
                            tokenInput.type = 'hidden';
                            tokenInput.name = '_token';
                            tokenInput.value = csrfToken.getAttribute('content');
                            whatsappForm.appendChild(tokenInput);
                        }
                    });
                }
            }
        });

        // Navbar scroll effect
        let ticking = false;

        function updateNavbar() {
            const navbar = document.getElementById('navbar');
            const scrollY = window.pageYOffset;

            if (scrollY > 50) {
                navbar.classList.remove('nav-default');
                navbar.classList.add('nav-scrolled');
            } else {
                navbar.classList.remove('nav-scrolled');
                navbar.classList.add('nav-default');
            }

            ticking = false;
        }

        function requestTick() {
            if (!ticking) {
                requestAnimationFrame(updateNavbar);
                ticking = true;
            }
        }

        window.addEventListener('scroll', requestTick, { passive: true });

        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', function() {
                const isHidden = mobileMenu.classList.contains('hidden');

                if (isHidden) {
                    mobileMenu.classList.remove('hidden');
                    menuIcon.className = 'fas fa-times text-xl';
                    document.body.style.overflow = 'hidden';
                } else {
                    mobileMenu.classList.add('hidden');
                    menuIcon.className = 'fas fa-bars text-xl';
                    document.body.style.overflow = '';
                }
            });
        }

        document.addEventListener('click', function(event) {
            if (mobileMenu && !mobileMenu.classList.contains('hidden') &&
                !mobileMenu.contains(event.target) &&
                !mobileMenuButton.contains(event.target)) {

                mobileMenu.classList.add('hidden');
                menuIcon.className = 'fas fa-bars text-xl';
                document.body.style.overflow = '';
            }
        });

        // FAQ Accordion
        document.addEventListener('DOMContentLoaded', function() {
            const faqItems = document.querySelectorAll('.faq-item');

            faqItems.forEach(item => {
                const header = item.querySelector('.px-6.py-5');
                
                if (header) {
                    header.addEventListener('click', () => {
                        const isActive = item.classList.contains('active');
                        
                        // Close all items
                        faqItems.forEach(otherItem => {
                            otherItem.classList.remove('active');
                        });
                        
                        // Open clicked item if it was closed
                        if (!isActive) {
                            item.classList.add('active');
                        }
                    });
                }
            });
        });

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 96;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
    
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>

