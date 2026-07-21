<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Concerns;

use LaraPkgs\Validation\Contracts\ValidationRule;

trait HasFluentRules
{
    abstract protected function applyFluentRule(string|ValidationRule $rule, array $arguments = []): self;

    /**
     * @RuleType = constraint
     */
    public function accepted(): self
    {
        return $this->applyFluentRule('accepted');
    }

    /**
     * @RuleType = constraint
     * TODO Check boolean casting
     */
    public function acceptedIf(string $field, mixed ...$values): self
    {
        return $this->applyFluentRule('accepted_if', compact('field', 'values'));
    }

    /**
     * @RuleType = constraint
     */
    public function activeUrl(): self
    {
        return $this->applyFluentRule('active_url');
    }

    /**
     * @RuleType = constraint
     * TODO Tell ValidationRule when a field is referenced
     */
    public function after(string $date): self
    {
        return $this->applyFluentRule('after', compact('date'));
    }

    /**
     * @RuleType = constraint
     * TODO Tell ValidationRule when a field is referenced
     */
    public function afterOrEqual(string $date): self
    {
        return $this->applyFluentRule('after_or_equal', compact('date'));
    }

    /**
     * @RuleType = constraint
     * TODO add ascii option
     */
    public function alpha(): self
    {
        return $this->applyFluentRule('alpha');
    }

    /**
     * @RuleType = constraint
     * TODO add ascii option
     */
    public function alphaDash(): self
    {
        return $this->applyFluentRule('alpha_dash');
    }

    /**
     * @RuleType = constraint
     * TODO add ascii option
     */
    public function alphaNum(): self
    {
        return $this->applyFluentRule('alpha_num');
    }

    /**
     * @RuleType = type
     */
    public function array(): self
    {
        return $this->applyFluentRule('array');
    }

    /**
     * @RuleType = constraint
     */
    public function ascii(): self
    {
        return $this->applyFluentRule('ascii');
    }

    /**
     * @RuleType = circuit
     */
    public function bail(): self
    {
        return $this->applyFluentRule('bail');
    }

    /**
     * @RuleType = constraint
     * TODO Tell ValidationRule when a field is referenced
     */
    public function before(string $date): self
    {
        return $this->applyFluentRule('before', compact('date'));
    }

    /**
     * @RuleType = constraint
     * TODO Tell ValidationRule when a field is referenced
     */
    public function beforeOrEqual(string $date): self
    {
        return $this->applyFluentRule('before_or_equal', compact('date'));
    }

    /**
     * @RuleType = constraint
     */
    public function between(int $min, int $max): self
    {
        return $this->applyFluentRule('between', compact('min', 'max'));
    }

    /**
     * @RuleType = type
     * TODO Add strict option
     */
    public function boolean(): self
    {
        return $this->applyFluentRule('boolean');
    }

    /**
     * @RuleType = constraint
     *  TODO Add custom field option
     */
    public function confirmed(): self
    {
        return $this->applyFluentRule('confirmed');
    }

    /**
     * @RuleType = constraint
     */
    public function contains(mixed ...$values): self
    {
        return $this->applyFluentRule('contains', compact('values'));
    }

    /**
     * @RuleType = constraint
     */
    public function currentPassword(): self
    {
        return $this->applyFluentRule('current_password');
    }

    /**
     * @RuleType = constraint
     */
    public function date(): self
    {
        return $this->applyFluentRule('date');
    }

    /**
     * @RuleType = constraint
     */
    public function dateEquals(string $date): self
    {
        return $this->applyFluentRule('date_equals', compact('date'));
    }

    /**
     * @RuleType = constraint
     */
    public function dateFormat(string $format): self
    {
        return $this->applyFluentRule('date_format', compact('format'));
    }

    /**
     * @RuleType = constraint
     *  TODO make max nullable
     */
    public function decimal(int $min, int $max): self
    {
        return $this->applyFluentRule('decimal', compact('min', 'max'));
    }

    /**
     * @RuleType = constraint
     */
    public function declined(): self
    {
        return $this->applyFluentRule('declined');
    }

    /**
     * @RuleType = constraint
     * TODO Check boolean casting
     */
    public function declinedIf(string $field, mixed ...$values): self
    {
        return $this->applyFluentRule('declined_if', compact('field', 'values'));
    }

    /**
     * @RuleType = constraint
     */
    public function different(string $field): self
    {
        return $this->applyFluentRule('different', compact('field'));
    }

    /**
     * @RuleType = constraint
     */
    public function digits(int $value): self
    {
        return $this->applyFluentRule('digits', compact('value'));
    }

