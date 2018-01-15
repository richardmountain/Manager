<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use DB;
use Auth;
use Storage;
use App\User;
use App\Event;
use App\GalleryAlbum;
use App\GalleryAlbumImage;

use Input;
use Validator;
use Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class GalleryController extends Controller
{
	/**
	 * Show Gallery Index Page
	 * @return view
	 */
	public function index()
	{
		$user = Auth::user();
		$albums = GalleryAlbum::all();
		return view('admin.gallery.index')->withUser($user)->withAlbums($albums);  
	}
	
	/**
	 * Show Gallery Page
	 * @return view
	 */
	public function show(GalleryAlbum $album)
	{
		$user = Auth::user();
		return view('admin.gallery.show')->withUser($user)->withAlbum($album);  
	}
	
	/**
	 * Store Gallery to DB
	 * @param  Request $request
	 * @return Redirect
	 */
	public function store(Request $request)
	{
		$rules = [
			'name'        => 'required',
			'description' => 'required'
		];
		$messages = [
			'name|required'         => 'A Name is required',
			'description|required'  => 'A Description is required'
		];
		$this->validate($request, $rules, $messages);

		$album = new GalleryAlbum;
		
		$album->name = $request->name;
		$album->description = $request->description;

		if (!$album->save()) {
			Session::flash('alert-danger', 'Cannot add Gallery!');
			return Redirect::to('admin/gallery');
		}
		Session::flash('alert-success', 'Successfully added Gallery!');
		return Redirect::to('admin/gallery/' . $album->id);
	}
	
	/**
	 * Update Gallery
	 * @param  GalleryAlbum           $album
	 * @param  GalleryAlbumImage|null $image
	 * @param  Request                $request
	 * @return Redirect                          
	 */
	public function update(GalleryAlbum $album, GalleryAlbumImage $image = NULL, Request $request)
	{
		$rules = [
			'name'        => 'filled',
			'description' => 'filled',
			'status'      => 'in:draft,published',
			'event_id'    => 'exists:events,id',
		];
		$messages = [
			'name|filled'         => 'Name cannot be empty',
			'description|filled'  => 'Description cannot be empty',
			'status|in'           => 'Status must be draft or published',
			'event_id|exists'     => 'Event_id must be a real ID',
		];
		$this->validate($request, $rules, $messages);

		$album->name        = $request->name;
		$album->description = $request->description;
		$album->status      = $request->status;
		$album->event_id    = $request->event_id;

		if(!$album->save()){
			Session::flash('alert-danger', 'Could not save!');
			return Redirect::back();
		}
		Session::flash('alert-success', 'Successfully updated!');
		return Redirect::back();
	}

	/**
	 * Delete Gallery
	 * @param  GalleryAlbum $album
	 * @return Redirect
	 */
	public function destroy(GalleryAlbum $album)
	{
		if (!$album->delete()) {
			Session::flash('alert-danger', 'Could not delete!');
			return Redirect::back();
		}
		Session::flash('alert-success', 'Successfully deleted!');
		return Redirect::back();
	}

	/**
	 * Upload Image to Gallery
	 * @param  GalleryAlbum $album
	 * @param  Request      $request
	 * @return Redirect
	 */
	public function uploadImage(GalleryAlbum $album, Request $request)
	{
		$rules = [
			'image.*' => 'image',
		];
		$messages = [
			'image.*|image'             => 'Venue Image must be of Image type',
		];
		$this->validate($request, $rules, $messages);
		$files = Input::file('images');
		//Keep a count of uploaded files
		$file_count = count($files);
		//Counter for uploaded files
		$uploadcount = 0;
		foreach($files as $file){
			$image = new GalleryAlbumImage;
			
			$image_name = $file->getClientOriginalName();
			$destination_path = 'public/images/gallery/' . $album->name;

			$image->display_name = $image_name;
			$image->nice_name = $image->url = strtolower(str_replace(' ', '-', $image_name));
			$image->gallery_album_id = $album->id;
			$image->path = str_replace(
					'public/', 
					'/storage/', 
					Storage::put($destination_path, 
							$file
					)
			);
			$uploadcount ++;
			$image->save();
		}
		if($uploadcount != $file_count){
			Session::flash('alert-danger', 'Upload unsuccessful!'); 
			return Redirect::to('admin/gallery/' . $album->id);
		} 
		Session::flash('alert-success', 'Upload successful!'); 
		return Redirect::to('admin/gallery/' . $album->id);
	}

	/**
	 * Delete Image from Gallery
	 * @param  GalleryAlbum      $album
	 * @param  GalleryAlbumImage $image
	 * @return Redirect
	 */
	public function destroyImage(GalleryAlbum $album, GalleryAlbumImage $image)
	{
		if (!$image->delete()) {
			Session::flash('alert-danger', 'Could not delete!');
			return Redirect::back();
		}
		Session::flash('alert-success', 'Successfully deleted!');
		return Redirect::back();
	}

	/**
	 * Update Image from Gallery
	 * @param  GalleryAlbum      $album
	 * @param  GalleryAlbumImage $image
	 * @param  Request           $request
	 * @return Redirect
	 */
	public function updateImage(GalleryAlbum $album, GalleryAlbumImage $image, Request $request)
	{

		//DEBUG - Refactor - replace iamge name as well!
		$image->display_name  = $request->name;
		$image->nice_name     = strtolower(str_replace(' ', '-', $request->name));
		$image->desc          = $request->desc;

		if(isset($request->album_cover) && $request->album_cover){
			$album->setAlbumCover($image->id);
		}

		if (!$image->save()) {
			Session::flash('alert-danger', 'Could not update!');
			return Redirect::back();
		}
		Session::flash('alert-success', 'Successfully updated!');
		return Redirect::back();
	}
}