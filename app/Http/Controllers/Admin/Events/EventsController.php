<?php

namespace App\Http\Controllers\Admin\Events;

use DB;
use Auth;
use Session;

use App\User;
use App\Event;
use App\EventParticipant;
use App\EventTicket;
use App\EventAnnoucement;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class EventsController extends Controller
{
	/**
	 * Show Events Page Index
	 * @return View
	 */
	public function index()
	{
		$user = Auth::user();
		$events = Event::withoutGlobalScopes()->get();
		return view('admin.events.index')->withUser($user)->withEvents($events);
	}

	/**
	 * Show Events Page
	 * @param  Event  $event
	 * @return View
	 */
	public function show(Event $event)
	{
		$user = Auth::user();
		$events = Event::withoutGlobalScopes();
		return view('admin.events.show')->withUser($user)->withEvent($event)->withEvents($events);
	}

	/**
	 * Add Event to Database
	 * @param  Request $request
	 * @return Redirect
	 */
	public function store(Request $request)
	{
		$rules = [
			'event_name'	=> 'required|unique:events,display_name',
			'desc_short'	=> 'required',
			'desc_long'		=> 'required',
			'end_date'		=> 'required|date_format:Y-m-d',
			'end_time'		=> 'required|date_format:H:i:s',
			'start_date'	=> 'required|date_format:Y-m-d',
			'start_time'	=> 'required|date_format:H:i:s',
		];
		$messages = [
			'event_name.required'	=> 'Event name is required',
			'start_date.required'	=> 'Start date is required',
			'start_time.required'	=> 'Start date is required',
			'end_date.required'		=> 'End date is required',
			'end_time.required'		=> 'End date is required',
			'end_date.date_format'	=> 'End Date must be Y-m-d format',
			'end_time.date_format'	=> 'End Time must be H:i:s formate',
			'end_date.date_format'	=> 'Start Date must be Y-m-d format',
			'end_time.date_format'	=> 'Start Time must be H:i:s format',
			'desc_short.required'	=> 'Short Description is required',
			'desc_long.required'	=> 'Long Description is required',
		];
		$this->validate($request, $rules, $messages);

		$event 						= new Event();
		$event->display_name		= $request->event_name;
		$event->nice_name			= strtolower(str_replace(' ', '-', $request->event_name));
		$event->start				= date("Y-m-d H:i:s", strtotime($request->start_date . $request->start_time));
		$event->end					= date("Y-m-d H:i:s", strtotime($request->end_date . $request->end_time));
		$event->desc_long			= $request->desc_long;
		$event->desc_short			= $request->desc_short;
		$event->event_venue_id		= @$request->venue;

		if (!$event->save()) {
			Session::flash('alert-danger', 'Cannot Save Event!');
			return Redirect::to('admin/events/' . $event->slug);
		}
		Session::flash('alert-success', 'Successfully saved Event!');
		return Redirect::to('admin/events/' . $event->slug);
	}
	
	/**
	 * Update Event
	 * @param  Event   $event
	 * @param  Request $request
	 * @return Redirect
	 */
	public function update(Event $event, Request $request)
	{
		$rules = [
			'event_name'		=> 'filled',
			'end_date'			=> 'filled|date_format:Y-m-d',
			'end_time'			=> 'filled|date_format:H:i:s',
			'start_date'		=> 'filled|date_format:Y-m-d',
			'start_time'		=> 'filled|date_format:H:i:s',
			'status'			=> 'in:draft,preview,published,private',
			'essential_info'	=> 'filled',
		];
		$messages = [
			'event_name.filled'			=> 'Event Name cannot be empty',
			'end_date.filled'			=> 'A End Date cannot be empty',
			'end_date.date_format'		=> 'A End Date must be Y-m-d format',
			'end_time.filled'			=> 'A End Time cannot be empty',
			'end_time.date_format'		=> 'A End Time must be H:i:s formate',
			'start_date.filled'			=> 'A Start Date cannut be empty',
			'end_date.date_format'		=> 'A Start Date must be Y-m-d format',
			'start_time.filled'			=> 'A Start Time cannot be empty',
			'end_time.date_format'		=> 'A Start Time must be H:i:s format',
			'status.in' 				=> 'Status must be draft, preview, published or private',
			'essential_info.filled'		=> 'Essetntial Information cannot be empty',
		];
		$this->validate($request, $rules, $messages);

		if (isset($request->event_name)) {
			$event->display_name	= $request->event_name;
			$event->nice_name		= strtolower(str_replace(' ', '-', $request->event_name));
		}

		if (isset($request->start_date)) {
			$start = $request->start_date . date("H:i:s", strtotime($event->start));
			$event->start			= date("Y-m-d H:i:s", strtotime($start));
		}

		if (isset($request->start_time)) {
			$start = date("Y-m-d", strtotime($event->start)) . $request->start_time;
			$event->start			= date("Y-m-d H:i:s", strtotime($start));
		}

		if (isset($request->end_date)) {
			$end = $request->end_date . date("H:i:s", strtotime($event->end));
			$event->end				= date("Y-m-d H:i:s", strtotime($end));
		}

		if (isset($request->end_time)) {
			$end = date("Y-m-d", strtotime($event->end)) . $request->end_time;
			$event->end				= date("Y-m-d H:i:s", strtotime($end));
		}

		if (isset($request->desc_long)) {
			$event->desc_long		= $request->desc_long;
		}

		if (isset($request->desc_short)) {
			$event->desc_short		= $request->desc_short;
		}

		if (isset($request->status)) {
			$event->status			= $request->status;
		}

		if (isset($request->essential_info)) {
			$event->essential_info = $request->essential_info; 
		}

		if (!$event->save()) {
			Session::flash('alert-danger', 'Cannot update Event!');
			return Redirect::to('admin/events/' . $event->slug);
		}

		Session::flash('alert-success', 'Successfully updated Event!');
		return Redirect::to('admin/events/' . $event->slug);
	}

	/**
	 * Delete Event from Database
	 * @param  Event  $event
	 * @return Redirect
	 */
	public function destroy(Event $event)
	{
		if (!$event->eventParticipants->isEmpty()) {
			Session::flash('alert-danger', 'Cannot delete event with participants!');
			return Redirect::back();
		}

		if (!$event->delete()) {
			Session::flash('alert-danger', 'Cannot delete Event!');
			return Redirect::back();
		}

		Session::flash('alert-success', 'Successfully deleted Event!');
		return Redirect::to('admin/events/');
	}

	/**
	 * Add Gift Participant
	 * @param  Request $request
	 * @param  Event   $event
	 * @return Redirect
	 */
	public function freeGift(Request $request, Event $event)
	{
		$participant							= new EventParticipant;
		$participant->user_id					= $request->user_id;
		$participant->event_id					= $event->id;
		$participant->free						= 1;
		$participant->staff_free_assigned_by	= Auth::id();
		$participant->generateQRCode();

		if (!$participant->save()) {
			Session::flash('alert-danger', 'Could not add Gift!');
			return Redirect::to('admin/events/' . $event->slug . '/tickets');          
		}

		Session::flash('alert-success', 'Successfully added Gift!');
		return Redirect::to('admin/events/' . $event->slug . '/tickets');
	}

	/**
	 * Add Admin Participant
	 * @param  Request $request
	 * @param  Event   $event
	 * @return Redirect 
	 */
	public function freeStaff(Request $request, Event $event)
	{
		$participant = new EventParticipant;

		$participant->user_id                = $request->user_id;
		$participant->event_id               = $event->id;
		$participant->staff                  = 1;
		$participant->staff_free_assigned_by = Auth::id();
		$participant->generateQRCode();
	 
		if (!$participant->save()) {
			Session::flash('alert-danger', 'Could not add Admin!');
			return Redirect::to('admin/events/' . $event->slug . '/tickets');          
		}

		Session::flash('alert-success', 'Successfully added Admin!');
		return Redirect::to('admin/events/' . $event->slug . '/tickets');
	}
}

