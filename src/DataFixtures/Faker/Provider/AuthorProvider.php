<?php


namespace App\DataFixtures\Faker\Provider;

use Faker\Provider\Base as BaseProvider;

class AuthorProvider extends BaseProvider
{
    const BOOK_AUTHOR = [
        'Julia Karr',
        'Charlotte Perkins Gilman',
        'Geraldine Brooks',
        'Margaret Atwood',
        'Katie MacAlister',
        'Laila Ibrahim',
        'Joan Didion',
        'Robert M. Pirsig',
        'Diane Ackerman',
        'Christopher Moore',
        'Therese Anne Fowler',
        'Lyn Hamilton',
        'CLAMP',
        'Dave Eggers',
        'Kim Stanley Robinson',
        'Teresa Medeiros',
        'Felicia Day',
        ' Robert C. O\'Brien',
        'Sara C. Roethle',
        'Max Brooks',
        'Isabel Allende',
        'Max Lucado',
        'Caroline Kepnes',
        'Jane Yolen',
        'Marjorie Kinnan Rawlings',
        'Jenny Downham',
        'Nikos Kazantzakis',
        'Stacey Jay',
        'Amy Poehler',
        'Marie Lu ',
        'Sue Grafton',
        'Lauren Beukes',
        'Moss Hart',
        'Michael Chabon',
        'Chika Shiomi',
        'Richard Peck',
        'Dr. Seuss',
        'Liz Kessler',
        'Cylin Busby'
    ];

    /**
     * @return string Random author name.
     */
    public function authorName()
    {
        return self::randomElement(self::BOOK_AUTHOR);
    }
}