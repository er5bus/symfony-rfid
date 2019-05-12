<?php


namespace App\DataFixtures\Faker\Provider;

use App\Entity\Reservation;
use Faker\Provider\Base as BaseProvider;

class ReservationStatusProvider extends BaseProvider
{
    const RESERVATION_STATUS = [
        Reservation::FULLY_ACCEPTED,
        Reservation::PARTIALLY_ACCEPTED,
        Reservation::IN_PENDING,
        Reservation::REFUSED
    ];

    /**
     * @return string Random reservation status.
     */
    public function reservationStatus()
    {
        return self::randomElement(self::RESERVATION_STATUS);
    }
}