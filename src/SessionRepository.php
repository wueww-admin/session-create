<?php


namespace SessionCreate;


use Doctrine\DBAL\Types\Type;
use SessionCreate\DTO\Session;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class SessionRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int $userId
     * @param Session $session
     * @return int
     * @throws DBALException
     */
    public function create(int $userId, Session $session): int
    {
        $this->connection->insert('session_details', [
            'title' => $session->getTitle(),
            'short_description' => $session->getShortDescription(),
            'long_description' => $session->getLongDescription(),
            'location_name' => $session->getLocationName(),
            'location_lat' => $session->getLocationLat(),
            'location_lng' => $session->getLocationLng(),
            'link' => $session->getLink(),
        ]);

        $sessionDetailId = $this->connection->lastInsertId();

        $this->connection->insert('sessions', [
            'start' => $session->getStart(),
            'end' => $session->getEnd(),
            'owner' => $userId,
            'proposed_details' => $sessionDetailId,
        ], [
            Type::DATETIME,
            Type::DATETIME,
            Type::INTEGER,
            Type::INTEGER,
        ]);

        return (int) $this->connection->lastInsertId();
    }

}