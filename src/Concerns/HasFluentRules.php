<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Concerns;

trait HasFluentRules
{
    abstract protected function applyFluentRule(string $ruleName, array $arguments = []): self;

    public function accepted(): self
    {
        return $this->applyFluentRule('accepted');
    }

    public function acceptedIf(string $anotherField, mixed ...$values): self
    {
        return $this->applyFluentRule('accepted_if', compact('anotherField', 'values'));
    }

    public function activeUrl(): self
    {
        return $this->applyFluentRule('active_url');
    }

    public function after(string $date): self
    {
        return $this->applyFluentRule('after', compact('date'));
    }

    public function afterOrEqual(string $date): self
    {
        return $this->applyFluentRule('after_or_equal', compact('date'));
    }

    public function alpha(): self
    {
        return $this->applyFluentRule('alpha');
    }

    public function alphaDash(): self
    {
        return $this->applyFluentRule('alpha_dash');
    }

    public function alphaNum(): self
    {
        return $this->applyFluentRule('alpha_num');
    }

    public function array(): self
    {
        return $this->applyFluentRule('array');
    }

    public function ascii(): self
    {
        return $this->applyFluentRule('ascii');
    }

    public function bail(): self
    {
        return $this->applyFluentRule('bail');
    }

    public function before(string $date): self
    {
        return $this->applyFluentRule('before', compact('date'));
    }

    public function beforeOrEqual(string $date): self
    {
        return $this->applyFluentRule('before_or_equal', compact('date'));
    }

    public function between(int $min, int $max): self
    {
        return $this->applyFluentRule('between', compact('min', 'max'));
    }

    public function boolean(): self
    {
        return $this->applyFluentRule('boolean');
    }

    public function confirmed(): self
    {
        return $this->applyFluentRule('confirmed');
    }

    public function contains(mixed ...$values): self
    {
        return $this->applyFluentRule('contains', compact('values'));
    }

    public function currentPassword(): self
    {
        return $this->applyFluentRule('current_password');
    }

    public function date(): self
    {
        return $this->applyFluentRule('date');
    }

    public function dateEquals(string $date): self
    {
        return $this->applyFluentRule('date_equals', compact('date'));
    }

    public function dateFormat(string $format): self
    {
        return $this->applyFluentRule('date_format', compact('format'));
    }

    public function decimal(int $min, int $max): self
    {
        return $this->applyFluentRule('decimal', compact('min', 'max'));
    }

    public function declined(): self
    {
        return $this->applyFluentRule('declined');
    }

    public function declinedIf(string $anotherField, mixed ...$values): self
    {
        return $this->applyFluentRule('declined_if', compact('anotherField', 'values'));
    }

    public function different(string $field): self
    {
        return $this->applyFluentRule('different', compact('field'));
    }

    public function digits(int $value): self
    {
        return $this->applyFluentRule('digits', compact('value'));
    }

    public function digitsBetween(int $min, int $max): self
    {
        return $this->applyFluentRule('digits_between', compact('min', 'max'));
    }

    public function dimensions(array $constraints): self
    {
        return $this->applyFluentRule('dimensions', compact('constraints'));
    }

    public function distinct(): self
    {
        return $this->applyFluentRule('distinct');
    }

    public function doesntContain(mixed ...$values): self
    {
        return $this->applyFluentRule('doesnt_contain', compact('values'));
    }

    public function doesntStartWith(string ...$prefixes): self
    {
        return $this->applyFluentRule('doesnt_start_with', compact('prefixes'));
    }

    public function email(): self
    {
        return $this->applyFluentRule('email');
    }

    public function endsWith(string ...$suffixes): self
    {
        return $this->applyFluentRule('ends_with', compact('suffixes'));
    }

    public function enum(string $className): self
    {
        return $this->applyFluentRule('enum', compact('className'));
    }

    public function exclude(): self
    {
        return $this->applyFluentRule('exclude');
    }

