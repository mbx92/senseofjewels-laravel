@php
    // Map setting keys → CSS custom property names
    $colorMap = [
        'theme_primary'         => '--color-primary',
        'theme_secondary'       => '--color-secondary',
        'theme_accent'          => '--color-accent',
        'theme_neutral'         => '--color-neutral',
        'theme_neutral_content' => '--color-neutral-content',
        'theme_base_100'        => '--color-base-100',
        'theme_base_200'        => '--color-base-200',
        'theme_base_300'        => '--color-base-300',
        'theme_base_content'    => '--color-base-content',
    ];

    $hasOverrides = collect($themeColors ?? [])->filter()->isNotEmpty();
@endphp

@if($hasOverrides)
<style>
[data-theme="bali-craft"] {
    @foreach($colorMap as $settingKey => $cssVar)
    @php $val = $themeColors[$settingKey] ?? null; @endphp
    @if($val && preg_match('/^#[0-9a-fA-F]{6}$/', $val))
    {{ $cssVar }}: {{ $val }};
    @endif
    @endforeach
}
</style>
@endif
