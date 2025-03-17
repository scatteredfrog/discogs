# ScatteredFrog's Discogs API Stuff

### Built With

[![PHP](https://img.shields.io/badge/php-%23777BB4.svg?&logo=php&logoColor=white)](https://www.php.net/)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?logo=javascript&logoColor=000)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![jQuery](https://img.shields.io/badge/jQuery-0769AD?logo=jquery&logoColor=fff)](https://jquery.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?logo=bootstrap&logoColor=fff)](https://getbootstrap.com/)

## Why This Project?
I'm a fan of vinyl records, and I keep track of my collection, as do many
collectors, on [Discogs](https://www.discogs.com/). Some prefer to use
spreadsheets, which is fine, but with Discogs, you get all kinds of features
that you likely wouldn't get otherwise, such as the date of a particular
version of a record, where it was pressed, etc. Also, with the Discogs phone
app, you can always have access to your inventory as long as you have an
Internet connection.

I use Discogs not just to track my collection, but also to make note of when I
last washed a record. When a record enters my home for the first time, it does
not touch the turntable until I wash it in my Spin-Clean washer. With a
collection my size, there was no way I could have washed all the records in
one session. At first, I'd use Discogs' built-in Notes field to keep track of
when I washed a record, but the problem is, I used that field for other things
as well, making it hard to find which records I hadn't yet washed.

My solution: Discogs allows custom fields -- text and dropdown. I added a text
custom field speicifcally to record the date the record was washed. But still,
that wasn't the greatest solution because none of Discogs' filtering allows
you to filter by custom fields. The site itself is slow, too, so scrolling
through my collection to find what records still need washing is a royal PITA.

## What Does It Do?
Demo video here: (https://www.youtube.com/watch?v=tyOannUb8HY) 

It occurred to me that maybe Discogs has an API - and sure enough, they do! As
of right now, this repo consists of code I whipped up to help me manage the
washing history of my records. Right now, there are two tasks that I coded:

1. At first, I entered the dates in MM-DD-YYYY format, which I later realized
could play hell when sorting in a spreadsheet should I export my collection as
CSV. List any item in my collection whose wash date is in that format, and
offer a button that, when clicked, re-submits the same date in YYYY-MM-DD
format.
2. List everything in my collection that does not have a wash date. Offer
three buttons:
    - one to click to indicate that the item should NOT be washed
(because it's a CD, tape, etc.) and populate the field with "n/a"
    - another to click that allows the user to enter a specific date
    - a third button that, when clicked, automatically populates the custom wash
date field with the current date.

My whole modus operandi is that when I'm washing my records, I can open this
mini-app on my laptop or phone, and after I wash a record, I can tap that
third button as I put it the newly-washed record on the drying rack, and I'll
be up-to-date immediately rather than make a note of what recoreds I washed
and hopefully remember to manually update their entries later.

Right now the only real problem I see with both of these tasks is that it
takes a long time for the tables to appear. Undoubtedly it's because of the
handling involved dumping the API's response into an array. Discogs doesn't
exactly have the most user-friendly API. You can't just tell it, "Give me
only titles by Led Zeppelin" or "Only give me the records that have a certain
field filled out" - you can only retrieve your collection by folder. Which
in many cases relies on you to be very diligent in sorting your collectin into
folders. There's also the option to retrieve folder_0, which is your ENTIRE
collection. The results are quite a mess to have to wade through -- you have
arrays within arrays within arrays, sometimes the arrays aren't as deep as
others, etc., so you have to put handling in that covers all situations. And
on top of that, you can't even get your WHOLE collection: the results are
paginated, and the maximum number of items per page you can retrieve is 100.
So I had to write my code so that it keeps making the API call and adding to
the array until it runs out of pages. The actual API calls are pretty fast,
though - again, it's the handling on my end that takes a long time. I am
considering re-doing the code in Python or perhaps Ruby -- maybe even Go if I
decide to run the code only locally instead of putting it on a server.

I'm always open to suggestions.

## Installation
Just copy the files somewhere where you can run PHP code -- your own computer,
a shared host, etc. You just need to have PHP installed. I used PHP 8.4 when
coding this, but I'm pretty sure this will work with PHP 7 as well.

## Usage
Plug the following information into the `config.php` file:
- `$user`: your Discogs.com username
- `$field_id`: the value of the custom field you wish to use (in my case, it's
  4 -- you may have to make a call to the
`/users/{username}/collection/folders/{folder_id}` end point to
verify)
- `$token`: your current app's token as provided by Discogs
-  `$items_per_page`: maxes out at 100. The biggger the number, the fewer the
-  API calls.

Open the `index.php` file in your web browser of choice.

### **Fix dates**
Gives you a list of titles whose wash dates are not formatted 
YYYY-MM-DD. Click the "Reformat" button to reformat the date. The page will 
reload, and because the title no longer has an incorrectly formatted date, you 
won't see it on the list.

### **Update wash dates**
Gives you a list of titles that do not have their wash 
date fields populated. Each listed title gives you three options:
- **NO** - as in, "No, this item should not be washed!" This is intended for
items that are generally not considered washable. If you click "NO" and the
item contains vinyl, a confirmation modal will appear verifying that you do
indeed want to _not_ wash the item.
- **When?** - as in, "When did you wash this item?" Click this button if you
want to enter the wash date manually - like, say, you washed it last week and
are just now updating your collection.
- **today** - as in, "I washed this today." Clicking this button tells the app
to just populate the field with today's date; no manual entry required!

_Note:_
- Clicking either "When?" or "today" will give you a verification modal if
the item you are washing does _not_ contain vinyl.
- After the wash date is updated successfully, the page will reload. Because the
item you updated is no longer missing a wash date, it will not appear after the
reload.
