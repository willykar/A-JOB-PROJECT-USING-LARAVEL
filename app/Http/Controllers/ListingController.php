<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use function GuzzleHttp\Promise\all;

class ListingController extends Controller
{
    //get and show all listings
    public function index() {
     return view('listings.index', [
                'listings' => Listing::latest()->filter(request(['tag', 'search']))->simplePaginate()
            ]);
    

    }

//show single listing
    public function show(Listing $listing) {
        return view('listings.show', [
            'listing' => $listing
         ]);
    }

    //Show create form
    public function create() {
        return view('listings.create');
    }

    //store listing data
    public function store(Request $request) {
        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')],
            'location' =>'required',
            'website' => 'required',
            'email' => ['required','email'],
            'tags' => 'required',
            'description' => 'required',
           
        ]);

        if($request->hasFile('logo')) {
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $formFields['user_id'] = auth()->id();

        Listing::create($formFields);


      

        return redirect('/')->with('message', 'Listing created succesfully');
    }

    //Show edit Form
    public function edit(Listing $listing) {
        return view('listings.edit', ['listing' => $listing]);
    }

    //update listing data

    public function update(Request $request, Listing $listing) {

        //Make sure logged in user is owner
        if($listing->user_id != auth()->id()) {
            abort(403, 'Unauthorized');
        }
        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required'],
            'location' =>'required',
            'website' => 'required',
            'email' => ['required','email'],
            'tags' => 'required',
            'description' => 'required',
           
        ]);

        if($request->hasFile('logo')) {
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $listing->update($formFields);


        return back()->with('message', 'Listing updated succesfully');
    }


    //Delete listing
    public function destroy(Listing $listing) {
        //Make sure logged in user is owner
        if($listing->user_id != auth()->id()) {
            abort(403, 'Unauthorized');
        }
        $listing->delete();
        return redirect('/')->with('message', 'Listing deleted succesfully');
    }


    //Manage Listings

    public function manage() {
        return view('listings.manage', ['listings' => auth()->
        user()->listings()->get()]);
    }

   
}
