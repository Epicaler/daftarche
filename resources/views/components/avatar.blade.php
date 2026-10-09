@props(['person', 'size' => 'size-11'])

<span {{ $attributes->merge(['class' => "$size inline-flex shrink-0 items-center justify-center rounded-full text-sm font-bold text-ink/80 ring-2 ring-white"]) }}
      style="background-color: {{ $person->avatar_color }}">{{ $person->initials }}</span>
