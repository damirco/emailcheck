<?php
namespace Damirco\Emailcheck;

class EmailSet
{
    private array $emails = [];

    /**
     * @param string[] $emails
     */
    public function __construct(array $emails)
    {
        $this->emails = array_unique(array_filter($emails, 'is_string'));
    }

    /**
     * Validates all emails in the set.
     *
     * @param bool $check_syntax Enable syntax validation via regex.
     * @param bool $check_mx     Enable MX record validation via DNS.
     * @return array{valid: string[], invalid: string[]} Emails grouped by validation result.
     */
    public function check(bool $check_syntax = true, bool $check_mx = true): array
    {
        $result = ['valid' => [], 'invalid' => []];
        foreach ( $this->emails as $email_address ) {
            $email = new Email($email_address);
            $isMxValid = !$check_mx || $email->isMxValid();
            $isSyntaxValid = !$check_syntax || $email->isSyntaxValid();
            if ( $isSyntaxValid && $isMxValid ) {
                $result['valid'][] = $email_address;
            } else {
                $result['invalid'][] = $email_address;
            }
        }
        return $result;
    }

    /**
     * Validates email syntax only (no DNS lookup).
     *
     * @return array{valid: string[], invalid: string[]}
     */
    public function checkSyntax(): array
    {
        return $this->check(true, false);
    }

    /**
     * Validates MX records only (no syntax check).
     *
     * @return array{valid: string[], invalid: string[]}
     */
    public function checkMxRecords(): array
    {
        return $this->check(false);
    }
}
