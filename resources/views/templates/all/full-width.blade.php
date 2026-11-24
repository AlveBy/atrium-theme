{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{-- Template: atrium-theme::theme.template_full_width --}}
{{--
    Any record of any module, drawn as it is without a template but across the
    whole width of the window: the view the record would have is $defaultView,
    the controller names it. The class of <main> is the theme layout's own
    place for that, see main.wrap-full in the stylesheet.
--}}
@extends($defaultView ?? 'atrium-theme::page')

@section('main-class', 'wrap-full')
