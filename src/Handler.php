<?php


namespace SessionCreate;


use Doctrine\DBAL\DBALException;
use SessionCreate\DTO\Session;
use SessionCreate\Exception\UserDataParseException;
use TopicAdvisor\Lambda\RuntimeApi\Http\HttpRequestInterface;
use TopicAdvisor\Lambda\RuntimeApi\Http\HttpResponse;
use TopicAdvisor\Lambda\RuntimeApi\InvocationRequestHandlerInterface;
use TopicAdvisor\Lambda\RuntimeApi\InvocationRequestInterface;
use TopicAdvisor\Lambda\RuntimeApi\InvocationResponseInterface;

class Handler implements InvocationRequestHandlerInterface
{
    /**
     * @var SessionRepository
     */
    private $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    /**
     * @param InvocationRequestInterface $request
     * @return bool
     */
    public function canHandle(InvocationRequestInterface $request): bool
    {
        return $request instanceof HttpRequestInterface;
    }

    /**
     * @param InvocationRequestInterface $request
     * @return void
     */
    public function preHandle(InvocationRequestInterface $request)
    {
    }

    /**
     * @param InvocationRequestInterface $request
     * @return InvocationResponseInterface
     * @throws DBALException
     */
    public function handle(InvocationRequestInterface $request): InvocationResponseInterface
    {
        if (!$request instanceof HttpRequestInterface) {
            throw new \LogicException('Must be invoked with HttpRequestInterface only');
        }

        $response = new HttpResponse($request->getInvocationId());

        if ($request->getMethod() !== 'POST') {
            $response->setStatusCode(405);
            return $response;
        }

        try {
            $session = Session::fromUserData(\json_decode($request->getBody(), true));
        } catch (UserDataParseException $ex) {
            $response->setStatusCode(400);
            $response->setBody($ex->getMessage());
            return $response;
        }

        $userId = (int)JwtTokenHelper::fromHttpRequest($request)->getSub();
        $result = $this->sessionRepository->create($userId, $session);

        $response = new HttpResponse($request->getInvocationId());
        $response->setStatusCode(201);
        $response->setHeaders(['Content-type' => 'application/json']);
        $response->setBody(\json_encode(['id' => $result]));

        return $response;
    }

    /**
     * @param InvocationRequestInterface $request
     * @param InvocationResponseInterface $response
     * @return void
     */
    public function postHandle(InvocationRequestInterface $request, InvocationResponseInterface $response)
    {
    }
}
