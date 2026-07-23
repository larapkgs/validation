<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use LaraPkgs\Validation\Contracts\ValidationRule;
use LaraPkgs\Validation\Rules\RuleParser;

trait HasFluentRules
{
    abstract protected function applyFluentRule(string|ValidationRule $rule, array $arguments = []): self;

    public function applyRule(object $rule): self
    {
        $parser = App::make(RuleParser::class);

        $rule = $parser->parse($rule)[0];

        return $this->applyFluentRule($rule);
    }

    /**
     * @RuleType = constraint
     */
    public function accepted(): self
    {
        return $this->applyFluentRule('accepted');
    }

    /**
     * @RuleType = constraint
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
     */
    public function after(string $date): self
    {
        $arguments = !strtotime($date) ? ['field' => $date] : compact('date');

        return $this->applyFluentRule('after', $arguments);
    }

    /**
     * @RuleType = constraint
     */
    public function afterOrEqual(string $date): self
    {
        $arguments = !strtotime($date) ? ['field' => $date] : compact('date');

        return $this->applyFluentRule('after_or_equal', $arguments);
    }

    /**
     * @RuleType = constraint
     */
    public function alpha(bool $ascii = false): self
    {
        $rule = $ascii ? 'alpha:ascii' : 'alpha';

        return $this->applyFluentRule($rule);
    }

    /**
     * @RuleType = constraint
     */
    public function alphaDash(bool $ascii = false): self
    {
        $rule = $ascii ? 'alpha_dash:ascii' : 'alpha_dash';

        return $this->applyFluentRule($rule);
    }

    /**
     * @RuleType = constraint
     */
    public function alphaNum(bool $ascii = false): self
    {
        $rule = $ascii ? 'alpha_num:ascii' : 'alpha_num';

        return $this->applyFluentRule($rule);
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
     */
    public function before(string $date): self
    {
        $arguments = !strtotime($date) ? ['field' => $date] : compact('date');

        return $this->applyFluentRule('before', $arguments);
    }

    /**
     * @RuleType = constraint
     */
    public function beforeOrEqual(string $date): self
    {
        $arguments = !strtotime($date) ? ['field' => $date] : compact('date');

        return $this->applyFluentRule('before_or_equal', $arguments);
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
     */
    public function boolean(bool $strict = false): self
    {
        $rule = $strict ? 'boolean:strict' : 'boolean';

        return $this->applyFluentRule($rule);
    }

    /**
     * @RuleType = constraint
     */
    public function confirmed(?string $field = null): self
    {
        $arguments = $field !== null ? ['field' => $field] : [];

        return $this->applyFluentRule('confirmed', $arguments);
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
     */
    public function decimal(int $min, ?int $max = null): self
    {
        $arguments = $max !== null ? compact('min', 'max') : compact('min');

        return $this->applyFluentRule('decimal', $arguments);
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
     */
    public function distinct(bool $strict = false, bool $ignoreCase = false): self
    {
        $arguments = [];
        if($strict) { $arguments[] = 'strict'; }
        if($ignoreCase) { $arguments[] = 'ignore_case'; }

        $rule = !empty($arguments)
            ? 'distinct:' . implode(',', $arguments)
            : 'distinct';

        return $this->applyFluentRule($rule);
    }

    /**
     * @param array<array-key, string|int> $constraints
     * @RuleType = constraint
     */
    public function dimensions(array $constraints): self
    {
        $constraints = Collection::make($constraints)
            ->map(fn($value, $key) => !is_int($key) ? $key . '=' . $value : $value)
            ->values()->all();

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
     */
    public function email(string ...$validators): self
    {
        return $this->applyFluentRule('email', compact('validators'));
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
     */
    public function integer(bool $strict = false): self
    {
        $rule = $strict ? 'integer:strict' : 'integer';

        return $this->applyFluentRule($rule);
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
     * @RuleType = modifier
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
    public function prohibitedIfAccepted(string ...$fields): self
    {
        return $this->applyFluentRule('prohibited_if_accepted', compact('fields'));
    }

    /**
     * @RuleType = presence
     */
    public function prohibitedIfDeclined(string ...$fields): self
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
    public function requiredIfDeclined(string ...$fields): self
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
     */
    public function url(string ...$protocols): self
    {
        $arguments = !empty($protocols) ? compact('protocols') : [];

        return $this->applyFluentRule('url', $arguments);
    }

    /**
     * @RuleType = constraint
     */
    public function uuid(?int $version = null): self
    {
        $arguments = $version !== null ? compact('version') : [];

        return $this->applyFluentRule('uuid', $arguments);
    }
}