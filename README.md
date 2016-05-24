# WebBoard
I'm extending the work of the class project to become my own thing, with added features and fixes.

This project was originally was called LTUBillboard and was a group project with some classmates. 
The end project was not really what I wanted it to be,so I'm going to work on fixing stuff that is broken and add features. 
One big thing that was pointed out during presentation was that it wasn't so much a billboard replacement as much as just a web calendar.

<h3>Current Features:</h3>
<h4>Front-end:</h4>
<ol>
<li>Create/edit student accounts and organization accounts. Organization accounts can be approved by admin, but nothing changes.</li>
<li>Organizations can add/remove events. Currently they're automatically approved, but there is code in place to approve them. This does nothing.</li>
<li>Student accounts can add/remove private events that only they see.</li>
<li>Students can add events to their calendar, which just adds them to another filter.</li>
<li>Admins can post announcements, but they are just text.</li>
<li>Students can "follow" organizations, which adds them to another filter on their calendar and to the student's profile page.</li>
<li>Upcoming events section on the calendar page shows the next 5 events that are on the calendar.</li>
<li>Session management with PHP.</s>
</ol>
<h4>Back-end:</h4>
<ol>
<li>Basic form validation with html5 and jquery.validation plugin. PHP also for checking if a user actually has an account. 
  Only fully works in Chrome, since html5 dates aren't supported by IE or FF</li>
<li>Most form processing is done on the page it's submitted from, discounting the admin page.</li>
</ol>

<h3>Planned Features:</h3>
<h4>Front-end:</h4>
<ol>
<li>Ability for organizations to edit events.</li>
<li>Email notifications for followed organizations and events</li>
<li>Some kind of actual billboard functionality, like the ability too add flier images or something with drag and dropping stuff on the board.</li>
<li>Added functionality to announcements, like timing or modal pop-ups.</li>
<li>Have stuff scale down better on Mobile</li>
</ol>

<h4>Back-end:</h4>
<ol>
<li>Code clean up, with more organization. Focus on files specifically for php processing instead of having each page have the same stuff.</li>
<li>More form processing at all levels, like disallowed words on events and organizations. Also have it work on non-Chrome broswers.</li>
<li>Encrpyted passwords in database.</li>
<li>Event and organization approval functionality</li> 
</ol>
