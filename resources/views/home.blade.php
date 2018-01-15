@extends('layouts.default')

@section('content')
	<div class="hero">
		
		<div class="hero-information" style="height:30%">

			<div class="container">
			
				@if ( count($events) >= 1)
					@foreach ( $events as $event )
						<div class="col-xs-12 col-sm-6 col-lg-4">
							<div class="hero-information__pre-title">
								Next LAN
							</div>
							<div class="hero-information__title">
								<h1>{{ $event->display_name }}</h1>
							</div>

							<div class="hero-information__from-date">
								<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Start Date: {{ date("d-m-Y H:i", strtotime($event->start)) }}				
							</div>

							<div class="hero-information__to-date">
								<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> End Date: {{ date("d-m-Y H:i", strtotime($event->end)) }}
							</div>
							<a href="/events/{{$event->slug}}#purchaseTickets">
								<button type="button" class="btn btn-primary btn-lg">
            			Book Now!
            		</button>
          		</a>
						</div>
						<div class="col-xs-12 col-sm-6 col-lg-4 col-lg-push-4 hidden-xs">
							<div class="hero-information__pre-title">
								<h1>2018 Dates</h1>
							</div>
							<br>
							<div class="hero-information__from-date">
								<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> March 16th - 18th				
							</div>
							<div class="hero-information__from-date">
								<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> May 18th - 20th		
							</div>
							<div class="hero-information__from-date">
								<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> September 7th - 9th				
							</div>
							<div class="hero-information__from-date">
								<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> November 9th - 11th			
							</div>
						</div>
					@endforeach
				@else
					<div class="hero-information__title">
						<h2>There are currently no events.</h2>
					</div>
				@endif
			</div>
		</div>
	</div><!-- end .hero -->
	<div class="book-now  text-center hidden-xs">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					@foreach ( $events as $event )
						<h3>Want to get in on the action <a href="/events/{{$event->slug}}" class="text-info">Book Now</a></h3>
					@endforeach
				</div><!-- end .col-xs-12 -->
			</div><!-- end .row -->
		</div><!-- end .container -->
	</div><!-- end .book-now -->

	<div class="news  section-padding  section-margin" hidden>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="text-center">
						<h2 class="section-heading  text-center">Latest News</h2>
					</div>
				</div><!-- end .col-xs-12 -->
				<div class="row">
					@foreach($news as $news_article)
						<div class="col-md-4  news-article__container">
							<div class="news-article">
								<h3>{{$news_article->title}}</h3>
								<div class="pull-right">{{date('d M Y', strtotime($news_article->created_at))}}</div>
								<div class="">posted by: {{$news_article->username}}</div>
								<p>{{$news_article->article}}</p>
								<div class="hidden"><a href="#">read more</a></div>
							</div>
						</div><!-- end .col-md-4 .news-article__container -->
					@endforeach
				</div>
			</div><!-- end .row -->
		</div><!-- end .container -->
	</div><!-- end .news .section-padding .section-margin -->

	<div class="stats  section-padding  section-margin">
		<div class="container">
			<div class="row">
				<div class="col-md-4  text-center">
					<div class="stats-number">
						{{ Helpers::getEventTotal() }}
					</div>
					<hr />
					<div class="stats-title">
						LANs we've hosted
					</div>
				</div><!-- end .col-md-4 -->

				<div class="col-md-4  text-center">
					<div class="stats-number">
						{{ Helpers::getEventParticipantTotal() }}
					</div>
					<hr />
					<div class="stats-title">
						GAMERs we've entertained
					</div>
				</div><!-- end .col-md-4 -->

				<div class="col-md-4  text-center">
					<div class="stats-number">
						A LOT
					</div>
					<hr />
					<div class="stats-title">
						PIZZAs we've ordered
					</div>
				</div><!-- end .col-md-4 -->
			</div><!-- end .row -->
		</div><!-- end .container -->
	</div><!-- end .stats  .section-padding  .section-margin -->

	<div class="about  section-padding  section-margin">
		<div class="container">
			<div class="row">
				<div class="col-md-8  col-md-offset-2 text-center">
					<div class="text-center">
						<h2 class="section-heading  text-center">All About {{ Settings::getOrgName() }}</h2>
					</div>
					{!! Settings::getAboutShort() !!}
				</div><!-- end .col-md-8 .col-md-offset-2 -->
			</div><!-- end .row -->
		</div><!-- end .container -->
	</div><!-- end .about -->

@endsection