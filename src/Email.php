<?php
namespace Damirco\Emailcheck;

class Email
{
    const string REGEX = '~^[\w.+-]+@[\w.-]+$~u';

    public function __construct(readonly private string $email)
    {
    }

    public function __toString(): string
    {
        return $this->email;
    }

    /**
     * Checks email syntax validity.
     */
    public function isSyntaxValid(): bool
    {
        return preg_match(self::REGEX, $this->email);
    }

    /**
     * Checks MX records in DNS for email domain.
     */
    public function isMxValid(): bool
    {
        $email_parts = explode('@', $this->email);
        if ( count($email_parts) !== 2 ) { // invalid email
            return false;
        }
        $domain = $email_parts[1];
        return checkdnsrr($domain, 'MX');
    }

    public function isValid(): bool
    {
        return $this->isSyntaxValid() && $this->isMxValid();
    }
}
