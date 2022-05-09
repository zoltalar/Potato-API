@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header')
{{ __('messages.codename') }}
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ __('messages.codename') }}. @lang('phrases.all_rights_reserved').
@endcomponent
@endslot
@endcomponent
