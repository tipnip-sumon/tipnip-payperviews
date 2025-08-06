<input {{ $attributes->merge(['class' => 'form-control']) }} type="{{ $type ?? 'text' }}" name="{{ $name ?? '' }}" id="{{ $id ?? '' }}" value="{{ $value ?? '' }}" {{ $attributes->except(['class', 'type', 'name', 'id', 'value']) }}>
@if ($errors->has($name ?? ''))
    <span class="text-danger">{{ $errors->first($name ?? '') }}</span>
@endif