    public function excludeIf(string $anotherField, mixed ...$values): self
    {
        return $this->applyFluentRule('exclude_if', compact('anotherField', 'values'));
    }

    public function excludeUnless(string $anotherField, mixed ...$values): self
    {
        return $this->applyFluentRule('exclude_unless', compact('anotherField', 'values'));
    }

    public function excludeWith(string $field): self
    {
        return $this->applyFluentRule('exclude_with', compact('field'));
    }

    public function excludeWithout(string $field): self
    {
        return $this->applyFluentRule('exclude_without', compact('field'));
    }

    public function exists(string $table, string $column): self
    {
        return $this->applyFluentRule('exists', compact('table', 'column'));
    }

    public function extensions(string ...$extensions): self
    {
        return $this->applyFluentRule('extensions', compact('extensions'));
    }

    public function file(): self
    {
        return $this->applyFluentRule('file');
    }

    public function filled(): self
    {
        return $this->applyFluentRule('filled');
    }

    public function gt(string $field): self
    {
        return $this->applyFluentRule('gt', compact('field'));
    }

    public function gte(string $field): self
    {
        return $this->applyFluentRule('gte', compact('field'));
    }

    public function hexColor(): self
    {
        return $this->applyFluentRule('hex_color');
    }

    public function in(mixed ...$values): self
    {
        return $this->applyFluentRule('in', compact('values'));
    }

    public function inArray(string $anotherField): self
    {
        return $this->applyFluentRule('in_array', compact('anotherField'));
    }

    public function image(): self
    {
        return $this->applyFluentRule('image');
    }

    public function integer(): self
    {
        return $this->applyFluentRule('integer');
    }

    public function ip(): self
    {
        return $this->applyFluentRule('ip');
    }

    public function ipv4(): self
    {
        return $this->applyFluentRule('ipv4');
    }

    public function ipv6(): self
    {
        return $this->applyFluentRule('ipv6');
    }

    public function json(): self
    {
        return $this->applyFluentRule('json');
    }

    public function list(): self
    {
        return $this->applyFluentRule('list');
    }

    public function lowercase(): self
    {
        return $this->applyFluentRule('lowercase');
    }

    public function lt(string $field): self
    {
        return $this->applyFluentRule('lt', compact('field'));
    }

    public function lte(string $field): self
    {
        return $this->applyFluentRule('lte', compact('field'));
    }

    public function macAddress(): self
    {
        return $this->applyFluentRule('mac_address');
    }

    public function max(int $value): self
    {
        return $this->applyFluentRule('max', compact('value'));
    }

    public function maxDigits(int $value): self
    {
        return $this->applyFluentRule('max_digits', compact('value'));
    }

    public function mimes(string ...$extensions): self
    {
        return $this->applyFluentRule('mimes', compact('extensions'));
    }

    public function mimetypes(string ...$types): self
    {
        return $this->applyFluentRule('mimetypes', compact('types'));
    }

    public function min(int $value): self
    {
        return $this->applyFluentRule('min', compact('value'));
    }

    public function minDigits(int $value): self
    {
        return $this->applyFluentRule('min_digits', compact('value'));
    }

    public function missing(): self
    {
        return $this->applyFluentRule('missing');
    }

    public function missingIf(string $anotherField, mixed ...$values): self
    {
        return $this->applyFluentRule('missing_if', compact('anotherField', 'values'));
    }

    public function missingUnless(string $anotherField, mixed ...$values): self
    {
        return $this->applyFluentRule('missing_unless', compact('anotherField', 'values'));
    }

    public function missingWith(string ...$fields): self
    {
        return $this->applyFluentRule('missing_with', compact('fields'));
    }

    public function missingWithAll(string ...$fields): self
    {
        return $this->applyFluentRule('missing_with_all', compact('fields'));
    }

    public function multipleOf(int $value): self
    {
        return $this->applyFluentRule('multiple_of', compact('value'));
    }

