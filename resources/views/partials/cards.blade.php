{{-- Records as cards: picture, date, title, short text. --}}
<ul class="cards">
    @foreach ($records as $record)
        @continue(! ($url = $record->url()))
        <li class="card">
            @if ($image = $record->thumbnail())
                <img src="{{ $image }}" alt="" loading="lazy">
            @endif
            <div class="card-body">
                @if ($date = $record->date())
                    <p class="date"><time datetime="{{ $date->toAtomString() }}">{{ $date->isoFormat('LL') }}</time></p>
                @endif
                <{{ $heading }}><a href="{{ $url }}">{{ $record->title() }}</a></{{ $heading }}>
                @if ($teaser = $record->teaser())
                    <div class="card-text">{!! $teaser !!}</div>
                @endif
            </div>
        </li>
    @endforeach
</ul>
