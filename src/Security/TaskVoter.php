<?php

namespace App\Security;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * @codeCoverageIgnore
 */
class TaskVoter extends Voter
{
    const REMOVE = 'remove';
    const REMOVE_TASK_WITH_ANONYMOUS_AUTHOR = 'remove_task_with_anonymous_author';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::REMOVE, self::REMOVE_TASK_WITH_ANONYMOUS_AUTHOR])) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $task = $subject;

        switch ($attribute) {
            case self::REMOVE:
                return $this->canRemove($task, $user);

            case self::REMOVE_TASK_WITH_ANONYMOUS_AUTHOR:
                return $this->canRemoveTaskWithoutAuthor();
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canRemove(Task $task, User $user): bool
    {
        return $user === $task->getAuthor();
    }

    private function canRemoveTaskWithoutAuthor(): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }
}