    /**
     * @RuleType = constraint
     */
    public function digitsBetween(int $min, int $max): self
    {
        return $this->applyFluentRule('digits_between', compact('min', 'max'));
    }

    /**
     * @RuleType = constraint
     * TODO add strict option
     * TODO add ignore_case option
     */
    public function distinct(): self
    {
        return $this->applyFluentRule('distinct');
    }

    /**
     * @param array<string, string> $constraints
     * @RuleType = constraint
     * TODO handle key value constraints eg. ['min_height' => 600] iso ['min_height=600']
     */
    public function dimensions(array $constraints): self
    {
        return $this->applyFluentRule('dimensions', compact('constraints'));
    }

    /**
     * @RuleType = constraint
     */
    public function doesntContain(mixed ...$values): self
    {
        return $this->applyFluentRule('doesnt_contain', compact('values'));
    }

    /**
     * @RuleType = constraint
     */
    public function doesntStartWith(string ...$prefixes): self
    {
        return $this->applyFluentRule('doesnt_start_with', compact('prefixes'));
    }

    /**
     * @RuleType = constraint
     */
    public function doesntEndWith(string ...$suffixes): self
    {
        return $this->applyFluentRule('doesnt_end_with', compact('suffixes'));
    }

    /**
     * @RuleType = constraint
     * TODO add validation type eg. rfc, strict, dns, ...
     */
    public function email(): self
    {
        return $this->applyFluentRule('email');
    }

    /**
     * @RuleType = constraint
     */
    public function encoding(string $type): self
    {
        return $this->applyFluentRule('encoding', compact('type'));
    }

    /**
     * @RuleType = constraint
     */
    public function endsWith(string ...$suffixes): self
    {
        return $this->applyFluentRule('ends_with', compact('suffixes'));
    }

    /**
     * @RuleType = modifier
     */
    public function exclude(): self
    {
        return $this->applyFluentRule('exclude');
    }

    /**
     * @RuleType = modifier
     */
    public function excludeIf(string $field, mixed ...$values): self
    {
        return $this->applyFluentRule('exclude_if', compact('field', 'values'));
    }

    /**
     * @RuleType = modifier
     */
    public function excludeUnless(string $field, mixed ...$values): self
    {
        return $this->applyFluentRule('exclude_unless', compact('field', 'values'));
    }

    /**
     * @RuleType = modifier
     */
    public function excludeWith(string $field): self
    {
        return $this->applyFluentRule('exclude_with', compact('field'));
    }

    /**
     * @RuleType = modifier
     */
    public function excludeWithout(string $field): self
    {
        return $this->applyFluentRule('exclude_without', compact('field'));
    }

    /**
     * @RuleType = constraint
     */
    public function exists(string $table, string $column): self
    {
        return $this->applyFluentRule('exists', compact('table', 'column'));
    }

    /**
     * @RuleType = constraint
     */
    public function extensions(string ...$extensions): self
    {
        return $this->applyFluentRule('extensions', compact('extensions'));
    }

    /**
     * @RuleType = type
     */
    public function file(): self
    {
        return $this->applyFluentRule('file');
    }

    /**
     * @RuleType = constraint
     */
    public function filled(): self
    {
        return $this->applyFluentRule('filled');
    }

    /**
     * @RuleType = constraint
     */
    public function gt(string $field): self
    {
        return $this->applyFluentRule('gt', compact('field'));
    }

    /**
     * @RuleType = constraint
     */
    public function gte(string $field): self
    {
        return $this->applyFluentRule('gte', compact('field'));
    }

    /**
     * @RuleType = constraint
     */
    public function hexColor(): self
    {
        return $this->applyFluentRule('hex_color');
    }

    /**
     * @RuleType = constraint
     */
    public function in(mixed ...$values): self
    {
        return $this->applyFluentRule('in', compact('values'));
    }

    /**
     * @RuleType = constraint
     */
    public function inArray(string $field): self
    {
        return $this->applyFluentRule('in_array', compact('field'));
    }

    /**
     * @RuleType = constraint
     */
    public function inArrayKeys(string ...$keys): self
    {
        return $this->applyFluentRule('in_array_keys', compact('keys'));
    }

    /**
     * @RuleType = constraint
     */
    public function image(): self
    {
        return $this->applyFluentRule('image');
    }

    /**
     * @RuleType = type
     * TODO add strict option
     */
    public function integer(): self
    {
        return $this->applyFluentRule('integer');
    }

    /**
     * @RuleType = constraint
     */
    public function ip(): self
    {
        return $this->applyFluentRule('ip');
    }