    public function notIn(mixed ...$values): self
    {
        return $this->applyFluentRule('not_in', compact('values'));
    }

    public function notRegex(string $pattern): self
    {
        return $this->applyFluentRule('not_regex', compact('pattern'));
    }

    public function nullable(): self
    {
        return $this->applyFluentRule('nullable');
    }

    public function numeric(): self
    {
        return $this->applyFluentRule('numeric');
    }

    public function object(): self
    {
        return $this->applyFluentRule('object');
    }

    public function present(): self
    {
        return $this->applyFluentRule('present');
    }

    public function presentIf(string $anotherField, mixed ...$values): self
    {
        return $this->applyFluentRule('present_if', compact('anotherField', 'values'));
    }

    public function presentUnless(string $anotherField, mixed ...$values): self
    {
        return $this->applyFluentRule('present_unless', compact('anotherField', 'values'));
    }

    public function presentWith(string ...$fields): self
    {
        return $this->applyFluentRule('present_with', compact('fields'));
    }

    public function presentWithAll(string ...$fields): self
    {
        return $this->applyFluentRule('present_with_all', compact('fields'));
    }

    public function prohibited(): self
    {
        return $this->applyFluentRule('prohibited');
    }

    public function prohibitedIf(string $anotherField, mixed ...$values): self
    {
        return $this->applyFluentRule('prohibited_if', compact('anotherField', 'values'));
    }

    public function prohibitedUnless(string $anotherField, mixed ...$values): self
    {
        return $this->applyFluentRule('prohibited_unless', compact('anotherField', 'values'));
    }

    public function prohibits(string ...$fields): self
    {
        return $this->applyFluentRule('prohibits', compact('fields'));
    }

    public function regex(string $pattern): self
    {
        return $this->applyFluentRule('regex', compact('pattern'));
    }

    public function required(): self
    {
        return $this->applyFluentRule('required');
    }

    public function requiredArrayKeys(string ...$keys): self
    {
        return $this->applyFluentRule('required_array_keys', compact('keys'));
    }

    public function requiredIf(string $anotherField, mixed ...$values): self
    {
        return $this->applyFluentRule('required_if', compact('anotherField', 'values'));
    }

    public function requiredIfAccepted(string $anotherField): self
    {
        return $this->applyFluentRule('required_if_accepted', compact('anotherField'));
    }

    public function requiredUnless(string $anotherField, mixed ...$values): self
    {
        return $this->applyFluentRule('required_unless', compact('anotherField', 'values'));
    }

    public function requiredWith(string ...$fields): self
    {
        return $this->applyFluentRule('required_with', compact('fields'));
    }

    public function requiredWithAll(string ...$fields): self
    {
        return $this->applyFluentRule('required_with_all', compact('fields'));
    }

    public function requiredWithout(string ...$fields): self
    {
        return $this->applyFluentRule('required_without', compact('fields'));
    }

    public function requiredWithoutAll(string ...$fields): self
    {
        return $this->applyFluentRule('required_without_all', compact('fields'));
    }

    public function same(string $field): self
    {
        return $this->applyFluentRule('same', compact('field'));
    }

    public function size(int $value): self
    {
        return $this->applyFluentRule('size', compact('value'));
    }

    public function sometimes(): self
    {
        return $this->applyFluentRule('sometimes');
    }

    public function string(): self
    {
        return $this->applyFluentRule('string');
    }

    public function timezone(): self
    {
        return $this->applyFluentRule('timezone');
    }

    public function ulid(): self
    {
        return $this->applyFluentRule('ulid');
    }

    public function unique(string $table, string $column): self
    {
        return $this->applyFluentRule('unique', compact('table', 'column'));
    }

    public function uppercase(): self
    {
        return $this->applyFluentRule('uppercase');
    }

    public function url(): self
    {
        return $this->applyFluentRule('url');
    }

    public function uuid(): self
    {
        return $this->applyFluentRule('uuid');
    }
}