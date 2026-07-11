<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(20);
        $newCount = ContactMessage::new()->count();

        return view('admin.contact-messages.index', compact('messages', 'newCount'));
    }

    public function show(ContactMessage $contactMessage)
    {
        // Mark unread messages as read on open.
        if ($contactMessage->status === 'new') {
            $contactMessage->update(['status' => 'read']);
        }

        return view('admin.contact-messages.show', ['message' => $contactMessage]);
    }

    public function reply(Request $request, ContactMessage $contactMessage)
    {
        $data = $request->validate([
            'admin_reply' => 'required|string|max:5000',
        ]);

        $contactMessage->update([
            'admin_reply' => $data['admin_reply'],
            'status'      => 'answered',
            'answered_at' => now(),
        ]);

        return back()->with('success', 'پاسخ ذخیره شد.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'پیام حذف شد.');
    }
}
