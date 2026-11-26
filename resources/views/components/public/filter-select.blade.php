@props(['name', 'label', 'options', 'selected', 'errors', 'empty' => null])

<div class="form-field">
    <label for="{{ $name }}">{{ $label }}</label>
    <select name="{{ $name }}" id="{{ $name }}"
        @if ($errors->has($name)) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif>
        @if ($empty !== null)
            <option value="">{{ $empty }}</option>
        @endif
        @foreach ($options as $value => $optionLabel)
            <option value="{{ $value }}" @selected($selected === $value)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if ($errors->has($name))
        <p class="field-error" id="{{ $name }}-error">{{ $errors->first($name) }}</p>
    @endif
</div>
