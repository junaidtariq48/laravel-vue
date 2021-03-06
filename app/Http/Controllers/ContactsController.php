<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    public function store()
    {
        $data = $this->validateData();
        Contact::create($data);
    }

    public function show(Contact $contact)
    {
        return $contact;
    }

    public function update(Contact $contact)
    {
        $contact->update($this->validateData());
    }

    private function validateData()
    {
        return request()->validate([
            'name' => 'required',
            'email' => 'required|email',
            'birthday' => 'required',
            'company' => 'required',
        ]);
    }
}
