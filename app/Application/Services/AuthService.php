<?php

namespace App\Application\Services;

use App\Domain\Entities\AuditLog;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FALaravel\Google2FA;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AuditService $auditService,
    ) {}

    public function attempt(string $email, string $password, string $ip, ?string $userAgent): ?array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return null;
        }

        if (!$user->isActive()) {
            $this->auditService->logFailedLogin($user->getCompanyId(), $user->getId(), $ip, $userAgent);
            return null;
        }

        if (!Hash::check($password, $user->getPassword())) {
            $this->auditService->logFailedLogin($user->getCompanyId(), $user->getId(), $ip, $userAgent);
            return null;
        }

        $user->updateLastLogin($ip, $userAgent ?? '');
        $user->markAsOnline();
        $this->userRepository->update($user);

        $this->auditService->logLogin($user->getCompanyId(), $user->getId(), $ip, $userAgent);

        $model = UserModel::find($user->getId());
        $token = auth('api')->login($model);

        $userData = $user->jsonSerialize();
        $userData['roles'] = $model ? $model->getRoleNames()->toArray() : [];

        return [
            'token' => $token,
            'user' => $userData,
            'requires_2fa' => $user->isTwoFactorEnabled(),
        ];
    }

    public function logout(): void
    {
        $user = auth('api')->user();
        if ($user) {
            $domainUser = $this->userRepository->findById($user->id);
            if ($domainUser) {
                $domainUser->markAsOffline();
                $this->userRepository->update($domainUser);
                $this->auditService->logLogout($domainUser->getCompanyId(), $domainUser->getId());
            }
        }
        auth('api')->logout();
    }

    public function refresh(): string
    {
        return auth('api')->refresh();
    }

    public function me(): ?\App\Domain\Entities\User
    {
        $user = auth('api')->user();
        if (!$user) {
            return null;
        }
        return $this->userRepository->findById($user->id);
    }

    public function validatePassword(string $password): bool
    {
        $validator = Validator::make(
            ['password' => $password],
            ['password' => ['required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/']]
        );

        return !$validator->fails();
    }

    public function enable2FA(int $userId, string $secret): void
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new \DomainException('User not found.');
        }

        $user->enableTwoFactor($secret);
        $this->userRepository->update($user);

        $this->auditService->log(
            companyId: $user->getCompanyId(),
            userId: $userId,
            action: AuditLog::ACTION_2FA_ENABLE,
            entityType: 'user',
            entityId: $userId,
            severity: AuditLog::SEVERITY_WARNING,
            description: "2FA enabled for user: {$user->getName()}",
        );
    }

    public function disable2FA(int $userId): void
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new \DomainException('User not found.');
        }

        $user->disableTwoFactor();
        $this->userRepository->update($user);

        $this->auditService->log(
            companyId: $user->getCompanyId(),
            userId: $userId,
            action: AuditLog::ACTION_2FA_DISABLE,
            entityType: 'user',
            entityId: $userId,
            severity: AuditLog::SEVERITY_WARNING,
            description: "2FA disabled for user: {$user->getName()}",
        );
    }
}
