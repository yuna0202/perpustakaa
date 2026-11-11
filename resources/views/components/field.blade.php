@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null, 'required' => false])
<div>
    <label for="{{ $name }}" class="field-label">{{ $label }}</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" @if($type !== 'file' && $type !== 'password') value="{{ old($name, $value) }}" @endif @required($required) @if($hint || $errors->has($name)) aria-describedby="{{ $name }}-description" @endif @if($errors->has($name)) aria-invalid="true" @endif {{ $attributes->class(['field', 'border-red-500' => $errors->has($name)]) }}>
    <div id="{{ $name }}-description">
        @if($hint)<p class="field-hint">{{ $hint }}</p>@endif
        @error($name)<p class="field-error" role="alert">{{ $message }}</p>@enderror
    </div>
</div>
