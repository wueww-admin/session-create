<?php


namespace SessionCreate\DTO;


use SessionCreate\Exception\UserDataParseException;

class Session
{
    /**
     * @var \DateTimeImmutable
     */
    private $start;

    /**
     * @var \DateTimeImmutable|null
     */
    private $end;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $shortDescription;

    /**
     * @var string|null
     */
    private $longDescription;

    /**
     * @var string
     */
    private $locationName;

    /**
     * @var float|null
     */
    private $locationLat;

    /**
     * @var float|null
     */
    private $locationLng;

    /**
     * @var string|null
     */
    private $link;

    private function __construct()
    {
    }

    /**
     * @param mixed $data
     * @return Session
     */
    public static function fromUserData($data): self
    {
        if (!is_array($data)) {
            throw new UserDataParseException('.');
        }

        $result = new self();

        self::parseStart($data, $result);
        self::parseEnd($data, $result);

        // TODO check end > start
        // TODO check max. session length

        self::parseTitle($data, $result);
        self::parseShortDescription($data, $result);
        self::parseLongDescription($data, $result);
        self::parseLocationName($data, $result);
        self::parseLocationLat($data, $result);
        self::parseLocationLng($data, $result);
        self::parseLink($data, $result);

        return $result;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getStart(): \DateTimeImmutable
    {
        return $this->start;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getEnd(): ?\DateTimeImmutable
    {
        return $this->end;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    /**
     * @return string|null
     */
    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    /**
     * @return string
     */
    public function getLocationName(): string
    {
        return $this->locationName;
    }

    /**
     * @return float|null
     */
    public function getLocationLat(): ?float
    {
        return $this->locationLat;
    }

    /**
     * @return float|null
     */
    public function getLocationLng(): ?float
    {
        return $this->locationLng;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    private static function parseStart(array $data, Session $result): void
    {
        if (!isset($data['start']) || !is_string($data['start'])) {
            throw new UserDataParseException('start');
        }

        try {
            $result->start = new \DateTimeImmutable($data['start']);
        } catch (\Exception $ex) {
            throw new UserDataParseException('start', $ex);
        }
    }

    private static function parseEnd(array $data, Session $result): void
    {
        if (!isset($data['end'])) {
            $result->end = null;
        } elseif (is_string($data['end'])) {
            try {
                $result->end = new \DateTimeImmutable($data['end']);
            } catch (\Exception $ex) {
                throw new UserDataParseException('end', $ex);
            }
        } else {
            throw new UserDataParseException('end');
        }
    }

    private static function parseTitle(array $data, Session $result): void
    {
        if (!isset($data['title']) || !is_string($data['title'])) {
            throw new UserDataParseException('title');
        }

        $result->title = $data['title'];
    }

    private static function parseShortDescription(array $data, Session $result): void
    {
        if (!isset($data['description']) || !is_array($data['description'])) {
            throw new UserDataParseException(('description'));
        }

        if (!isset($data['description']['short']) || !is_string($data['description']['short'])) {
            throw new UserDataParseException(('description.short'));
        }

        $result->shortDescription = $data['description']['short'];
    }

    private static function parseLongDescription(array $data, Session $result): void
    {
        if (!isset($data['description']) || !is_array($data['description'])) {
            throw new UserDataParseException(('description'));
        }

        if (!isset($data['description']['long'])) {
            $result->longDescription = null;
        } elseif (is_string($data['description']['long'])) {
            $result->longDescription = $data['description']['long'];
        } else {
            throw new UserDataParseException(('description.long'));
        }
    }

    private static function parseLocationName(array $data, Session $result): void
    {
        if (!isset($data['location']) || !is_array($data['location'])) {
            throw new UserDataParseException(('location'));
        }

        if (!isset($data['location']['name']) || !is_string($data['location']['name'])) {
            throw new UserDataParseException(('location.name'));
        }

        $result->locationName = $data['location']['name'];
    }

    private static function parseLocationLat(array $data, Session $result): void
    {
        if (!isset($data['location']) || !is_array($data['location'])) {
            throw new UserDataParseException(('location'));
        }

        if (!isset($data['location']['lat'])) {
            $result->locationLat = null;
        } elseif (is_float($data['location']['lat'])) {
            $result->locationLat = $data['location']['lat'];
        } else {
            throw new UserDataParseException(('location.lat'));
        }
    }

    private static function parseLocationLng(array $data, Session $result): void
    {
        if (!isset($data['location']) || !is_array($data['location'])) {
            throw new UserDataParseException(('location'));
        }

        if (!isset($data['location']['lng'])) {
            $result->locationLng = null;
        } elseif (is_float($data['location']['lng'])) {
            $result->locationLng = $data['location']['lng'];
        } else {
            throw new UserDataParseException(('location.lng'));
        }
    }

    private static function parseLink(array $data, Session $result): void
    {
        if (!isset($data['link'])) {
            $result->link = null;
        } elseif (is_string($data['link'])) {
            $result->link = $data['link'];
        } else {
            throw new UserDataParseException(('link'));
        }
    }
}