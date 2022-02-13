@component($typeForm, get_defined_vars())
    <table class="matrix table table-bordered"
           data-controller="matrix"
           data-matrix-index="{{ $index }}"
           data-matrix-rows="{{ $maxRows }}"
           data-matrix-key-value="{{ var_export($keyValue) }}" style="border-color:#ccc !important;border-right: 1px solid #ccc !important;"
    > {{-- removed class - border-right-0 --}}
        <thead>
        <tr style="border-color:#ccc;">
            @foreach($columns as $key => $column)
                <th scope="col" class="text-capitalize" style="border-bottom: 1px solid #ccc !important;color: #667780 !important;">
                    {{ is_int($key) ? $column : $key }}
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody>

        @foreach($value as $key => $row)
            @include('platform::partials.fields.matrixRow',['row' => $row, 'key' => $key])
        @endforeach

        <tr class="add-row" style="border-color:#ccc;">
            <th colspan="{{ count($columns) }}" class="text-center p-0">
                <a href="#" data-action="matrix#addRow" class="btn btn-block small text-muted" style="transition: .5s;">
                    <x-orchid-icon path="plus-alt"/>

                    <span>{{ __('Add New') }}</span>
                </a>
            </th>
        </tr>

        <template>
            @include('platform::partials.fields.matrixRow',['row' => [], 'key' => '{index}'])
        </template>
        </tbody>
    </table>
@endcomponent
