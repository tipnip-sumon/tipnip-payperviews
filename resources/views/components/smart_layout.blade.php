<!-- FORCE DESKTOP LAYOUT FOR ALL DEVICES -->
@php
    // Force desktop layout for all devices - no mobile detection needed
    // This ensures consistent experience across all devices
@endphp

{{-- Always use desktop layout regardless of device type --}}
<x-desktop_layout>
    {{ $slot }}
</x-desktop_layout>
