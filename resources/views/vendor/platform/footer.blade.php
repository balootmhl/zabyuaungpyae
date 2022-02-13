@guest
    {{-- <p>Crafted with <span class="me-1">❤️</span> by Alexandr Chernyaev</p> --}}
@else

    <div class="text-center user-select-none">
        <p class="small m-0">
            {{-- {{ __('The application code is published under the MIT license.') }} --}}&copy; Mahar Shin {{date('Y')}}<br>
            <a href="" target="_blank" rel="noopener">
                {{ __('Version') }}: {{\Orchid\Platform\Dashboard::VERSION}}
            </a>. All Rights Reserved.
        </p>
    </div>
@endguest
