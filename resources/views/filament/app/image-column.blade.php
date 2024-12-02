<div>
    @if(!empty($getRecord()->image_url))
        <img src="{{asset('storage/' . $getRecord()->image_url)}}"/>
    @else
        <img src="{{asset('storage/' . 'car_placeholder.png')}}"/>
    @endif
</div>
