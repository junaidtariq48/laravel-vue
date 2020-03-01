<?php

namespace Tests\Feature;

use App\Contact;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
   public function a_contact_can_be_added()
   {
       $this->post('/api/contacts',$this->data());

       $contact = Contact::first();

       $this->assertEquals('Test Name', $contact->name);
       $this->assertEquals('test@email.com', $contact->email);
       $this->assertEquals('08/10/1990', $contact->birthday->format('m/d/Y'));
       $this->assertEquals('test company', $contact->company);
   }

   /** @test */
   public function fields_are_required()
   {
       collect(['name', 'email', 'birthday', 'company'])
       ->each(function ($field){
           $response = $this->post('/api/contacts',array_merge(
               $this->data(),
               [ $field => '' ]
           ));

           $response->assertSessionHasErrors($field);
           $this->assertCount(0, Contact::all());
       });
   }

    /** @test */
    public function email_must_be_a_valid_email()
    {
        $response = $this->post('/api/contacts',array_merge(
            $this->data(),
            [ 'email' => 'not an email' ]
        ));

        $response->assertSessionHasErrors('email');
        $this->assertCount(0, Contact::all());
    }

    /** @test */
    public function birthday_are_properly_stored()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('/api/contacts',array_merge(
            $this->data()
        ));

        $birthday = Contact::first()->birthday;

        $this->assertCount(1, Contact::all());
        $this->assertInstanceOf(Carbon::class, $birthday );
        $this->assertEquals('08-10-1990', $birthday->format('m-d-Y'));
    }

    /** @test */
    public function a_contact_can_be_retrieved()
    {
        $contact = factory(Contact::class)->create();

        $response = $this->get('/api/contacts/'.$contact->id);

        $response->assertJson([
            'name' => $contact->name,
            'email' => $contact->email,
            'birthday' => $contact->birthday,
            'company' => $contact->company
        ]);

    }

    /** @test */
    public function a_contact_can_be_patched()
    {
        $this->withoutExceptionHandling();
        $contact = factory(Contact::class)->create();

        $response = $this->patch('/api/contacts/' . $contact->id, $this->data());

        $contact = $contact->fresh();

        $this->assertEquals('Test Name', $contact->name);
        $this->assertEquals('test@email.com', $contact->email);
        $this->assertEquals('08/10/1990', $contact->birthday->format('m/d/Y'));
        $this->assertEquals('test company', $contact->company);
    }

   public function data()
   {
       return [
           'name' => 'Test Name',
           'email' => 'test@email.com',
           'birthday' => '08/10/1990',
           'company' => 'test company'
       ];
   }
}