    /**
     * @RuleType = constraint
     */
    public function ipv4(): self
    {
        return $this->applyFluentRule('ipv4');
    }

    /**
     * @RuleType = constraint
     */
    public function ipv6(): self
    {
        return $this->applyFluentRule('ipv6');
    }

    /**
     * @RuleType = constraint
     */
    public function json(): self
    {
        return $this->applyFluentRule('json');
    }

    /**
     * @RuleType = constraint
     */
    public function list(): self
    {
        return $this->applyFluentRule('list');
    }

    /**
     * @RuleType = constraint
     */
    public function lowercase(): self
    {
        return $this->applyFluentRule('lowercase');
    }

    /**
     * @RuleType = constraint
     */
    public function lt(string $field): self
    {
        return $this->applyFluentRule('lt', compact('field'));
    }

    /**
     * @RuleType = constraint
     */
    public function lte(string $field): self
    {
        return $this->applyFluentRule('lte', compact('field'));
    }

    /**
     * @RuleType = constraint
     */
    public function macAddress(): self
    {
        return $this->applyFluentRule('mac_address');
    }

    /**
     * @RuleType = constraint
     */
    public function max(int $value): self
    {
        return $this->applyFluentRule('max', compact('value'));
    }

    /**
     * @RuleType = constraint
     */
    public function maxDigits(int $value): self
    {
        return $this->applyFluentRule('max_digits', compact('value'));
    }

    /**
     * @RuleType = constraint
     */
    public function mimes(string ...$extensions): self
    {
        return $this->applyFluentRule('mimes', compact('extensions'));
    }

    /**
     * @RuleType = constraint
     */
    public function mimetypes(string ...$types): self
    {
        return $this->applyFluentRule('mimetypes', compact('types'));
    }

    /**
     * @RuleType = constraint
     */
    public function min(int $value): self
    {
        return $this->applyFluentRule('min', compact('value'));
    }

    /**
     * @RuleType = constraint
     */
    public function minDigits(int $value): self
    {
        return $this->applyFluentRule('min_digits', compact('value'));
    }

    /**
     * @RuleType = presence
     */
    public function missing(): self
    {
        return $this->applyFluentRule('missing');
    }

    /**
     * @RuleType = presence
     */
    public function missingIf(string $field, mixed ...$values): self
    {
        return $this->applyFluentRule('missing_if', compact('field', 'values'));
    }

    /**
     * @RuleType = presence
     */
    public function missingUnless(string $field, mixed ...$values): self
    {
        return $this->applyFluentRule('missing_unless', compact('field', 'values'));
    }

    /**
     * @RuleType = presence
     */
    public function missingWith(string ...$fields): self
    {
        return $this->applyFluentRule('missing_with', compact('fields'));
    }

    /**
     * @RuleType = presence
     */
    public function missingWithAll(string ...$fields): self
    {
        return $this->applyFluentRule('missing_with_all', compact('fields'));
    }

    /**
     * @RuleType = constraint
     */
    public function multipleOf(int $value): self
    {
        return $this->applyFluentRule('multiple_of', compact('value'));
    }

    /**
     * @RuleType = constraint
     */
    public function notIn(mixed ...$values): self
    {
        return $this->applyFluentRule('not_in', compact('values'));
    }

    /**
     * @RuleType = constraint
     */
    public function notRegex(string $pattern): self
    {
        return $this->applyFluentRule('not_regex', compact('pattern'));
    }

    /**
     * @RuleType = modiefier
     */
    public function nullable(): self
    {
        return $this->applyFluentRule('nullable');
    }

    /**
     * @RuleType = type
     */
    public function numeric(): self
    {
        return $this->applyFluentRule('numeric');
    }

    /**
     * @RuleType = presence
     */
    public function present(): self
    {
        return $this->applyFluentRule('present');
    }

    /**
     * @RuleType = presence
     */
    public function presentIf(string $field, mixed ...$values): self
    {
        return $this->applyFluentRule('present_if', compact('field', 'values'));
    }

    /**
     * @RuleType = presence
     */
    public function presentUnless(string $field, mixed ...$values): self
    {
        return $this->applyFluentRule('present_unless', compact('field', 'values'));
    }

    /**
     * @RuleType = presence
     */
    public function presentWith(string ...$fields): self
    {
        return $this->applyFluentRule('present_with', compact('fields'));
    }

    /**
     * @RuleType = presence
     */
    public function presentWithAll(string ...$fields): self
    {
        return $this->applyFluentRule('present_with_all', compact('fields'));
    }

