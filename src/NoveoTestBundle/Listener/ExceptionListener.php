<?php
namespace NoveoTestBundle\Listener;

use JMS\Serializer\SerializerInterface;
use NoveoTestBundle\DTO\Error;
use NoveoTestBundle\DTO\ValidationError;
use NoveoTestBundle\Exception\ValidationFailedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $response = new Response();

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());

            $error = new Error($exception->getMessage());
        } elseif ($exception instanceof ValidationFailedException) {
            $response->setStatusCode(400);

            $error = new Error($exception->getMessage(), 'validation');

            foreach ($exception->getViolations() as $violation) {
                $error->addDetailedError(new ValidationError($violation->getMessage(), $violation->getPropertyPath(), $violation->getCode()));
            }
        } else {
            $response->setStatusCode(500);
            $error = new Error($exception->getMessage());
        }

        $response->setContent($this->serializer->serialize(['error' => $error], 'json'));

        $event->setResponse($response);
    }
}