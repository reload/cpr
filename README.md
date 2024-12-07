
# CprNumber

A class representing Danish civil registration numbers (CPR
numbers).

The CPR number is a ten digit number with the format DDMMYY-SSSS, where
the first six digits represent the date of birth and the last four digits
are a sequence number.

The CPR number is used in Denmark to uniquely identify persons in
various systems, and is also used as a personal identification number
in many contexts.

The class represents a CPR number as a read-only value object, and
provides methods for working with, formatting, and validating CPR
numbers.

* Full name: `\Reload\Cpr\CprNumber`

## Methods

### __construct

Construct a CPR number readonly value object from a string.

```php
public __construct(string $cpr)
```

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$cpr` | **string** | A string with the CPR number. |

**Throws:**
<p>If the CPR number does not
contain 10 digits</p>

- [`InvalidCprNumberFormat`](./Exception/InvalidCprNumberFormat.md)
<p>If the date in the CPR number doesn't
exist.</p>

- [`NonExistingDate`](./Exception/NonExistingDate.md)

### __toString

Format the CPR number in the traditional format (120345-6789).

```php
public __toString(): string
```

**See Also:**

* \Reload\Cpr\CprNumber::formatPretty() - 

### formatPretty

Format the CPR number in the traditional format (120345-6789).

```php
public formatPretty(): string
```

### formatNumbersOnly

Format the CPR number using numbers only (1203456789).

```php
public formatNumbersOnly(): string
```

### isFemale

Check if the CPR number represents a female person.

```php
public isFemale(): bool
```

### isMale

Check if the CPR number represents a male person.

```php
public isMale(): bool
```

### getDateTimeImmutable

Get a DateTimeImmutable object from the CPR number.

```php
public getDateTimeImmutable(?\DateTimeZone $timezone = null): ?\DateTimeImmutable
```

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$timezone` | **?\DateTimeZone** | <br />A DateTimeZone object representing the desired time zone.<br /><br />If timezone is omitted or null the current timezone will be<br />used. |

### validateModulus11

Validate the CPR number using the modulus 11 algorithm.

```php
public validateModulus11(): bool
```

NOTICE: CPR numbers are no longer required to fulfill the modulus
11 check. You should NOT use this method to validate or dismiss
CPR numbers.

