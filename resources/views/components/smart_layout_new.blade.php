<!-- SMART LAYOUT DEBUG: Template is being processed -->
@php
    // Get device detection from middleware
    $isMobile = request()->attributes->get('is_mobile', false);
    $deviceType = request()->attributes->get('device_type', 'desktop');
    
    // Initialize variables
    $userAgent = request()->header('User-Agent', '');
    $screenWidth = request()->header('X-Screen-Width');

    // Fallback to manual detection if middleware didn't run
    if (!request()->attributes->has('is_mobile')) {
        $isMobile = false;
        // Check for mobile patterns in user agent
        $mobilePatterns = [
            '/android/i',
            '/webos/i',
            '/iphone/i',
            '/ipad/i',
            '/ipod/i',
            '/blackberry/i',
            '/iemobile/i',
            '/mobile/i',
            '/phone/i'
        ];
        
        foreach ($mobilePatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                $isMobile = true;
                break;
            }
        }
        
        // Also check for small screen width if available
        if ($screenWidth && $screenWidth <= 991) {
            $isMobile = true;
        }
    }
    
    // Debug logging
    if (config('app.debug')) {
        \Log::info('Smart Layout Detection', [
            'user_agent' => $userAgent,
            'is_mobile' => $isMobile,
            'device_type' => $deviceType,
            'screen_width' => $screenWidth,
        ]);
    }
@endphp

@if($isMobile)
    {{-- Load Mobile Layout with all content --}}
    <x-mobile_layout>
        {{ $slot }}
    </x-mobile_layout>
@else
    {{-- Load Desktop Layout with all content --}}
    <x-desktop_layout>
        {{ $slot }}
    </x-desktop_layout>
@endif
