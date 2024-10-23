<form action="{{ $actionUrl }}" method="{{ $method === 'GET' ? 'GET' : 'POST' }}" @if($useAjax) id="ajaxForm"
      data-cancel-url="{{ $cancelUrl }}" @endif>
    {!! csrf_field() !!}
    @if($method !== 'GET' && $method !== 'POST')
        <input type="hidden" name="_method" value="{{ $method }}">
    @endif

    @foreach($panels as $panel)
        @if (empty($panel['fields']))
            @continue
        @endif

        @php
            $showRules = $panel['options']['show-rules'] ?? null;
            $shouldHide = $showRules ? 'style=display:none;' : '';
        @endphp
        <div class="card mb-4" data-panel-id="{{ $panel['label'] }}" {{ $shouldHide }}>
            <div class="card-header">
                <h5 class="card-title">{{ $panel['label'] }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($panel['fields'] as $name => $details)
                        @php
                            $value = $formData[$name] ?? '';
                            $showRules = $details['options']['show-rules'] ?? null;
                            $shouldHide = $showRules ? 'style="display:none;"' : '';
                            $mask = $details['options']['mask'] ?? null;
                            $colClass = isset($details['options']['col']) ? 'col-' . $details['options']['col'] : 'col-12';
                            $label = $details['label'] ?? null
                        @endphp
                        <div class="{{ $colClass }} mb-3" id="field-{{ $name }}" {{ $shouldHide }}>
                            @if($label)
                                <label for="{{ $name }}" class="form-label">{{ $label }}:</label>
                            @endif

                            @switch($details['type'])
                                @case('text')
                                @case('email')
                                @case('date')
                                    <input type="{{ $details['type'] }}"
                                           class="form-control {{ $mask ? 'mask-'.$mask : '' }}"
                                           id="{{ $name }}" name="{{ $name }}"
                                           value="{{ htmlspecialchars($value) }}">
                                    @break
                                @case('number')
                                    <input type="number"
                                           class="form-control {{ $mask ? 'mask-'.$mask : '' }}"
                                           id="{{ $name }}" name="{{ $name }}"
                                           step="{{ $details['options']['step'] ?? "" }}"
                                           value="{{ htmlspecialchars($value) }}">
                                    @break
                                @case('password')
                                    <input type="{{ $details['type'] }}" class="form-control" id="{{ $name }}"
                                           name="{{ $name }}">
                                    @break
                                @case('textarea')
                                    <textarea class="form-control" id="{{ $name }}" name="{{ $name }}"
                                              maxlength="{{ $details['maxChars'] }}">{{ htmlspecialchars($value) }}</textarea>
                                    <div id="{{ $name }}-char-count" class="form-text">{{ strlen($value) }}
                                        /{{ $details['maxChars'] }}</div>
                                    @break
                                @case('select')
                                    <select class="form-control" id="{{ $name }}" name="{{ $name }}">
                                        <option value=""></option>
                                        @foreach($details['values'] as $index => $item)
                                            <option
                                                value="{{ $index }}" {{ $value == $index ? 'selected' : '' }}>{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @break
                                @case('multiselect')
                                    <select class="form-control" id="{{ $name }}" name="{{ $name }}[]" multiple>
                                        @foreach($details['values'] as $index => $item)
                                            <option
                                                value="{{ $index }}" {{ in_array($index, (array)$value) ? 'selected' : '' }}>{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @break
                                @case('radio')
                                    @foreach($details['values'] as $index => $item)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="{{ $name }}"
                                                   id="{{ $name }}-{{ $index }}"
                                                   value="{{ $index }}" {{ $value == $index ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                   for="{{ $name }}-{{ $index }}">{{ $item }}</label>
                                        </div>
                                    @endforeach
                                    @break
                                @case('show')
                                    <div class="form-control-plaintext"
                                         id="{{ $name }}">{{ htmlspecialchars($details['data']) }}</div>
                                    @break
                                @case('file')
                                    <input type="file"
                                           class="form-control {{ $mask ? 'mask-'.$mask : '' }}"
                                           id="{{ $name }}" name="{{ $name }}">
                                    @break
                                @case('hidden')
                                    <input type="hidden" id="{{ $name }}" name="{{ $name }}"
                                           value="{{ htmlspecialchars($value) }}">
                                    @break
                            @endswitch
                            <div class="invalid-feedback" id="error-{{ $name }}"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    <div class="d-flex justify-content-end">
        <a href="{{ $cancelUrl }}" class="btn btn-secondary me-2">{{ $cancelButtonLabel }}</a>
        <button type="submit" class="btn btn-primary">{{ $saveButtonLabel }}</button>
    </div>
</form>


@if($useAjax)
    <script>
        document.getElementById("ajaxForm").addEventListener("submit", function (event) {
            event.preventDefault();
            let errorElements = document.getElementsByClassName("invalid-feedback");
            for (let errorElement of errorElements) {
                errorElement.style.display = "none";
            }
            let form = event.target;
            let formData = new FormData(form);
            let method = form.method;
            for (let [key, value] of formData.entries()) {
                if (value === null || value === "") {
                    formData.delete(key);
                }
            }
            fetch(form.action, {
                method: method,
                headers: {
                    "X-CSRF-TOKEN": formData.get("_token"),
                    "Accept": "application/json"
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.errors) {
                        for (let field in data.errors) {
                            let errorElement = document.getElementById("error-" + field);
                            if (errorElement) {
                                errorElement.textContent = data.errors[field][0];
                                errorElement.style.display = "block";
                            }
                        }
                    } else if (data.success) {
                        if (data.url) {
                            window.location.href = data.url;
                        } else {
                            window.location.href = form.getAttribute("data-cancel-url");
                        }
                    }
                })
                .catch(error => console.error("Error:", error));
        });
    </script>
@endif

<script>
    document.querySelectorAll("textarea[maxlength]").forEach(function (textarea) {
        textarea.addEventListener("input", function () {
            let maxChars = textarea.getAttribute("maxlength");
            let currentLength = textarea.value.length;
            let charCountElement = document.getElementById(textarea.id + "-char-count");
            charCountElement.textContent = currentLength + "/" + maxChars;
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const panels = @json($panels);

        // Verifica se panels é um objeto
        if (typeof panels !== 'object' || panels === null) {
            console.error('panels is not a valid object', panels);
            return;
        }

        function checkShowRules() {
            // Itera sobre as chaves do objeto panels
            Object.keys(panels).forEach(key => {
                let panel = panels[key]; // Acessa o painel
                let panelElement = document.querySelector(`[data-panel-id="${key}"]`);
                let panelShowRules = panel.options && panel.options['show-rules'];
                let shouldShowPanel = true;

                if (panelShowRules) {
                    for (let field in panelShowRules) {
                        let fieldElement = document.querySelector(`[name="${field}"]:checked`);
                        let fieldValue = fieldElement ? fieldElement.value : document.querySelector(`[name="${field}"]`).value;

                        if (Array.isArray(panelShowRules[field]) && !panelShowRules[field].includes(fieldValue)) {
                            shouldShowPanel = false;
                            break;
                        }

                        if (fieldValue != panelShowRules[field] && !Array.isArray(panelShowRules[field])) {
                            shouldShowPanel = false;
                            break;
                        }
                    }
                    panelElement.style.display = shouldShowPanel ? 'block' : 'none';
                }

                if (shouldShowPanel) {
                    for (let name in panel.fields) {
                        let details = panel.fields[name];
                        if (details.options && details.options['show-rules']) {
                            let showRules = details.options['show-rules'];
                            let shouldShowField = true;
                            for (let field in showRules) {
                                let fieldElement = document.querySelector(`[name="${field}"]:checked`);
                                let fieldValue = fieldElement ? fieldElement.value : document.querySelector(`[name="${field}"]`).value;

                                if (Array.isArray(showRules[field]) && !showRules[field].includes(fieldValue)) {
                                    shouldShowField = false;
                                    break;
                                }

                                if (fieldValue != showRules[field] && !Array.isArray(showRules[field])) {
                                    shouldShowField = false;
                                    break;
                                }
                            }
                            let fieldToShow = document.getElementById('field-' + name);
                            fieldToShow.style.display = shouldShowField ? 'block' : 'none';
                        }
                    }
                }
            });
        }

        document.querySelectorAll('input, select, textarea').forEach(function (input) {
            input.addEventListener('change', checkShowRules);
        });

        checkShowRules();
    });
</script>
