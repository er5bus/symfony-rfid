<?php

namespace App\DataFixtures\Faker\Provider;

use Faker\Provider\Base as BaseProvider;

class WritingGenreProvider extends BaseProvider
{
    const BOOK_GENRE = [
        'Action and adventure',
        'Art',
        'Alternate history',
        'Autobiography',
        'Anthology',
        'Biography',
        'Chick lit',
        'Book review',
        'Children\'s',
        'Cookbook',
        'Comic book',
        'Diary',
        'Coming-of-age',
        'Dictionary',
        'Crime',
        'Encyclopedia',
        'Drama',
        'Guide',
        'Fairytale',
        'Health',
        'Fantasy',
        'History',
        'Graphic novel',
        'Journal',
        'Historical fiction',
        'Math',
        'Horror',
        'Memoir',
        'Mystery',
        'Prayer',
        'Paranormal romance',
        'Religion, spirituality, and new age',
        'Picture book',
        'Textbook',
        'Poetry',
        'Review',
        'Political thriller',
        'Science',
        'Romance',
        'Self help',
        'Satire',
        'Travel',
        'Science fiction',
        'True crime',
        'Short story',
        'Suspense',
        'Thriller',
        'Young adult'
    ];

    /**
     * @return string Random Writing Genre.
     */
    public function writingGenre()
    {
        return self::randomElement(static::BOOK_GENRE);
    }
}