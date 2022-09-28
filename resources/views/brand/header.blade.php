@push('head')
    {{-- @if(auth()->user()->id == 1) --}}
        <link href="{{ asset('custom/img/logo.png') }}" id="favicon" rel="icon">
    {{-- @else
        <link href="{{ asset('custom/img/zabyuaungpyae-logo.jpeg') }}" id="favicon" rel="icon">
    @endif --}}
@endpush

<p class="h2 n-m font-thin v-center">
    {{-- <x-orchid-icon path="screen-smartphone"/> --}}
    
    <span class="m-l d-none d-sm-block">
        {{-- MaharShin
        <small class="v-top opacity">Beta</small> --}}
        {{-- @if(auth()->user()->id == 1) --}}
            <img src="{{ asset('custom/img/logo.png') }}" alt="" style="height: 150px;width:auto;text-align: center;">
        {{-- @else
            <img src="{{ asset('custom/img/zabyuaungpyae-logo.jpeg') }}" alt="" style="height: 150px;width:auto;text-align: center;border-radius:30%;">
        @endif --}}
    </span>

</p>
