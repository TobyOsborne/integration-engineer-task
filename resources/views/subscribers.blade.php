<x-layout>
	<x-slot name="title">
		{{$title}}
    </x-slot>
    <div>
		<ul class="notices"></ul>
		<table id="subscribers-table" data-lang-delete="{{__('views.delete')}}" data-per-page="{{$per_page ? $per_page : 10}}">
			<thead>
				<tr>
					<th>{{__('views.email')}}</th>
					<th>{{__('views.name')}}</th>
					<th>{{__('views.country')}}</th>
					<th>{{__('views.subscription_date')}}</th>
					<th>{{__('views.subscription_time')}}</th>
					<th>{{__('views.actions')}}</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
    </div>
</x-layout>
       

