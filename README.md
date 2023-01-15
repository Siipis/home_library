# Home Library
**Personal project and a constant work in progress.** As an owner of an overflowing amount of books, I need a way to easily catalogue the volumes and track who I lend them to. I've solved the problem by hooking a handheld barcode scanner into the PC (I'm still looking for a well-maintained library that supports smartphones), or alternatively allowed the user to manually insert data. 

If given an ISBN, the app attempts to fetch a record from the user's library. If none is found, it queries [Finna](https://finna.fi/?lng=en-gb), [GoodReads](https://www.goodreads.com/), and the [Open Library](https://openlibrary.org/) for information and offers the results as presets for adding a new record. The app also attempts to fetch the cover image based on the data entered into the form in addition to providing the alternative to insert one manually.

## The stack
The back end is powered by [Laravel 8](https://laravel.com/) using [Twig](https://twig.symfony.com/) as the templating engine. Dynamic views are supplemented by [Vue](https://vuejs.org/) on the front-end. The solution works but is far from elegant, and will be refactored to work on top of [Laravel Livewire](https://laravel-livewire.com/) when I have the time.

## How do I get access?
You can email or message me to get access to the site. Contact info at [varjohovi.net](https://varjohovi.net).