    /**
     * @RuleType = presence
     */
    public function prohibited(): self
    {
        return $this->applyFluentRule('prohibited');
    }

    /**
     * @RuleType = presence
     */
    public function prohibitedIf(string $field, mixed ...$values): self
    {
        return $this->applyFluentRule('prohibited_if', compact('field', 'values'));
    }

    /**
     * @RuleType = presence
     */
    public function prohibitedIfAccepted(string...$fields): self
    {
        return $this->applyFluentRule('prohibited_if_accepted', compact('fields'));
    }

    /**
     * @RuleType = presence
     */
    public function prohibitedIfDeclined(string...$fields): self
    {
        return $this->applyFluentRule('prohibited_if_declined', compact('fields'));
    }

    /**
     * @RuleType = presence
     */
    public function prohibitedUnless(string $field, mixed ...$values): self
    {
        return $this->applyFluentRule('prohibited_unless', compact('field', 'values'));
    }

    /**
     * @RuleType = presence
     */
    public function prohibits(string ...$field): self
    {
        return $this->applyFluentRule('prohibits', compact('field'));
    }

    /**
     * @RuleType = constraint
     */
    public function regex(string $pattern): self
    {
        return $this->applyFluentRule('regex', compact('pattern'));
    }

    /**
     * @RuleType = presence
     */
    public function required(): self
    {
        return $this->applyFluentRule('required');
    }

    /**
     * @RuleType = presence
     */
    public function requiredArrayKeys(string ...$keys): self
    {
        return $this->applyFluentRule('required_array_keys', compact('keys'));
    }

    /**
     * @RuleType = presence
     */
    public function requiredIf(string $field, mixed ...$values): self
    {
        return $this->applyFluentRule('required_if', compact('field', 'values'));
    }

    /**
     * @RuleType = presence
     */
    public function requiredIfAccepted(string ...$fields): self
    {
        return $this->applyFluentRule('required_if_accepted', compact('fields'));
    }

    /**
     * @RuleType = presence
     */
    public function requiredIfDeclined(string $fields): self
    {
        return $this->applyFluentRule('required_if_declined', compact('fields'));
    }

    /**
     * @RuleType = presence
     */
    public function requiredUnless(string $field, mixed ...$values): self
    {
        return $this->applyFluentRule('required_unless', compact('field', 'values'));
    }

    /**
     * @RuleType = presence
     */
    public function requiredWith(string ...$fields): self
    {
        return $this->applyFluentRule('required_with', compact('fields'));
    }

    /**
     * @RuleType = presence
     */
    public function requiredWithAll(string ...$fields): self
    {
        return $this->applyFluentRule('required_with_all', compact('fields'));
    }

    /**
     * @RuleType = presence
     */
    public function requiredWithout(string ...$fields): self
    {
        return $this->applyFluentRule('required_without', compact('fields'));
    }

    /**
     * @RuleType = presence
     */
    public function requiredWithoutAll(string ...$fields): self
    {
        return $this->applyFluentRule('required_without_all', compact('fields'));
    }

    /**
     * @RuleType = constraint
     */
    public function same(string $field): self
    {
        return $this->applyFluentRule('same', compact('field'));
    }

    /**
     * @RuleType = constraint
     */
    public function size(int $value): self
    {
        return $this->applyFluentRule('size', compact('value'));
    }

    /**
     * @RuleType = modifier
     */
    public function sometimes(): self
    {
        return $this->applyFluentRule('sometimes');
    }

    /**
     * @RuleType = constraint
     */
    public function startsWith(string ...$prefixes): self
    {
        return $this->applyFluentRule('starts_with', compact('prefixes'));
    }

    /**
     * @RuleType = type
     */
    public function string(): self
    {
        return $this->applyFluentRule('string');
    }

    /**
     * @RuleType = constraint
     */
    public function timezone(): self
    {
        return $this->applyFluentRule('timezone');
    }

    /**
     * @RuleType = constraint
     */
    public function ulid(): self
    {
        return $this->applyFluentRule('ulid');
    }

    /**
     * @RuleType = constraint
     */
    public function unique(string $table, string $column): self
    {
        return $this->applyFluentRule('unique', compact('table', 'column'));
    }

    /**
     * @RuleType = constraint
     */
    public function uppercase(): self
    {
        return $this->applyFluentRule('uppercase');
    }

    /**
     * @RuleType = constraint
     * TODO add protocols
     */
    public function url(): self
    {
        return $this->applyFluentRule('url');
    }

    /**
     * @RuleType = constraint
     *  TODO add version
     */
    public function uuid(): self
    {
        return $this->applyFluentRule('uuid');
    }
}