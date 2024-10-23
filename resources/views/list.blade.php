<form method="GET" action="{{ url()->current() }}" class="row g-3 mb-3">
    <div class="col-auto">
        @if($defaultActionButton)
            <a href="{{ $defaultActionButton['url'] }}" class="{{ $defaultActionButton['class'] }}">
                @if($defaultActionButton['icon'])
                    <i class="{{ $defaultActionButton['icon'] }}"></i>
                @endif
                {{ $defaultActionButton['label'] }}
            </a>
        @endif
    </div>
    <div class="col-auto ms-auto">
        <input type="text" name="search" class="form-control" placeholder="{{ $searchPlaceholder }}"
               value="{{ $request->input('search') }}">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary mb-3">
            @if($searchButtonIcon)
                <i class="{{ $searchButtonIcon }}"></i>
            @endif
            {{ $searchButtonLabel }}
        </button>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr>
            @foreach($columns as $details)
                <th>{{ $details['label'] }}</th>
            @endforeach
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                @foreach($columns as $field => $details)
                    @php
                        $value = $getColumnValue($item, $field);
                    @endphp
                    <td>
                        @if(is_null($value) || $value === '')
                            -
                        @else
                            @if($details['type'] === 'date')
                                {{ \Carbon\Carbon::parse($value)->format('d/m/Y') }}
                            @elseif($details['type'] === 'datetime')
                                {{ \Carbon\Carbon::parse($value)->format('d/m/Y H:i') }}
                            @elseif($details['type'] === 'boolean')
                                @if((bool) $value === true)
                                    <span class="badge bg-success">Sim</span>
                                @else
                                    <span class="badge bg-danger">Não</span>
                                @endif
                            @else
                                {{ htmlspecialchars($value) }}
                            @endif
                        @endif
                    </td>
                @endforeach
                <td>
                    @foreach($rowActionButtons as $button)
                        @if($button['method']  === 'GET')
                            <a href="{{ $routeFunction($button['routeName'], [$item->id]) }}"
                               class="{{ $button['class'] }}">
                                @if($button['icon'])
                                    <i class="{{ $button['icon'] }}"></i>
                                @endif
                                {{ $button['label'] }}
                            </a>
                            @continue
                        @endif
                        <form method="POST" action="{{ $routeFunction($button['routeName'], [$item->id]) }}"
                              style="display:inline;"
                              @if($button['confirm']) onsubmit="return confirmDelete(this);" @endif>
                            {!! csrf_field() !!}
                            {!! method_field($button['method']) !!}
                            <button type="submit" class="{{ $button['class'] }}">
                                @if($button['icon'])
                                    <i class="{{ $button['icon'] }}"></i>
                                @endif
                                {{ $button['label'] }}
                            </button>
                        </form>
                    @endforeach
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center">
    {{ $items->appends($request->input())->links() }}
</div>

<script>
    function confirmDelete(form) {
        event.preventDefault();
        Swal.fire({
            title: "Você tem certeza?",
            text: "Esta ação não poderá ser desfeita!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sim, continuar!",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return false;
    }
</script>
