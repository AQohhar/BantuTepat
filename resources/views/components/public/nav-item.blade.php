@php
    $currentPage = request()->routeIs($routeName);
    $currentSection = ($routeName === 'program.index' && request()->routeIs('program.show'))
        || ($routeName === 'demo.workspace.index' && request()->routeIs('demo.workspace.login', 'demo.program', 'demo.proposal', 'demo.ticket', 'demo.ticket.revision'));
@endphp

<li>
    <a href="{{ route($routeName) }}" @class(['nav-link', 'nav-link-active' => $currentPage || $currentSection, 'nav-link-primary' => $prominent ?? false])
        @if ($currentPage) aria-current="page" @elseif ($currentSection) aria-current="true" @endif>
        {{ $label }}
    </a>
</li>
