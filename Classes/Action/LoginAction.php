<?php

/**
 * This file is part of the "wpu_graphql" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Wolf Utz <wpu@hotmail.de>
 */

declare(strict_types=1);

namespace Wpu\Graphql\Action;

use Exception;
use GraphQL\Error\FormattedError;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use Wpu\Graphql\Auth\Jwt\JwtAuthInterface;
use Wpu\Graphql\Exception\HttpBadRequestException;
use Wpu\Graphql\Exception\HttpUnauthorizedException;
use Wpu\Graphql\Provider\DebugFlagProviderInterface;

class LoginAction implements ActionInterface
{
    /**
     * @var JwtAuthInterface
     */
    private $auth;

    /**
     * @var PasswordHashFactory
     */
    private $passwordHashFactory;

    /**
     * @var FrontendUserRepository
     */
    private $frontendUserRepository;

    /**
     * @var QuerySettingsInterface
     */
    private $querySettings;

    /**
     * @var DebugFlagProviderInterface
     */
    private $debugFlagProvider;

    public function __construct(
        JwtAuthInterface $auth,
        PasswordHashFactory $passwordHashFactory,
        FrontendUserRepository $frontendUserRepository,
        QuerySettingsInterface $querySettings,
        DebugFlagProviderInterface $debugFlagProvider
    ) {
        $this->auth = $auth;
        $this->passwordHashFactory = $passwordHashFactory;
        $this->frontendUserRepository = $frontendUserRepository;
        $this->querySettings = $querySettings;
        $this->debugFlagProvider = $debugFlagProvider;
    }

    public function process(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $configuration = []
    ): ResponseInterface {
        try {
            $this->removeStoragePageConstraintOfFrontendUserRepository();

            $arguments = $this->getAndValidateRequiredArguments($request);

            // Get user, if exists.
            $user = $this->fetchUser($arguments['username']);
            // Check password.
            if (!$this->checkPassword($arguments['password'], $user->getPassword())) {
                throw new HttpUnauthorizedException('Username or password is wrong.');
            }
            // Send success response with jwt.
            $result = [
                'access_token' => $this->auth->createJwt([]), //@todo add claim service
                'token_type' => 'Bearer',
                'expires_in' => $this->auth->getLifetime(),
            ];
            $response->getBody()->write((string) json_encode($result));
            $response = $response->withStatus(201)->withHeader('Content-type', 'application/json');

            return $response;
        } catch (Exception $exception) {
            $response->getBody()->write((string) json_encode([
                'errors' => FormattedError::createFromException($exception, $this->debugFlagProvider->getDebugFlag()),
            ]));

            return $response->withStatus(200)->withHeader('Content-type', 'application/json');
        }
    }

    private function getAndValidateRequiredArguments(ServerRequestInterface $request): array
    {
        $arguments = (array) $request->getParsedBody();

        // Validate username.
        if (!isset($arguments['username']) || empty($arguments['username'])) {
            throw new HttpBadRequestException('Missing required argument "username".');
        }

        // Validate password.
        if (!isset($arguments['password']) || empty($arguments['password'])) {
            throw new HttpBadRequestException('Missing required argument "password".');
        }

        return $arguments;
    }

    private function removeStoragePageConstraintOfFrontendUserRepository(): void
    {
        $this->querySettings->setRespectStoragePage(false);
        $this->frontendUserRepository->setDefaultQuerySettings($this->querySettings);
    }

    private function fetchUser(string $username): FrontendUser
    {
        /** @var FrontendUser|null $user */
        $user = $this->frontendUserRepository->findOneByUsername($username);
        if (is_null($user)) {
            throw new HttpUnauthorizedException('User not found');
        }

        return $user;
    }

    private function checkPassword(string $suppliedPassword, string $storedPassword): bool
    {
        try {
            if (!defined('TYPO3_MODE')) {
                define('TYPO3_MODE', 'BE');
            }
            $hashInstance = $this->passwordHashFactory->get($storedPassword, TYPO3_MODE);

            return $hashInstance instanceof PasswordHashInterface &&
                $hashInstance->checkPassword($suppliedPassword, $storedPassword);
        } catch (Exception $exception) {
            return false;
        }
    }
}
